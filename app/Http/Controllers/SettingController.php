<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::firstOrCreate(
            ['id' => 1],
            [
                'phone' => '',
                'theme' => 'light',
                'company_name' => '',
                'company_email' => '',
                'company_address' => ''
            ]
        );
        return view('admin.settings.view', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::firstOrCreate(['id' => 1]);

        $setting->update($request->only([
            'phone', 'theme', 'company_name', 'company_email', 'company_address'
        ]));

        return back()->with('success', 'Pengaturan berhasil diperbarui');
    }
}
