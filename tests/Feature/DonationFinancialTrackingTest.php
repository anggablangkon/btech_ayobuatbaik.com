<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\KategoriDonasi;
use App\Models\PenggalangDana;
use App\Models\ProgramDonasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonationFinancialTrackingTest extends TestCase
{
    use RefreshDatabase;

    private function createProgram(): ProgramDonasi
    {
        $kategori = KategoriDonasi::create([
            'name' => 'Kemanusiaan',
            'slug' => 'kemanusiaan',
            'deskripsi' => 'Bantuan kemanusiaan',
        ]);

        $penggalang = PenggalangDana::create([
            'nama' => 'Yayasan Amal',
            'tipe' => 'yayasan',
            'kontak' => '081234567890',
        ]);

        return ProgramDonasi::create([
            'title' => 'Program Donasi Test',
            'slug' => 'program-donasi-test',
            'kategori_id' => $kategori->id,
            'penggalang_id' => $penggalang->id,
            'target_amount' => 1000000,
            'collected_amount' => 0,
            'start_date' => now()->toDateString(),
            'status' => 'active',
        ]);
    }

    #[Test]
    public function midtrans_notification_menyimpan_metadata_finansial_dan_menandai_reconciliation(): void
    {
        $program = $this->createProgram();

        $donation = Donation::create([
            'donation_code' => 'DON-TEST-001',
            'program_donasi_id' => $program->id,
            'donor_name' => 'Ahmad',
            'donor_phone' => '081234567890',
            'donor_email' => 'ahmad@example.com',
            'donation_type' => 'general',
            'amount' => 100000,
            'gross_amount' => 100000,
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/midtrans/notification', [
            'order_id' => $donation->donation_code,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'gross_amount' => '100000',
            'settlement_time' => '2026-04-15 12:30:00',
        ]);

        $response->assertOk();

        $donation->refresh();
        $program->refresh();

        $this->assertSame('success', $donation->status);
        $this->assertSame('bank_transfer', $donation->payment_type);
        $this->assertSame(100000, $donation->gross_amount);
        $this->assertSame('pending_reconciliation', $donation->net_amount_source);
        $this->assertNotNull($donation->settlement_time);
        $this->assertSame('bank_transfer', $donation->midtrans_payload['payment_type']);
        $this->assertSame(100000, (int) $program->collected_amount);
    }

    #[Test]
    public function midtrans_notification_qris_langsung_menghitung_estimasi_fee(): void
    {
        $program = $this->createProgram();

        $donation = Donation::create([
            'donation_code' => 'DON-TEST-QRIS',
            'program_donasi_id' => $program->id,
            'donor_name' => 'Siti',
            'donor_phone' => '081234567891',
            'donor_email' => 'siti@example.com',
            'donation_type' => 'general',
            'amount' => 10000,
            'gross_amount' => 10000,
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/midtrans/notification', [
            'order_id' => $donation->donation_code,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'gross_amount' => '10000',
            'settlement_time' => '2026-04-15 12:35:00',
        ]);

        $response->assertOk();

        $donation->refresh();

        $this->assertSame('success', $donation->status);
        $this->assertSame('qris', $donation->payment_type);
        $this->assertSame(70, $donation->midtrans_fee_amount);
        $this->assertSame(9930, $donation->net_amount);
        $this->assertSame('estimated_payment_rule', $donation->net_amount_source);
    }

    #[Test]
    public function command_reconciliation_mengisi_net_amount_dan_skip_transaksi_manual(): void
    {
        $program = $this->createProgram();

        $midtransDonation = Donation::create([
            'donation_code' => 'DON-REPORT-001',
            'program_donasi_id' => $program->id,
            'donor_name' => 'Fatimah',
            'donor_phone' => '081111111111',
            'donor_email' => 'fatimah@example.com',
            'donation_type' => 'general',
            'amount' => 100000,
            'gross_amount' => 100000,
            'status' => 'success',
            'net_amount_source' => 'pending_reconciliation',
        ]);

        $manualDonation = Donation::create([
            'donation_code' => 'MANUAL-REPORT-001',
            'program_donasi_id' => $program->id,
            'donor_name' => 'Manual Donor',
            'donor_phone' => '082222222222',
            'donor_email' => 'manual@example.com',
            'donation_type' => 'cash',
            'payment_type' => 'manual_entry',
            'amount' => 50000,
            'gross_amount' => 50000,
            'status' => 'success',
            'net_amount_source' => 'not_applicable_manual',
        ]);

        $reportPath = storage_path('app/testing-midtrans-report.csv');
        file_put_contents(
            $reportPath,
            implode("\n", [
                'order_id;gross_amount;amount_net;mdr_fee;transaction_fee;payment_type;settlement_time',
                'DON-REPORT-001;100000;95560;4000;440;bank_transfer;2026-04-15 13:00:00',
                'MANUAL-REPORT-001;50000;48000;1500;500;qris;2026-04-15 13:10:00',
            ])
        );

        Artisan::call('donations:reconcile-midtrans-report', [
            'path' => $reportPath,
            '--delimiter' => ';',
        ]);

        $midtransDonation->refresh();
        $manualDonation->refresh();

        $this->assertSame(100000, $midtransDonation->gross_amount);
        $this->assertSame(4440, $midtransDonation->midtrans_fee_amount);
        $this->assertSame(95560, $midtransDonation->net_amount);
        $this->assertSame('bank_transfer', $midtransDonation->payment_type);
        $this->assertSame('midtrans_report', $midtransDonation->net_amount_source);

        $this->assertNull($manualDonation->midtrans_fee_amount);
        $this->assertNull($manualDonation->net_amount);
        $this->assertSame('manual_entry', $manualDonation->payment_type);
        $this->assertSame('not_applicable_manual', $manualDonation->net_amount_source);

        @unlink($reportPath);
    }
}
