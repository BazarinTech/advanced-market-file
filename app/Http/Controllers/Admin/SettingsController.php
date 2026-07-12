<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings', [
            'withdrawal_min'       => Setting::get('withdrawal_min', 50),
            'withdrawal_fee'       => Setting::get('withdrawal_fee', 5),
            'home_banner'          => Setting::get('home_banner'),
            'claim_image'          => Setting::get('claim_image'),
            'link_whatsapp'        => Setting::get('link_whatsapp'),
            'link_telegram'        => Setting::get('link_telegram'),
            'link_customer_support'=> Setting::get('link_customer_support'),
            'link_download_app'    => Setting::get('link_download_app'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'withdrawal_min' => 'required|numeric|min:1',
            'withdrawal_fee' => 'required|numeric|min:0|max:100',
        ]);

        Setting::set('withdrawal_min', $request->withdrawal_min);
        Setting::set('withdrawal_fee', $request->withdrawal_fee);

        return back()->with('success_withdrawal', 'Withdrawal settings saved.');
    }

    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner' => 'required|image|mimes:jpeg,jpg,png,webp|max:3072',
        ]);

        $file     = $request->file('banner');
        $filename = 'banner-home.' . $file->getClientOriginalExtension();
        $file->move(base_path('images'), $filename);

        Setting::set('home_banner', $filename);

        return back()->with('success_banner', 'Banner image updated.');
    }

    public function updateLinks(Request $request)
    {
        $request->validate([
            'link_whatsapp'         => 'nullable|url|max:500',
            'link_telegram'         => 'nullable|url|max:500',
            'link_customer_support' => 'nullable|url|max:500',
            'link_download_app'     => 'nullable|url|max:500',
        ]);

        Setting::set('link_whatsapp',         $request->link_whatsapp ?? '');
        Setting::set('link_telegram',         $request->link_telegram ?? '');
        Setting::set('link_customer_support', $request->link_customer_support ?? '');
        Setting::set('link_download_app',     $request->link_download_app ?? '');

        return back()->with('success_links', 'Links saved successfully.');
    }

    public function updateClaimImage(Request $request)
    {
        $request->validate([
            'claim_image' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $file     = $request->file('claim_image');
        $filename = 'claim-icon.' . $file->getClientOriginalExtension();
        $file->move(base_path('images'), $filename);

        Setting::set('claim_image', $filename);

        return back()->with('success_claim_image', 'Claim image updated.');
    }
}
