<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingAdminController extends Controller
{
    public function edit()
    {
        // Ambil data pertama, kalau tidak ada buat object kosong
        $setting = Setting::first() ?? new Setting();
        return view('admin.setting.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();

        $validated = $request->validate([
            'site_name' => 'required|string|max:50',
            'site_description' => 'required|string',
            'copyright_text' => 'required|string',
            'mentor_commission_percent' => 'required|numeric',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'gmaps_embed_url' => 'nullable|string',
            'hero_title' => 'nullable|string|max:100',
            'hero_subtitle' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'hero_image' => 'nullable|image|max:2048',
        ]);

        // Handle Logo
        if ($request->hasFile('logo')) {
            if ($setting && $setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }
            $validated['logo'] = $request->file('logo')->store('settings', 'public');
        }

        // Handle Hero Image
        if ($request->hasFile('hero_image')) {
            if ($setting && $setting->hero_image) {
                Storage::disk('public')->delete($setting->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('settings', 'public');
        }

        Setting::updateOrCreate(['id' => 1], $validated);

        return redirect()->back()->with('success', 'Konfigurasi situs berhasil diperbarui!');
    }

    //
}
