<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display settings form.
     */
    public function index()
    {
        $settings = SiteSetting::allCached();
        return view('pages.admin.settings.index', compact('settings'));
    }

    /**
     * Update all settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name_highlight' => 'required|string|max:50',
            'site_name_rest' => 'required|string|max:100',
            'site_title' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_url' => 'nullable|url|max:255',
            'meta_pixel_id' => 'nullable|string|max:50',
            'whatsapp_number' => 'nullable|string|max:20',
            'whatsapp_message' => 'nullable|string|max:255',
            'footer_description' => 'nullable|string|max:500',
            'footer_copyright' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
        ]);

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $logo = $request->file('site_logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('img'), $logoName);
            SiteSetting::set('site_logo', '/img/' . $logoName);
        }

        // Update text settings
        $textSettings = [
            'site_name_highlight',
            'site_name_rest',
            'site_title',
            'site_description',
            'site_url',
            'meta_pixel_id',
            'whatsapp_number',
            'whatsapp_message',
            'footer_description',
            'footer_copyright',
        ];

        foreach ($textSettings as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->input($key));
            }
        }

        // Clear all cache
        SiteSetting::clearCache();

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
