@extends('layouts.app')
@section('title', 'Transactions - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center h-14 px-4 bg-[#0a1929] border-b border-[#1a3a4a] fixed top-0 left-1/2 -translate-x-1/2 max-w-150 z-10">
    <a href="{{ route('account') }}" class="text-[#64899a] hover:text-white text-lg mr-4 no-underline">
        <x-icon name="arrow-left" />
    </a>
    <p class="flex-1 text-center text-white text-xs font-light tracking-[0.3em] uppercase pr-8">Transactions</p>
</div>

<div class="w-full flex flex-col px-4 mt-16 pb-10 gap-3">
    @forelse($transactions as $tx)
    @php $isCredit = in_array($tx->type, ['Deposit','Deposits','Referral']); @endphp
    <div class="rounded-xl px-4 py-3 flex items-center justify-between" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $isCredit ? 'bg-[#22c55e]/10' : 'bg-[#ef4444]/10' }}">
                <x-icon name="{{ $isCredit ? 'arrow-down' : 'arrow-up' }}" class="{{ $isCredit ? 'text-[#22c55e]' : 'text-[#ef4444]' }} text-sm" />
            </div>
            <div>
                <p class="text-white text-sm font-medium tracking-wide">{{ $tx->type }}</p>
                <p class="text-xs tracking-widest mt-0.5 {{ in_array($tx->status, ['Success','Approved']) ? 'text-[#00c9a7]' : ($tx->status === 'Pending' ? 'text-[#f59e0b]' : 'text-[#ef4444]') }}">
                    {{ $tx->status }}
                </p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm font-semibold {{ $isCredit ? 'text-[#22c55e]' : 'text-[#ef4444]' }}">
                {{ $isCredit ? '+' : '-' }}Kes {{ number_format($tx->amount, 2) }}
            </p>
            <p class="text-[#64899a] text-[10px] mt-0.5">{{ $tx->date }}</p>
        </div>
    </div>
    @empty
    <div class="text-center mt-16">
        <x-icon name="receipt" class="text-[#1a3a4a] text-4xl mb-3" />
        <p class="text-[#64899a] tracking-widest uppercase text-xs">No transactions yet</p>
    </div>
    @endforelse
</div>
@endsection
