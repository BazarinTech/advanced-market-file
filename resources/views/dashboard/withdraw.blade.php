@extends('layouts.app')
@section('title', 'Withdraw - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center h-14 px-4 bg-[#0a1929] border-b border-[#1a3a4a]">
    <a href="{{ route('account') }}" class="text-[#64899a] hover:text-white text-lg mr-4 no-underline">
        <x-icon name="arrow-left" />
    </a>
    <p class="flex-1 text-center text-white text-xs font-light tracking-[0.3em] uppercase pr-8">Withdraw</p>
</div>

<div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">

    {{-- Balance card --}}
    <div class="rounded-xl p-5 text-center" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase mb-1">Available Balance</p>
        <p class="text-white text-2xl font-light tracking-wide">Kes <span class="font-semibold">{{ number_format($earnings->balance, 2) }}</span></p>
        <div class="w-10 h-px bg-[#00c9a7] mx-auto mt-3"></div>
    </div>

    @if(session('success'))
        <div class="px-4 py-2 rounded-lg text-sm text-[#22c55e] bg-[#22c55e]/10 border border-[#22c55e]/20">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-2 rounded-lg text-sm text-[#ef4444] bg-[#ef4444]/10 border border-[#ef4444]/20">{{ session('error') }}</div>
    @endif

    @if(!$account)
    {{-- No account — setup form --}}
    <div class="rounded-xl px-4 py-3 flex items-start gap-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <x-icon name="triangle-exclamation" class="text-[#f59e0b] mt-0.5 text-sm shrink-0" />
        <div class="space-y-1">
            <p class="text-white text-xs tracking-widest uppercase font-light">Setup Required</p>
            <p class="text-[#64899a] text-xs tracking-wide">Set up your withdrawal account before making a withdrawal. This can only be set once — contact support to change it later.</p>
        </div>
    </div>

    <div class="rounded-xl p-5" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase mb-4">Set Up Withdrawal Account</p>
        <form id="setup-form" action="{{ route('withdraw.setup') }}" method="post" class="flex flex-col gap-4">
            @csrf
            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Full Name (as on M-Pesa)</label>
                <input type="text" name="name" placeholder="Your full name"
                       value="{{ old('name') }}"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                       required>
            </div>
            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">M-Pesa Phone Number</label>
                <input type="tel" name="phone" placeholder="07xxxxxxxx"
                       value="{{ old('phone') }}"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                       required>
            </div>
            <button type="submit" id="setup-btn"
                    class="w-full bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-bold py-3 rounded-lg tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                Save Account
            </button>
        </form>
    </div>

    @else
    {{-- Account exists — withdrawal form --}}

    {{-- Withdrawal account card --}}
    <div class="rounded-xl px-4 py-4 flex items-center justify-between" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[#00c9a7]/10 flex items-center justify-center shrink-0">
                <x-icon name="mobile-screen" class="text-[#00c9a7] text-sm" />
            </div>
            <div>
                <p class="text-[#64899a] text-[10px] tracking-widests uppercase">Withdrawal Account</p>
                <p class="text-white text-sm font-light mt-0.5">{{ $account->name }}</p>
                <p class="text-[#64899a] text-xs">{{ $account->phone }}</p>
            </div>
        </div>
        <div class="text-right">
            <span class="bg-[#00c9a7]/10 text-[#00c9a7] text-[9px] px-2 py-1 rounded uppercase tracking-widest">M-Pesa</span>
            <p class="text-xs text-[#64899a] mt-2">
                <a href="{{ config('services.links.customer_support', '#') }}" target="_blank"
                   class="text-[#00c9a7] no-underline hover:text-white text-[9px] tracking-widests">Change? Support</a>
            </p>
        </div>
    </div>

    {{-- Info box --}}
    <div class="rounded-xl px-4 py-3 flex items-start gap-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <x-icon name="circle-info" class="text-[#00c9a7] mt-0.5 text-sm shrink-0" />
        <div class="space-y-1">
            <p class="text-[#64899a] text-xs">Fee charged: {{ $withdrawal_fee }}%</p>
            <p class="text-[#64899a] text-xs">Minimum withdrawal: Kes {{ number_format($withdrawal_min, 0) }}</p>
            <p class="text-[#64899a] text-xs">Withdrawals are processed automatically</p>
        </div>
    </div>

    {{-- Withdrawal form --}}
    <div class="rounded-xl p-5" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <form id="withdraw-form" action="{{ route('withdraw.store') }}" method="post" class="flex flex-col gap-4">
            @csrf
            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Amount (KES)</label>
                <input type="number" step="1" min="{{ $withdrawal_min }}" name="amount"
                       placeholder="e.g. 500"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                       required>
            </div>
            <p class="text-[#64899a] text-xs px-1">
                Sending to: <span class="text-white">{{ $account->phone }}</span> · {{ $account->name }}
            </p>
            <button type="submit" id="withdraw-btn"
                    class="w-full bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-bold py-3.5 rounded-lg tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                Withdraw
            </button>
        </form>
    </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    function bindLoading(formId, btnId, loadingText) {
        const form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function () {
            const btn = document.getElementById(btnId);
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="tracking-widest uppercase text-xs">${loadingText}</span>`;
        });
    }
    bindLoading('setup-form',    'setup-btn',    'Saving…');
    bindLoading('withdraw-form', 'withdraw-btn', 'Processing…');
})();
</script>
@endpush
@endsection
