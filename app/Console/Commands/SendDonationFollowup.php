<?php

namespace App\Console\Commands;

use App\Helpers\Fonnte;
use App\Models\Donation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendDonationFollowup extends Command
{
    protected $signature = "donations:send-followup {--dry-run : Preview donatur yang akan dikirimi tanpa mengirim WA}";
    protected $description = "Kirim reminder WA ke donatur yang sudah X hari tidak donasi";

    public function handle()
    {
        $days = (int) env('DONATION_FOLLOWUP_DAYS', 3);
        $now = now();
        $isDryRun = $this->option('dry-run');

        // Query donatur yang:
        // 1. Punya donasi success
        // 2. Donasi terakhir > X hari lalu
        // 3. Followup terakhir > X hari lalu (atau belum pernah)
        $cutoffDate = $now->copy()->subDays($days)->format('Y-m-d H:i:s');
        
        $donors = DB::table('donations')
            ->select(
                'donor_phone',
                DB::raw('MAX(donor_name) as donor_name'),
                DB::raw('MAX(created_at) as last_donation_at'),
                DB::raw('MAX(followup_sent_at) as last_followup_at')
            )
            ->where('status', 'success')
            ->whereNotNull('donor_phone')
            ->whereNull('deleted_at')
            ->groupBy('donor_phone')
            ->havingRaw("MAX(created_at) < ?", [$cutoffDate])
            ->havingRaw("(MAX(followup_sent_at) IS NULL OR MAX(followup_sent_at) < ?)", [$cutoffDate])
            ->get();

        if ($donors->isEmpty()) {
            $this->info("[" . now()->format('Y-m-d H:i') . "] Followup - No donors to follow up");
            return 0;
        }

        if ($isDryRun) {
            $this->info("=== DRY RUN MODE ===");
            $this->info("Jumlah hari tanpa donasi: {$days} hari");
            $this->info("Donatur yang akan dikirimi:");
            $this->newLine();

            foreach ($donors as $donor) {
                $donorName = $donor->donor_name ?: 'Sahabat Baikku';
                $this->line("📱 {$donor->donor_phone} - {$donorName}");
                $this->line("   Donasi terakhir: {$donor->last_donation_at}");
                $this->line("   Followup terakhir: " . ($donor->last_followup_at ?? 'Belum pernah'));
                $this->newLine();
            }

            $this->info("Total: {$donors->count()} donatur");
            return 0;
        }

        $sent = 0;
        $failed = 0;

        foreach ($donors as $donor) {
            $phone = $donor->donor_phone;
            $donorName = $donor->donor_name ?: 'Sahabat Baikku';

            $message = "Assalamu’alaikum warahmatullahi wabarakatuh 🙏

Halo Kak {$donorName}, terima kasih banyak atas kebaikan kakak yang sebelumnya sudah berdonasi melalui AyoBuatBaik. Dukungan kakak sangat berarti dan telah membantu menghadirkan manfaat nyata bagi mereka yang membutuhkan.

Kami ingin mengajak kakak kembali melanjutkan kebaikan ini. Setiap donasi, sekecil apa pun, insyaAllah menjadi harapan besar bagi para penerima manfaat dan bisa menjadi amal jariyah untuk kakak.

Bila berkenan, kakak bisa berdonasi kembali melalui tautan berikut:
🔗 https://ayobuatbaik.com

Semoga Allah membalas setiap kebaikan kakak dengan keberkahan, kesehatan, dan rezeki yang berlimpah.
Jazakallahu khairan katsiran 🤲

Salam hangat,
Tim AyoBuatBaik";

            try {
                Fonnte::send($phone, $message);

                // Update followup_sent_at di SEMUA donasi dengan nomor HP ini
                Donation::where('donor_phone', $donor->donor_phone)
                    ->update(['followup_sent_at' => now()]);

                $sent++;
                $this->info("✅ Sent to: {$donor->donor_phone} - {$donor->donor_name}");
            } catch (\Exception $e) {
                Log::error("Followup failed for: {$donor->donor_phone}", ['error' => $e->getMessage()]);
                $failed++;
                $this->error("❌ Failed: {$donor->donor_phone}");
            }

            // Rate limiting - tunggu 1 detik antar pesan
            sleep(1);
        }

        $this->info("[" . now()->format('Y-m-d H:i') . "] Followup - Sent: {$sent}, Failed: {$failed}");

        return 0;
    }
}
