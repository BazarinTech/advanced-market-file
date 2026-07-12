@extends('layouts.app')
@section('title', 'Redeem Coupon - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center h-14 px-4 bg-[#0a1929] border-b border-[#1a3a4a]">
    <a href="{{ route('account') }}" class="text-[#64899a] hover:text-white text-lg mr-4 no-underline">
        <x-icon name="arrow-left" />
    </a>
    <p class="flex-1 text-center text-white text-xs font-light tracking-[0.3em] uppercase pr-8">Redeem Coupon</p>
</div>

<div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">

    {{-- Hero card --}}
    <div class="rounded-xl p-5 text-center relative overflow-hidden"
         style="background: linear-gradient(135deg, #0d2a40 0%, #0a3d30 100%); border: 1px solid #1a3a4a;">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full opacity-10"
             style="background:#00c9a7; transform:translate(30%,-30%);"></div>
        <div class="w-12 h-12 rounded-xl bg-[#00c9a7]/10 flex items-center justify-center mx-auto mb-3">
            <x-icon name="ticket" class="text-[#00c9a7] text-xl" />
        </div>
        <p class="text-white text-sm font-medium tracking-wide mb-1">Got a coupon code?</p>
        <p class="text-[#64899a] text-xs tracking-wide">Enter it below to receive your reward instantly credited to your balance.</p>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-lg text-sm text-[#22c55e] bg-[#22c55e]/10 border border-[#22c55e]/20 flex items-center gap-2">
            <x-icon name="circle-check" class="shrink-0" />
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-lg text-sm text-[#ef4444] bg-[#ef4444]/10 border border-[#ef4444]/20 flex items-center gap-2">
            <x-icon name="circle-xmark" class="shrink-0" />
            {{ session('error') }}
        </div>
    @endif

    {{-- Redemption form --}}
    <div class="rounded-xl p-5" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <form action="{{ route('coupon.redeem') }}" method="POST" id="coupon-form" class="flex flex-col gap-4">
            @csrf
            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Coupon Code</label>
                <input type="text" name="code"
                       placeholder="e.g. TRADE2025"
                       value="{{ old('code') }}"
                       autocomplete="off"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a] uppercase tracking-widest"
                       required>
            </div>
            <button type="submit" id="coupon-btn"
                    class="w-full bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-bold py-3.5 rounded-lg tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                Redeem Coupon
            </button>
        </form>
    </div>

    {{-- Info box --}}
    <div class="rounded-xl px-4 py-3 flex items-start gap-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <x-icon name="circle-info" class="text-[#00c9a7] mt-0.5 text-sm shrink-0" />
        <div class="space-y-1">
            <p class="text-[#64899a] text-xs">Each coupon can only be redeemed once per account</p>
            <p class="text-[#64899a] text-xs">Rewards are credited instantly to your balance</p>
            <p class="text-[#64899a] text-xs">Coupons are case-insensitive and may have expiry limits</p>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.getElementById('coupon-form').addEventListener('submit', function () {
    const btn = document.getElementById('coupon-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="tracking-widest uppercase text-xs">Verifying…</span>';
});
// Auto-uppercase input
document.querySelector('input[name="code"]').addEventListener('input', function () {
    this.value = this.value.toUpperCase();
});
</script>
@endpush
@endsection
