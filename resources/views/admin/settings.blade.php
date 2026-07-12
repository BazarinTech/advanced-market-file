@extends('layouts.admin')
@section('title', 'Settings - Admin')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Platform Settings</h1>

<div class="flex flex-col gap-6 max-w-2xl">

    {{-- ── Section 1: Withdrawal Settings ── --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
            <div class="w-1 h-5 bg-blue-600 rounded"></div>
            <h2 class="text-base font-semibold text-gray-700">Withdrawal Settings</h2>
        </div>

        @if(session('success_withdrawal'))
            <div class="bg-green-50 border border-green-300 text-green-700 text-sm px-4 py-2 rounded mb-4">
                {{ session('success_withdrawal') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="flex flex-col gap-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Minimum Withdrawal Amount (Kes)
                </label>
                <input type="number" name="withdrawal_min" min="1" step="1"
                       value="{{ old('withdrawal_min', $withdrawal_min) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                       required>
                <p class="text-xs text-gray-400 mt-1">Users cannot withdraw less than this amount.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Withdrawal Fee (%)
                </label>
                <input type="number" name="withdrawal_fee" min="0" max="100" step="0.1"
                       value="{{ old('withdrawal_fee', $withdrawal_fee) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                       required>
                <p class="text-xs text-gray-400 mt-1">Percentage deducted from every withdrawal. E.g. <strong>5</strong> = 5%.</p>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg text-sm">
                    Save Withdrawal Settings
                </button>
            </div>
        </form>
    </div>

    {{-- ── Section 2: Home Page Banner ── --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
            <div class="w-1 h-5 bg-emerald-500 rounded"></div>
            <h2 class="text-base font-semibold text-gray-700">Home Page Banner</h2>
        </div>

        @if(session('success_banner'))
            <div class="bg-green-50 border border-green-300 text-green-700 text-sm px-4 py-2 rounded mb-4">
                {{ session('success_banner') }}
            </div>
        @endif

        {{-- Current banner preview --}}
        @if($home_banner)
        <div class="mb-4">
            <p class="text-xs text-gray-400 mb-2 uppercase tracking-wide">Current Banner</p>
            <img src="{{ asset('images/' . $home_banner) }}"
                 class="w-full h-36 object-cover rounded-lg border border-gray-200" alt="Current Banner">
        </div>
        @endif

        <form action="{{ route('admin.settings.banner') }}" method="POST"
              enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Upload New Banner Image
                </label>
                <input type="file" name="banner" accept="image/jpeg,image/jpg,image/png,image/webp"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-emerald-500"
                       required>
                <p class="text-xs text-gray-400 mt-1">Accepted: JPG, PNG, WEBP. Max size: 3 MB. Recommended width: 600px+.</p>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-lg text-sm">
                    Upload Banner
                </button>
            </div>
        </form>
    </div>


    {{-- ── Section 3: Claim Image ── --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
            <div class="w-1 h-5 bg-purple-500 rounded"></div>
            <h2 class="text-base font-semibold text-gray-700">Orders Page — Claim Image</h2>
        </div>

        @if(session('success_claim_image'))
            <div class="bg-green-50 border border-green-300 text-green-700 text-sm px-4 py-2 rounded mb-4">
                {{ session('success_claim_image') }}
            </div>
        @endif

        @if($claim_image)
        <div class="mb-4 flex items-center gap-4">
            <div>
                <p class="text-xs text-gray-400 mb-2 uppercase tracking-wide">Current Image</p>
                <img src="{{ asset('images/' . $claim_image) }}"
                     class="w-20 h-20 object-cover rounded-lg border border-gray-200" alt="Claim Image">
            </div>
        </div>
        @endif

        <form action="{{ route('admin.settings.claim-image') }}" method="POST"
              enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Upload New Claim Image
                </label>
                <input type="file" name="claim_image" accept="image/jpeg,image/jpg,image/png,image/webp"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-500"
                       required>
                <p class="text-xs text-gray-400 mt-1">Accepted: JPG, PNG, WEBP. Max size: 2 MB. Recommended: square image (e.g. 200×200).</p>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded-lg text-sm">
                    Upload Image
                </button>
            </div>
        </form>
    </div>


    {{-- ── Section 4: Links & Social ── --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
            <div class="w-1 h-5 bg-indigo-500 rounded"></div>
            <h2 class="text-base font-semibold text-gray-700">Links &amp; Social</h2>
        </div>

        @if(session('success_links'))
            <div class="bg-green-50 border border-green-300 text-green-700 text-sm px-4 py-2 rounded mb-4">
                {{ session('success_links') }}
            </div>
        @endif
        @if($errors->has('link_whatsapp') || $errors->has('link_telegram') || $errors->has('link_customer_support') || $errors->has('link_download_app'))
            <div class="bg-red-50 border border-red-300 text-red-700 text-sm px-4 py-2 rounded mb-4">
                @foreach(['link_whatsapp','link_telegram','link_customer_support','link_download_app'] as $f)
                    @error($f)<p>{{ $message }}</p>@enderror
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.settings.links') }}" method="POST" class="flex flex-col gap-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    <x-icon name="whatsapp" class="text-green-500 mr-1" /> WhatsApp Group URL
                </label>
                <input type="url" name="link_whatsapp"
                       value="{{ old('link_whatsapp', $link_whatsapp) }}"
                       placeholder="https://chat.whatsapp.com/..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                <p class="text-xs text-gray-400 mt-1">Leave blank to hide from users.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    <x-icon name="telegram" class="text-blue-500 mr-1" /> Telegram Group URL
                </label>
                <input type="url" name="link_telegram"
                       value="{{ old('link_telegram', $link_telegram) }}"
                       placeholder="https://t.me/..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                <p class="text-xs text-gray-400 mt-1">Leave blank to hide from users.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    <x-icon name="headset" class="text-cyan-500 mr-1" /> Customer Support URL
                </label>
                <input type="url" name="link_customer_support"
                       value="{{ old('link_customer_support', $link_customer_support) }}"
                       placeholder="https://wa.me/254..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                <p class="text-xs text-gray-400 mt-1">Link to WhatsApp chat, Telegram DM, or a support page.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    <x-icon name="download" class="text-amber-500 mr-1" /> Download App URL
                </label>
                <input type="url" name="link_download_app"
                       value="{{ old('link_download_app', $link_download_app) }}"
                       placeholder="https://..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                <p class="text-xs text-gray-400 mt-1">APK download link or Play Store / App Store URL.</p>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg text-sm">
                    Save Links
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
