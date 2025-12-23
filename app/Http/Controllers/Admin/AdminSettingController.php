<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_description' => 'nullable|string|max:2000',
            'app_email' => 'nullable|email|max:255',
            'app_phone' => 'nullable|string|max:50',
            'app_address' => 'nullable|string|max:2000',

            'kabupaten_name' => 'nullable|string|max:255',
            'kecamatan_name' => 'nullable|string|max:255',
            'desa_name' => 'nullable|string|max:255',
            'full_address' => 'nullable|string|max:2000',
            'post_code' => 'nullable|string|max:20',

            'signature_location' => 'nullable|string|max:255',
            'signature_role' => 'nullable|string|max:255',
            'signature_name' => 'nullable|string|max:255',

            'app_logo' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'app_favicon' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg,ico|max:2048',
            'logo_desa' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'logo_kecamatan' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'logo_kabupaten' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        // Handle file uploads
        if ($request->hasFile('app_logo')) {
            $path = $request->file('app_logo')->store('settings', 'public');
            \App\Models\Setting::updateOrCreate(['key' => 'app_logo'], ['value' => $path]);
        }
        if ($request->hasFile('app_favicon')) {
            $path = $request->file('app_favicon')->store('settings', 'public');
            \App\Models\Setting::updateOrCreate(['key' => 'app_favicon'], ['value' => $path]);
        }

        $regionalLogos = ['logo_desa', 'logo_kecamatan', 'logo_kabupaten'];
        foreach ($regionalLogos as $logo) {
            if ($request->hasFile($logo)) {
                $path = $request->file($logo)->store('settings', 'public');
                \App\Models\Setting::updateOrCreate(['key' => $logo], ['value' => $path]);
            }
        }

        // Handle other fields
        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) continue;
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
    }
}
