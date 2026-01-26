<?php

namespace App\Console\Commands;

use App\Helpers\Fonnte;
use App\Models\Donation;
use Illuminate\Console\Command;
use Log;

class SendPendingReminders extends Command
{
    protected $signature = "donations:send-reminders {--dry-run : Preview donatur yang akan dikirimi tanpa mengirim WA}";
    protected $description = "Kirim Reminder WA untuk donasi pending";

    public function handle()
    {
        $now = now();

        $pendingDonations = Donation::where("status", "pending")
            ->where("created_at", ">=", $now->copy()->subHours(24))
            ->where("created_at", "<=", $now->copy()->subMinutes(1))
            ->whereNull("reminder_sent_at")
            ->get();

        if ($pendingDonations->isEmpty()) {
            $this->info("[" . now()->format('Y-m-d H:i') . "] Reminders - Sent: 0, Failed: 0 (No pending donations)");
            return 0;
        }

        $sent = 0;
        $failed = 0;

        foreach ($pendingDonations as $donation) {
            $phone = preg_replace("/^0/", "62", $donation->donor_phone);
            $programName = $donation->program->title;
            $amount = number_format($donation->amount, 0, ",", ".");
            $url = route("donation.status", $donation->donation_code);

            $message = "Assalamualaikum Warahmatullahi Wabarakatuh
Yth. Bapak/Ibu Dermawan,
Terima kasih atas niat baik Anda untuk berdonasi {$programName}. Berikut ini adalah instruksi pembayaran donasi Anda:
Mohon lakukan transfer sesuai nominal berikut :
📌 Nominal: Rp {$amount}
Silakan transfer ke rekening yang telah kami sediakan pada link berikut: {$url}
Pembayaran dapat dilakukan maksimal dalam 1x24 jam. Setelah waktu tersebut, sistem akan otomatis membatalkan donasi Anda.
Jazakumullahu Khairan Katsiran.
Terima kasih atas kebaikan dan kepercayaan Anda.";

            try {
                Fonnte::send($phone, $message);
                $donation->update(["reminder_sent_at" => now()]);
                $sent++;
            } catch (\Exception $e) {
                Log::error("Reminder failed: {$donation->donation_code}");
                $failed++;
            }
        }

        $this->info("[" . now()->format('Y-m-d H:i') . "] Reminders - Sent: {$sent}, Failed: {$failed}");

        return 0;
    }
}

