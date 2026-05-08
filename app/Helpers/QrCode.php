<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode as FacadesQrCode;

class QrCode
{
    public static function generate($data, $size = 200, $format = "png", $imgPath = null)
    {
        $qrCode = FacadesQrCode::format($format)->size($size);
        if ($imgPath) {
            $qrCode->merge(public_path("img/logo_ABBI.png"))->errorCorrection("H")->margin(1);
        }
        return $qrCode->generate($data);
    }

    public static function generateAndSave($data, $size = 200, $format = "png", $path = "qrcode", $imgPath = null)
    {
        $qrCode = self::generate($data, $size, $format, $imgPath);
        Storage::disk("public")->put($path, $qrCode);
        return $path;
    }
}
