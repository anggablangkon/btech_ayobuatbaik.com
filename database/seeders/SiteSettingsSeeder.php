<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Brand Name (split for styled display: <span>Ayo</span>buatbaik)
            'site_name_highlight' => 'Ayo',
            'site_name_rest' => 'buatbaik',
            
            // Browser tab title
            'site_title' => 'Ayobuatbaik - Platform Donasi Digital',
            
            // Meta description
            'site_description' => 'Platform donasi digital yang menghubungkan para dermawan dengan berbagai program kemanusiaan.',
            
            // Base URL
            'site_url' => 'https://ayobuatbaik.com',
            
            // Logo path (relative to public)
            'site_logo' => '/img/icon_ABBI.png',
            
            // Facebook Meta Pixel ID
            'meta_pixel_id' => '2777910462416668',
            
            // WhatsApp floating button
            'whatsapp_number' => '6282133337058',
            'whatsapp_message' => 'Assalamualaikum, saya ingin berbuat baik',
            
            // Footer
            'footer_description' => 'Platform donasi digital yang menghubungkan para dermawan dengan berbagai program kemanusiaan.',
            'footer_copyright' => '© 2025 Ayobuatbaik. All rights reserved.',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
