@extends('layouts.app')
@section('title', 'Account - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center justify-center h-14 bg-[#0a1929] border-b border-[#1a3a4a]">
    <p class="text-white text-xs font-light tracking-[0.3em] uppercase">Account</p>
</div>

{{-- Balance Card --}}
<div class="w-full px-4 mt-4">
    <div class="w-full rounded-xl p-5 grid grid-cols-3 text-center"
         style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="flex flex-col gap-1.5 items-center">
            <p class="text-[#64899a] text-[9px] tracking-widest uppercase">Balance</p>
            <p class="text-white text-sm font-medium">Kes {{ number_format($earnings->balance, 2) }}</p>
        </div>
        <div class="flex flex-col gap-1.5 items-center border-l border-r border-[#1a3a4a]">
            <p class="text-[#64899a] text-[9px] tracking-widest uppercase">Deposited</p>
            <p class="text-white text-sm font-medium">Kes {{ number_format($earnings->deposit, 2) }}</p>
        </div>
        <div class="flex flex-col gap-1.5 items-center">
            <p class="text-[#64899a] text-[9px] tracking-widest uppercase">Withdrawn</p>
            <p class="text-white text-sm font-medium">Kes {{ number_format($earnings->withdraw, 2) }}</p>
        </div>
    </div>
</div>

{{-- Menu Items --}}
<div class="w-full px-4 mt-4 mb-28 flex flex-col gap-2">
    @foreach([
        ['deposit',         'plus-circle',        'Deposit',           'Add funds to your account'],
        ['transaction',     'receipt',            'Transactions',      'View transaction history'],
        ['withdraw',        'arrow-up-from-bracket','Withdraw',         'Cash out your earnings'],
        ['coupon',          'ticket',             'Redeem Coupon',     'Enter a code to claim your reward'],
        ['user',            'user-tie',           'Profile Settings',  'Manage your account details'],
        ['team',            'share-nodes',        'My Team',           'View referrals & earnings'],
        ['packages.table',  'table-list',         'Investment Plans',  'Compare all available plans'],
    ] as [$route, $icon, $label, $desc])
    <a href="{{ route($route) }}"
       class="flex items-center justify-between px-4 py-3.5 rounded-xl no-underline transition-colors"
       style="background:#0d1f35; border:1px solid #1a3a4a;">
        <span class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[#00c9a7]/10 flex items-center justify-center shrink-0">
                <x-icon name="{{ $icon }}" class="text-[#00c9a7] text-sm" />
            </div>
            <span>
                <span class="block text-white text-sm tracking-wide font-light">{{ $label }}</span>
                <span class="block text-[#64899a] text-[10px] mt-0.5">{{ $desc }}</span>
            </span>
        </span>
        <x-icon name="angle-right" class="text-[#1a3a4a] text-sm" />
    </a>
    @endforeach

    @if($link_whatsapp)
    <a href="{{ $link_whatsapp }}" target="_blank"
       class="flex items-center justify-between px-4 py-3.5 rounded-xl no-underline transition-colors"
       style="background:#0d1f35; border:1px solid #1a3a4a;">
        <span class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[#22c55e]/10 flex items-center justify-center shrink-0">
                <x-icon name="whatsapp" class="text-[#22c55e] text-sm" />
            </div>
            <span>
                <span class="block text-white text-sm tracking-wide font-light">WhatsApp Group</span>
                <span class="block text-[#64899a] text-[10px] mt-0.5">Join our community</span>
            </span>
        </span>
        <x-icon name="angle-right" class="text-[#1a3a4a] text-sm" />
    </a>
    @endif

    @if($link_telegram)
    <a href="{{ $link_telegram }}" target="_blank"
       class="flex items-center justify-between px-4 py-3.5 rounded-xl no-underline transition-colors"
       style="background:#0d1f35; border:1px solid #1a3a4a;">
        <span class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[#3b82f6]/10 flex items-center justify-center shrink-0">
                <x-icon name="telegram" class="text-[#3b82f6] text-sm" />
            </div>
            <span>
                <span class="block text-white text-sm tracking-wide font-light">Telegram Group</span>
                <span class="block text-[#64899a] text-[10px] mt-0.5">Get updates & announcements</span>
            </span>
        </span>
        <x-icon name="angle-right" class="text-[#1a3a4a] text-sm" />
    </a>
    @endif

    @if($link_customer_support)
    <a href="{{ $link_customer_support }}" target="_blank"
       class="flex items-center justify-between px-4 py-3.5 rounded-xl no-underline transition-colors"
       style="background:#0d1f35; border:1px solid #1a3a4a;">
        <span class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[#22d3ee]/10 flex items-center justify-center shrink-0">
                <x-icon name="headset" class="text-[#22d3ee] text-sm" />
            </div>
            <span>
                <span class="block text-white text-sm tracking-wide font-light">Customer Support</span>
                <span class="block text-[#64899a] text-[10px] mt-0.5">Get help from our team</span>
            </span>
        </span>
        <x-icon name="angle-right" class="text-[#1a3a4a] text-sm" />
    </a>
    @endif

    @if($link_download_app)
    <a href="{{ $link_download_app }}" target="_blank"
       class="flex items-center justify-between px-4 py-3.5 rounded-xl no-underline transition-colors"
       style="background:#0d1f35; border:1px solid #1a3a4a;">
        <span class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[#f59e0b]/10 flex items-center justify-center shrink-0">
                <x-icon name="download" class="text-[#f59e0b] text-sm" />
            </div>
            <span>
                <span class="block text-white text-sm tracking-wide font-light">Download App</span>
                <span class="block text-[#64899a] text-[10px] mt-0.5">Get the mobile application</span>
            </span>
        </span>
        <x-icon name="angle-right" class="text-[#1a3a4a] text-sm" />
    </a>
    @endif

    {{-- Logout --}}
    <form action="{{ route('logout') }}" method="post" class="mt-2">
        @csrf
        <button type="submit"
                class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl transition-colors text-left"
                style="background:#1a0a0a; border:1px solid #3a1a1a;">
            <span class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#ef4444]/10 flex items-center justify-center shrink-0">
                    <x-icon name="right-from-bracket" class="text-[#ef4444] text-sm" />
                </div>
                <span class="text-[#ef4444] text-sm tracking-wide font-light">Sign Out</span>
            </span>
            <x-icon name="angle-right" class="text-[#3a1a1a] text-sm" />
        </button>
    </form>
</div>

@include('partials.bottom-nav')
@endsection
