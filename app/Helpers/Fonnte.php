<?php

namespace App\Helpers;

class Fonnte
{
    /**
     * Send WhatsApp message via Fonnte
     */
    public static function send($target, $message, $url = null)
    {
        // 1. Normalisasi Nomor (Panggil method local)
        $target = self::normalize($target);

        // 2. Ambil token dari Config (Aman dicache)
        $token = config('services.fonnte.token');

        $curl = curl_init();

        $postFields = [
            "target" => $target,
            "message" => $message,
        ];

        if ($url) {
            $postFields['url'] = $url;
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.fonnte.com/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => ["Authorization: $token"],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Hanya log error, tidak log data sensitif
        if ($httpCode !== 200) {
            \Log::error("Fonnte: Gagal mengirim WA", ['http_code' => $httpCode]);
        }

        return $response;
    }

    /**
     * Pusat normalisasi nomor HP +62
     */
    public static function normalize($target)
    {
        $target = trim($target);
        
        // hapus karakter selain angka
        $target = preg_replace('/[^0-9]/', '', $target);

        // ubah 08 jadi 628
        if (substr($target, 0, 2) === '08') {
            return '62' . substr($target, 1);
        }
        // ubah 8 jadi 628
        elseif (substr($target, 0, 1) === '8') {
            return '62' . $target;
        }

        return $target;
    }
}
