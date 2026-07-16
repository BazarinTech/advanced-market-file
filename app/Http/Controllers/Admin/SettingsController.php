<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Media;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    protected function brandingUrl(string $key): ?string
    {
        return Media::brandingUrl($key);
    }

    public function index()
    {
        return Inertia::render('Admin/Settings', [
            'withdrawal_min'       => Setting::get('withdrawal_min', 50),
            'withdrawal_fee'       => Setting::get('withdrawal_fee', 5),
            'home_banner'          => $this->brandingUrl('home_banner'),
            'claim_image'          => $this->brandingUrl('claim_image'),
            'link_whatsapp'        => Setting::get('link_whatsapp'),
            'link_telegram'        => Setting::get('link_telegram'),
            'link_customer_support'=> Setting::get('link_customer_support'),
            'link_download_app'    => Setting::get('link_download_app'),
            'logo'                 => $this->brandingUrl('logo'),
            'usd_kes_rate'         => Setting::get('usd_kes_rate', 130),
            'crypto_deposit_address' => Setting::get('crypto_deposit_address'),
            'referral_level1_pct'  => Setting::get('referral_level1_pct', 10),
            'referral_level2_pct'  => Setting::get('referral_level2_pct', 3),
            'referral_level3_pct'  => Setting::get('referral_level3_pct', 1),
        ]);
    }

    public function updateReferral(Request $request)
    {
        $request->validate([
            'referral_level1_pct' => 'required|numeric|min:0|max:100',
            'referral_level2_pct' => 'required|numeric|min:0|max:100',
            'referral_level3_pct' => 'required|numeric|min:0|max:100',
        ]);

        Setting::set('referral_level1_pct', $request->referral_level1_pct);
        Setting::set('referral_level2_pct', $request->referral_level2_pct);
        Setting::set('referral_level3_pct', $request->referral_level3_pct);

        return back()->with('success_referral', 'Referral commission rates saved.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'withdrawal_min' => 'required|numeric|min:1',
            'withdrawal_fee' => 'required|numeric|min:0|max:100',
            'usd_kes_rate'   => 'required|numeric|min:1',
        ]);

        Setting::set('withdrawal_min', $request->withdrawal_min);
        Setting::set('withdrawal_fee', $request->withdrawal_fee);
        Setting::set('usd_kes_rate', $request->usd_kes_rate);

        return back()->with('success_withdrawal', 'Withdrawal settings saved.');
    }

    public function updateCrypto(Request $request)
    {
        $request->validate([
            'crypto_deposit_address' => 'required|string|max:100',
        ]);

        Setting::set('crypto_deposit_address', $request->crypto_deposit_address);

        return back()->with('success_crypto', 'Crypto deposit address saved.');
    }

    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner' => 'required|image|mimes:jpeg,jpg,png,webp|max:3072',
        ]);

        $file     = $request->file('banner');
        $filename = 'banner-home.' . $file->getClientOriginalExtension();
        Media::put($file, 'branding', $filename);

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
        Media::put($file, 'branding', $filename);

        Setting::set('claim_image', $filename);

        return back()->with('success_claim_image', 'Claim image updated.');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,jpg,png,webp|max:1024',
        ]);

        $file     = $request->file('logo');
        $filename = 'logo-mark.' . $file->getClientOriginalExtension();
        Media::put($file, 'branding', $filename);

        Setting::set('logo', $filename);

        return back()->with('success_logo', 'Logo updated.');
    }
}
