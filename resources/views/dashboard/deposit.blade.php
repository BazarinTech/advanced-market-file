@extends('layouts.app')
@section('title', 'Deposit - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center h-14 px-4 bg-[#0a1929] border-b border-[#1a3a4a]">
    <a href="{{ route('account') }}" class="text-[#64899a] hover:text-white text-lg mr-4 no-underline">
        <x-icon name="arrow-left" />
    </a>
    <p class="flex-1 text-center text-white text-xs font-light tracking-[0.3em] uppercase pr-8">Deposit</p>
</div>

<div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">

    {{-- Balance card --}}
    <div class="rounded-xl p-5 text-center" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase mb-1">Available Balance</p>
        <p class="text-white text-2xl font-light tracking-wide">Kes <span class="font-semibold">{{ number_format($earnings->balance, 2) }}</span></p>
        <div class="w-10 h-px bg-[#00c9a7] mx-auto mt-3"></div>
    </div>

    {{-- Info box --}}
    <div class="rounded-xl px-4 py-3 flex items-start gap-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <x-icon name="circle-info" class="text-[#00c9a7] mt-0.5 text-sm shrink-0" />
        <div class="space-y-1">
            <p class="text-[#64899a] text-xs">Enter amount and phone number</p>
            <p class="text-[#64899a] text-xs">Click submit — you will receive an M-Pesa STK pop-up</p>
            <p class="text-[#64899a] text-xs">Enter your PIN and balance updates automatically</p>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-2 rounded-lg text-sm text-[#22c55e] bg-[#22c55e]/10 border border-[#22c55e]/20">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-2 rounded-lg text-sm text-[#ef4444] bg-[#ef4444]/10 border border-[#ef4444]/20">{{ session('error') }}</div>
    @endif

    {{-- Form --}}
    <div class="rounded-xl p-5" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <form id="deposit-form" action="{{ route('deposit.store') }}" method="post" class="flex flex-col gap-4">
            @csrf
            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Amount (KES)</label>
                <input type="number" step="1" min="400" name="amount"
                       placeholder="e.g. 1000"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                       required>
            </div>
            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Phone Number</label>
                <input type="tel" name="phone"
                       placeholder="07xxxxxxxx"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                       required>
            </div>
            <button type="submit" id="deposit-btn"
                    class="w-full bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-bold py-3.5 rounded-lg tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                Submit Deposit
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('deposit-form').addEventListener('submit', function () {
    const btn = document.getElementById('deposit-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="tracking-widest uppercase text-xs">Processing…</span>';
});
</script>
@endpush
@endsection
