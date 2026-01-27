<?php

namespace App\Helpers;

class Fonnte
{
    public static function send($target, $message, $url = null)
    {
        // Normalisasi Nomor HP
        // Hapus karakter selain angka
        $target = preg_replace('/[^0-9]/', '', $target);

        // Ubah 08... jadi 628...
        if (substr($target, 0, 2) === '08') {
            $target = '62' . substr($target, 1);
        }
        // Ubah 8... jadi 628... (jika user input tanpa 0 atau 62)
        elseif (substr($target, 0, 1) === '8') {
            $target = '62' . $target;
        }
        // Jika sudah 628... biarkan saja.

        $token = env("FONNTE_API_KEY");

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
}
