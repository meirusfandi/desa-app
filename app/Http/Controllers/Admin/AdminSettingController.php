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
