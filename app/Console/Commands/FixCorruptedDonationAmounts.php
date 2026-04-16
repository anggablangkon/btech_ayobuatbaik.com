<?php

namespace App\Console\Commands;

use App\Models\Donation;
use App\Support\MidtransFeeEstimator;
use Illuminate\Console\Command;

class FixCorruptedDonationAmounts extends Command
{
    protected $signature = 'donations:fix-corrupted-amounts
                            {--dry-run : Preview changes without saving}';

    protected $description = 'Fix gross_amount corrupted by parseMoneyValue bug ("5000.00" → 500000) and recalculate fee/net.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $estimator = new MidtransFeeEstimator();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE — tidak ada data yang diubah.');
        }

        // Step 1: Fix corrupted gross_amount
        // The bug turned "5000.00" into 500000 (100x the actual value).
        // The `amount` column was never affected (set from user input).
        // Safe fix: reset gross_amount = amount where they differ for non-manual donations.
        $corruptedDonations = Donation::query()
            ->whereColumn('gross_amount', '!=', 'amount')
            ->where('donation_code', 'not like', 'MANUAL-%')
            ->whereNotIn('donation_type', ['manual', 'cash'])
            ->get();

        $stats = [
            'gross_fixed' => 0,
            'fee_recalculated' => 0,
            'fee_pending' => 0,
        ];

        if ($corruptedDonations->isEmpty()) {
            $this->info('✅ Tidak ada gross_amount yang perlu diperbaiki.');
        } else {
            $this->info("Found {$corruptedDonations->count()} donation(s) with mismatched gross_amount.");

            foreach ($corruptedDonations as $donation) {
                $oldGross = $donation->gross_amount;
                $correctGross = $donation->amount;

                $this->line("  [{$donation->donation_code}] gross: {$oldGross} → {$correctGross} (amount: {$correctGross})");

                if (!$dryRun) {
                    $donation->gross_amount = $correctGross;
                    $donation->save();
                }

                $stats['gross_fixed']++;
            }
        }

        // Step 2: Recalculate fee/net for all non-manual, successful donations
        // that have payment_type but no fee/net yet, or had corrupted fee/net
        $donationsToRecalculate = Donation::query()
            ->where('status', 'success')
            ->where('donation_code', 'not like', 'MANUAL-%')
            ->where(function ($query) {
                $query->whereNull('donation_type')
                    ->orWhereNotIn('donation_type', ['manual', 'cash']);
            })
            ->whereNotNull('payment_type')
            ->where('payment_type', '!=', 'manual_entry')
            ->where(function ($query) {
                // Recalculate if: no net yet, or was estimated with potentially wrong data
                $query->whereNull('net_amount')
                    ->orWhere('net_amount_source', 'pending_reconciliation')
                    ->orWhere('net_amount_source', 'estimated_payment_rule');
            })
            // Don't touch reconciled data from official Midtrans report
            ->where(function ($query) {
                $query->whereNull('net_amount_source')
                    ->orWhereNot('net_amount_source', 'midtrans_report');
            })
            ->get();

        $this->info("Found {$donationsToRecalculate->count()} donation(s) to recalculate fee/net.");

        foreach ($donationsToRecalculate as $donation) {
            $grossAmount = (int) ($donation->gross_amount ?? $donation->amount);
            $estimated = $estimator->estimate($donation->payment_type, $grossAmount);

            if ($estimated !== null) {
                $this->line("  [{$donation->donation_code}] {$donation->payment_type}: gross={$grossAmount}, fee={$estimated['midtrans_fee_amount']}, net={$estimated['net_amount']}");

                if (!$dryRun) {
                    $donation->update([
                        'midtrans_fee_amount' => $estimated['midtrans_fee_amount'],
                        'net_amount' => $estimated['net_amount'],
                        'net_amount_source' => $estimated['net_amount_source'],
                    ]);
                }

                $stats['fee_recalculated']++;
            } else {
                $this->warn("  [{$donation->donation_code}] {$donation->payment_type}: no fee rule configured → pending_reconciliation");

                if (!$dryRun) {
                    $donation->update([
                        'net_amount_source' => 'pending_reconciliation',
                    ]);
                }

                $stats['fee_pending']++;
            }
        }

        $this->newLine();
        $this->info('=== Summary ===');
        $this->table(
            ['Action', 'Count'],
            [
                ['Gross amount fixed', $stats['gross_fixed']],
                ['Fee/net recalculated', $stats['fee_recalculated']],
                ['Fee pending (no rule)', $stats['fee_pending']],
            ]
        );

        if ($dryRun) {
            $this->warn('ℹ️  Jalankan tanpa --dry-run untuk apply perubahan.');
        } else {
            $this->info('✅ Done! Semua data sudah diperbaiki.');
        }

        return self::SUCCESS;
    }
}
