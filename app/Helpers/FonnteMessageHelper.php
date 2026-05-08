<?php

namespace App\Helpers;

class FonnteMessageHelper
{
    public static function claimKuponQurbanMessage($quponParticipant)
    {
        $message = "Assalamualaikum Warahmatullahi Wabarakatuh\n";
        $message .= "Yth. Bapak/Ibu " . $quponParticipant->full_name . ",\n";
        $message .= "Silahkan menukarkan kupon Anda di Lokasi " . $quponParticipant->address . "\n\n";
        $message .= "Kode Kupon anda adalah " . $quponParticipant->coupon_code . " atau dengan scan QR yang telah kami sediakan di bawah ini:\n\n";
        $message .= route("qurban.voucher.public", ["coupon_code" => $quponParticipant->coupon_code]) . "\n\n";
        $message .= "Jazakumullahu Khairan Katsiran.\n";
        $message .= "Terima kasih atas kebaikan dan kepercayaan Anda.";
        return $message;
    }
}
