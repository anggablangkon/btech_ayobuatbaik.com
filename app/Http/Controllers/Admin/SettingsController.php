<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            "site_title" => Setting::get("site_title", "Ayobuatbaik"),
            "site_logo" => Setting::get("site_logo"),
            "tagline" => Setting::get("tagline", "Platform Donasi Digital"),
            "whatsapp_number" => Setting::get("whatsapp_number", "6282133337058"),
            "footer_description" => Setting::get(
                "footer_description",
                "Platform donasi digital yang menghubungkan para dermawan dengan berbagai program kemanusiaan.",
            ),
            "copyright_text" => Setting::get("copyright_text", "Ayobuatbaik. All rights reserved."),
            "site_description" => Setting::get("site_description", "Ayobuatbaik - Platform Donasi Digital"),
            "whatsapp_message" => Setting::get("whatsapp_message", "Assalamualaikum ayobuatbaik, saya ingin berbuat baik"),
            "og_image" => Setting::get("og_image", "https://ayobuatbaik.com/img/icon_ABBI.png"),
        ];

        return view("admin.settings.index", compact("settings"));
    }

    public function update(Request $request)
    {
        $request->validate([
            "site_title" => "required|string|max:255",
            "tagline" => "required|string|max:255",
            "whatsapp_number" => "required|string|max:20",
            "footer_description" => "required|string",
            "copyright_text" => "required|string|max:255",
            "site_description" => "required|string|max:255",
            "whatsapp_message" => "required|string",
            "og_image" => "nullable|url",
            "site_logo" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);

        // Handle logo upload
        if ($request->hasFile("site_logo")) {
            $logoPath = $request->file("site_logo")->store("logos", "public");
            Setting::set("site_logo", $logoPath);
        }

        // Update other settings
        Setting::set("site_title", $request->site_title);
        Setting::set("tagline", $request->tagline);
        Setting::set("whatsapp_number", $request->whatsapp_number);
        Setting::set("footer_description", $request->footer_description);
        Setting::set("copyright_text", $request->copyright_text);
        Setting::set("site_description", $request->site_description);
        Setting::set("whatsapp_message", $request->whatsapp_message);
        Setting::set("og_image", $request->og_image);

        return redirect()->back()->with("success", "Settings updated successfully.");
    }
}
