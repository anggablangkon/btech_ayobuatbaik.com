<?php

namespace App\Console\Commands;

use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ReconcileMidtransReport extends Command
{
    protected $signature = 'donations:reconcile-midtrans-report
                            {path : Path ke file CSV Midtrans payout/settlement report}
                            {--delimiter= : Paksa delimiter tertentu, mis. ; atau ,}';

    protected $description = 'Import Midtrans payout/settlement report untuk mengisi fee dan net amount transaksi non-manual.';

    public function handle(): int
    {
        $path = $this->resolvePath($this->argument('path'));

        if (!is_file($path)) {
            $this->error("File report tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->error("Gagal membuka file report: {$path}");

            return self::FAILURE;
        }

        $delimiter = $this->option('delimiter') ?: $this->detectDelimiter($path);
        $header = fgetcsv($handle, 0, $delimiter);

        if ($header === false) {
            fclose($handle);
            $this->error('File report kosong atau header tidak bisa dibaca.');

            return self::FAILURE;
        }

        $normalizedHeader = array_map(fn ($value) => $this->normalizeHeader((string) $value), $header);

        $stats = [
            'processed' => 0,
            'updated' => 0,
            'manual_skipped' => 0,
            'not_found' => 0,
            'invalid' => 0,
        ];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $stats['processed']++;

            $record = $this->combineRow($normalizedHeader, $row);
            $transactionType = strtolower((string) ($this->firstValue($record, [
                'transaction_type',
                'type',
            ]) ?? ''));
            $transactionStatus = strtolower((string) ($this->firstValue($record, [
                'status',
                'transaction_status',
            ]) ?? ''));

            if ($transactionType !== '' && $transactionType !== 'payment') {
                continue;
            }

            if ($transactionStatus !== '' && !in_array($transactionStatus, ['settlement', 'capture'], true)) {
                continue;
            }

            $orderId = $this->firstValue($record, [
                'order_id',
                'merchant_order_id',
                'transaction_id',
                'merchant_reference',
            ]);

            if (blank($orderId)) {
                $stats['invalid']++;
                continue;
            }

            /** @var Donation|null $donation */
            $donation = Donation::query()->where('donation_code', trim((string) $orderId))->first();

            if (!$donation) {
                $stats['not_found']++;
                continue;
            }

            if ($donation->isManualDonation()) {
                $stats['manual_skipped']++;
                continue;
            }

            $grossAmount = $this->parseMoneyValue($this->firstValue($record, [
                'gross_amount',
                'gross',
                'transaction_amount',
                'amount',
            ])) ?? $donation->gross_amount ?? $donation->amount;

            $netAmount = $this->parseMoneyValue($this->firstValue($record, [
                'amount_net',
                'amount_nett',
                'net_amount',
                'payable_nett',
                'payable_net',
                'amount_net_idr',
            ]));

            $explicitTotalFee = $this->parseMoneyValue($this->firstValue($record, [
                'total_fee',
                'fee_amount',
                'fee',
            ]));

            $mdrFee = $this->parseMoneyValue($this->firstValue($record, [
                'mdr_fee',
            ]));

            $transactionFee = $this->parseMoneyValue($this->firstValue($record, [
                'transaction_fee',
            ]));

            $vatFee = $this->parseMoneyValue($this->firstValue($record, [
                'vat',
                'vat_fee',
                'vat_on_fee',
            ]));

            $feeAmount = $this->normalizeFeeAmount($explicitTotalFee);

            if ($feeAmount === null) {
                $feeParts = array_filter([$mdrFee, $transactionFee, $vatFee], fn ($value) => $value !== null);

                if ($feeParts !== []) {
                    $feeAmount = array_sum($feeParts);
                } elseif ($netAmount !== null && $grossAmount !== null && $grossAmount >= $netAmount) {
                    $feeAmount = $grossAmount - $netAmount;
                }
            }

            if ($netAmount === null && $grossAmount !== null && $feeAmount !== null) {
                $netAmount = max(0, $grossAmount - $feeAmount);
            }

            $updates = [
                'gross_amount' => $grossAmount,
                'net_amount_source' => 'midtrans_report',
            ];

            $paymentType = $this->normalizePaymentType($this->firstValue($record, [
                'payment_type',
                'payment_method',
                'channel',
            ]));

            if (blank($donation->payment_type) && $paymentType !== null) {
                $updates['payment_type'] = $paymentType;
            }

            if ($feeAmount !== null) {
                $updates['midtrans_fee_amount'] = $feeAmount;
            }

            if ($netAmount !== null) {
                $updates['net_amount'] = $netAmount;
            }

            $settlementTime = $this->parseDateValue($this->firstValue($record, [
                'settlement_time',
                'settlement_date',
                'paid_time',
                'transaction_time',
            ]));

            if ($settlementTime !== null) {
                $updates['settlement_time'] = $settlementTime;
            }

            $donation->update($updates);
            $stats['updated']++;
        }

        fclose($handle);

        $this->info('Reconciliation selesai.');
        $this->table(
            ['Processed', 'Updated', 'Manual Skipped', 'Not Found', 'Invalid'],
            [[
                $stats['processed'],
                $stats['updated'],
                $stats['manual_skipped'],
                $stats['not_found'],
                $stats['invalid'],
            ]]
        );

        return self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        if (is_file($path)) {
            return $path;
        }

        return base_path($path);
    }

    private function detectDelimiter(string $path): string
    {
        $sample = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)[0] ?? '';
        $delimiters = [';', ',', "\t"];
        $scores = [];

        foreach ($delimiters as $delimiter) {
            $scores[$delimiter] = count(str_getcsv($sample, $delimiter));
        }

        arsort($scores);

        return (string) array_key_first($scores);
    }

    private function normalizeHeader(string $value): string
    {
        $normalized = Str::of($value)
            ->lower()
            ->replaceMatches('/[^\pL\pN]+/u', '_')
            ->trim('_')
            ->toString();

        return match ($normalized) {
            'amount_net', 'amount_nett', 'amount_net_' => 'amount_net',
            'orderid' => 'order_id',
            default => $normalized,
        };
    }

    private function combineRow(array $header, array $row): array
    {
        $record = [];

        foreach ($header as $index => $column) {
            if ($column === '') {
                continue;
            }

            $record[$column] = $row[$index] ?? null;
        }

        return $record;
    }

    private function firstValue(array $record, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $record) && $record[$key] !== null && $record[$key] !== '') {
                return $record[$key];
            }
        }

        return null;
    }

    private function parseMoneyValue(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        $normalized = preg_replace('/[^\d-]/', '', (string) $value);

        return $normalized === '' ? null : (int) $normalized;
    }

    private function normalizeFeeAmount(?int $value): ?int
    {
        if ($value === null) {
            return null;
        }

        return abs($value);
    }

    private function normalizePaymentType(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = Str::of((string) $value)
            ->lower()
            ->replace(['&', '/', '-'], ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();

        return match ($normalized) {
            'qris' => 'qris',
            'mandiri bill', 'mandiri bill payment', 'echannel' => 'echannel',
            'bank transfer', 'bca va', 'bni va', 'bri va', 'permata va' => 'bank_transfer',
            'gopay' => 'gopay',
            'shopeepay' => 'shopeepay',
            'indomaret', 'alfamart' => 'cstore',
            default => Str::of($normalized)->replace(' ', '_')->toString(),
        };
    }

    private function parseDateValue(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
