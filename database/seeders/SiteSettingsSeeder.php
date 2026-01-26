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
            'site_name_highlight' => 'Nama',
            'site_name_rest' => 'Brand',
            
            // Browser tab title
            'site_title' => 'Nama Brand - Platform Donasi Digital',
            
            // Meta description
            'site_description' => 'Platform donasi digital yang menghubungkan para dermawan dengan berbagai program kemanusiaan.',
            
            // Base URL
            'site_url' => 'https://example.com',
            
            // Logo path (relative to public)
            'site_logo' => '/img/icon_ABBI.png', // Default icon remains, user can change it
            
            // Facebook Meta Pixel ID
            'meta_pixel_id' => '', // Empty by default
            
            // WhatsApp floating button
            'whatsapp_number' => '628123456789',
            'whatsapp_message' => 'Halo, saya ingin bertanya...',
            
            // Footer
            'footer_description' => 'Platform donasi digital yang menghubungkan para dermawan dengan berbagai program kemanusiaan.',
            'footer_copyright' => '© ' . date('Y') . ' Nama Brand. All rights reserved.',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
