<?php

namespace Database\Seeders;

use App\Helpers\QrCode;
use App\Models\QurbanParticipant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QurbanSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QurbanParticipant::factory()->count(100)->create();
        $qurbanParticipants = QurbanParticipant::all();
        // foreach ($qurbanParticipants as $qurbanParticipant) {
        //     $qrCodePath = QrCode::generateAndSave($qurbanParticipant->coupon_code, 200, "png", "qrcode/{$qurbanParticipant->id}.webp");
        //     $qurbanParticipant->update(['image_qr_path' => $qrCodePath]);
        // }
    }
}
