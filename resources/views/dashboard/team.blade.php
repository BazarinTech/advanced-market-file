@extends('layouts.app')
@section('title', 'Team - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center h-14 px-4 bg-[#0a1929] border-b border-[#1a3a4a]">
    <a href="{{ route('account') }}" class="text-[#64899a] hover:text-white text-lg mr-4 no-underline">
        <x-icon name="arrow-left" />
    </a>
    <p class="flex-1 text-center text-white text-xs font-light tracking-[0.3em] uppercase pr-8">Team</p>
</div>

{{-- Stats Grid --}}
<div class="w-full grid grid-cols-2" style="border-bottom:1px solid #1a3a4a;">
    <div class="flex flex-col gap-1 items-center py-4" style="border-right:1px solid #1a3a4a; border-bottom:1px solid #1a3a4a; background:#0d1f35;">
        <p class="text-[#64899a] text-[9px] tracking-widests uppercase">Referral Bonus</p>
        <p class="text-[#00c9a7] text-sm font-semibold">Kes {{ number_format($earnings->referral, 2) }}</p>
    </div>
    <div class="flex flex-col gap-1 items-center py-4" style="border-bottom:1px solid #1a3a4a; background:#0d1f35;">
        <p class="text-[#64899a] text-[9px] tracking-widests uppercase">Total Members</p>
        <p class="text-white text-sm font-semibold">{{ $downline->count() }}</p>
    </div>
    <div class="flex flex-col gap-1 items-center py-4" style="border-right:1px solid #1a3a4a; background:#0d1f35;">
        <p class="text-[#64899a] text-[9px] tracking-widests uppercase">Deposited</p>
        <p class="text-white text-sm font-semibold">{{ $numDeposited }}</p>
    </div>
    <div class="flex flex-col gap-1 items-center py-4" style="background:#0d1f35;">
        <p class="text-[#64899a] text-[9px] tracking-widests uppercase">Active</p>
        <p class="text-[#22c55e] text-sm font-semibold">{{ $numActive }}</p>
    </div>
</div>

<div class="w-full px-4 mt-4 flex flex-col gap-3 pb-10">

    {{-- Referral Link Card --}}
    <div class="rounded-xl p-4" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase mb-1">Your Referral Code</p>
        <p class="text-[#00c9a7] text-lg font-semibold tracking-widest mb-4">{{ $user->ID }}</p>

        <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase mb-2">Invite Link</p>
        <div class="flex items-center gap-2">
            <input id="invite-link" type="text" readonly
                   value="{{ config('app.url') }}/register?invite={{ $user->ID }}"
                   class="flex-1 rounded-lg px-3 py-2.5 text-xs text-[#64899a] outline-none truncate tracking-wide"
                   style="background:#0a1929; border:1px solid #1a3a4a;">
            <button id="copy-btn" onclick="copyInviteLink()"
                    class="bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] text-xs font-bold px-4 py-2.5 rounded-lg uppercase tracking-widest whitespace-nowrap transition-colors">
                <x-icon name="copy" class="mr-1" /> Copy
            </button>
        </div>
    </div>

    {{-- Downline List --}}
    @forelse($downline as $member)
    @php
        $phone   = $member->phone;
        $masked  = substr($phone, 0, 2) . '***' . substr($phone, -2);
        $deposited = $depositTotals[$member->email] ?? 0;
    @endphp
    <div class="rounded-xl px-4 py-3 flex items-center justify-between" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[#00c9a7]/10 flex items-center justify-center shrink-0">
                <x-icon name="user" class="text-[#00c9a7] text-sm" />
            </div>
            <div>
                <p class="text-white text-sm font-light tracking-wide">{{ $masked }}</p>
                <p class="text-xs tracking-widests uppercase mt-0.5 {{ $member->status === 'Active' ? 'text-[#22c55e]' : 'text-[#ef4444]' }}">
                    {{ $member->status }}
                </p>
            </div>
        </div>
        <div class="text-center">
            <p class="text-[#64899a] text-[9px] tracking-widests uppercase mb-0.5">Deposited</p>
            <p class="text-white text-sm font-semibold">Kes {{ number_format($deposited, 0) }}</p>
        </div>
        <div class="text-right">
            <p class="text-[#64899a] text-[10px]">{{ $member->date }}</p>
        </div>
    </div>
    @empty
    <div class="text-center mt-10">
        <x-icon name="users" class="text-[#1a3a4a] text-4xl mb-3" />
        <p class="text-[#64899a] tracking-widests uppercase text-xs">No members yet</p>
    </div>
    @endforelse
</div>

{{-- Toast --}}
<div id="copy-toast" class="fixed bottom-6 left-1/2 rounded-xl text-white text-xs px-5 py-2.5 shadow-xl z-50 flex items-center gap-2 uppercase tracking-widests"
     style="background:#0d1f35; border:1px solid #1a3a4a; opacity:0; transform:translateX(-50%) translateY(16px); transition:opacity 0.3s, transform 0.3s; pointer-events:none;">
    <x-icon name="circle-check" class="text-[#00c9a7]" /> Link copied!
</div>

@push('scripts')
<script>
    const copyBtn = document.getElementById('copy-btn');
    const originalBtnHtml = copyBtn.innerHTML;

    function copyInviteLink() {
        const input = document.getElementById('invite-link');
        const text  = input.value;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(showFeedback).catch(function () {
                fallbackCopy(input);
            });
        } else {
            fallbackCopy(input);
        }
    }

    function fallbackCopy(input) {
        input.select();
        input.setSelectionRange(0, 99999);
        try { document.execCommand('copy'); } catch(e) {}
        input.blur();
        showFeedback();
    }

    function showFeedback() {
        copyBtn.innerHTML = '✓ Copied!';
        const toast = document.getElementById('copy-toast');
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) translateY(0)';
        setTimeout(function () {
            copyBtn.innerHTML = originalBtnHtml;
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(16px)';
        }, 2500);
    }
</script>
@endpush
@endsection
