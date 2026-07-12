@extends('layouts.app')
@section('title', 'Investment Plans - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center h-14 px-4 bg-[#0a1929] border-b border-[#1a3a4a]">
    <a href="{{ route('account') }}" class="text-[#64899a] hover:text-white text-lg mr-4 no-underline">
        <x-icon name="arrow-left" />
    </a>
    <p class="flex-1 text-center text-white text-xs font-light tracking-[0.3em] uppercase pr-8">Investment Plans</p>
</div>

<div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">

    {{-- Section heading --}}
    <div class="flex items-center gap-3">
        <div class="w-4 h-px bg-[#00c9a7]"></div>
        <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase">Plans at a Glance</p>
        <div class="flex-1 h-px bg-[#1a3a4a]"></div>
    </div>

    {{-- Comparison table --}}
    <div class="rounded-xl overflow-hidden overflow-x-auto" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <table class="w-full text-xs border-collapse" style="min-width:340px;">
            <thead>
                <tr style="background:#0a1929; border-bottom:1px solid #1a3a4a;">
                    <td class="px-3 py-3 text-left font-light tracking-[0.2em] uppercase text-[#00c9a7]" style="border-right:1px solid #1a3a4a;">
                        Plan
                    </td>
                    @foreach($packages as $pkg)
                    <td class="px-3 py-3 text-center font-light tracking-widests uppercase text-white" style="border-right:1px solid #1a3a4a;">
                        {{ $pkg->name }}
                    </td>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{-- Price --}}
                <tr style="border-bottom:1px solid #1a3a4a;">
                    <td class="px-3 py-3 text-[#00c9a7] tracking-widests uppercase font-light whitespace-nowrap" style="border-right:1px solid #1a3a4a; background:#0d1f35;">
                        Price
                    </td>
                    @foreach($packages as $pkg)
                    <td class="px-3 py-3 text-center text-white font-semibold tracking-wide" style="border-right:1px solid #1a3a4a; background:#0d1f35;">
                        Kes {{ number_format($pkg->amount, 0) }}
                    </td>
                    @endforeach
                </tr>
                {{-- Cycle --}}
                <tr style="border-bottom:1px solid #1a3a4a;">
                    <td class="px-3 py-3 text-[#00c9a7] tracking-widests uppercase font-light whitespace-nowrap" style="border-right:1px solid #1a3a4a; background:#0a1929;">
                        Cycle
                    </td>
                    @foreach($packages as $pkg)
                    <td class="px-3 py-3 text-center text-[#64899a] tracking-wide" style="border-right:1px solid #1a3a4a; background:#0a1929;">
                        {{ $pkg->days }} days
                    </td>
                    @endforeach
                </tr>
                {{-- Daily --}}
                <tr style="border-bottom:1px solid #1a3a4a;">
                    <td class="px-3 py-3 text-[#00c9a7] tracking-widests uppercase font-light whitespace-nowrap" style="border-right:1px solid #1a3a4a; background:#0d1f35;">
                        Daily
                    </td>
                    @foreach($packages as $pkg)
                    <td class="px-3 py-3 text-center text-white font-semibold tracking-wide" style="border-right:1px solid #1a3a4a; background:#0d1f35;">
                        Kes {{ number_format($pkg->daily, 0) }}
                    </td>
                    @endforeach
                </tr>
                {{-- Total --}}
                <tr style="border-bottom:1px solid #1a3a4a;">
                    <td class="px-3 py-3 text-[#00c9a7] tracking-widests uppercase font-light whitespace-nowrap" style="border-right:1px solid #1a3a4a; background:#0a1929;">
                        Total
                    </td>
                    @foreach($packages as $pkg)
                    <td class="px-3 py-3 text-center text-white font-semibold tracking-wide" style="border-right:1px solid #1a3a4a; background:#0a1929;">
                        Kes {{ number_format($pkg->daily * $pkg->days, 0) }}
                    </td>
                    @endforeach
                </tr>
                {{-- ROI --}}
                <tr>
                    <td class="px-3 py-3 text-[#00c9a7] tracking-widests uppercase font-light whitespace-nowrap" style="border-right:1px solid #1a3a4a; background:#0a1929;">
                        Return
                    </td>
                    @foreach($packages as $pkg)
                    @php
                        $roi = $pkg->amount > 0
                            ? round((($pkg->daily * $pkg->days) / $pkg->amount) * 100) . '%'
                            : '∞';
                    @endphp
                    <td class="px-3 py-3 text-center text-[#00c9a7] font-semibold tracking-widests" style="border-right:1px solid #1a3a4a; background:#0a1929;">
                        {{ $roi }}
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        <p class="text-[#64899a] text-[9px] tracking-widests uppercase text-right px-3 py-2">
            Return on investment over full cycle
        </p>
    </div>

    {{-- Withdrawal terms heading --}}
    <div class="flex items-center gap-3">
        <div class="w-4 h-px bg-[#00c9a7]"></div>
        <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase">Withdrawal Terms</p>
        <div class="flex-1 h-px bg-[#1a3a4a]"></div>
    </div>

    {{-- Withdrawal terms --}}
    <div class="rounded-xl overflow-hidden grid grid-cols-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="flex flex-col items-center py-5 px-2 gap-1.5" style="border-right:1px solid #1a3a4a;">
            <div class="w-9 h-9 rounded-lg bg-[#00c9a7]/10 flex items-center justify-center mb-1">
                <x-icon name="money-bill-wave" class="text-[#00c9a7] text-sm" />
            </div>
            <p class="text-[#64899a] text-[9px] tracking-widests uppercase text-center">Min. Withdrawal</p>
            <p class="text-white text-sm font-semibold">Kes 200</p>
        </div>
        <div class="flex flex-col items-center py-5 px-2 gap-1.5" style="border-right:1px solid #1a3a4a;">
            <div class="w-9 h-9 rounded-lg bg-[#f59e0b]/10 flex items-center justify-center mb-1">
                <x-icon name="percent" class="text-[#f59e0b] text-sm" />
            </div>
            <p class="text-[#64899a] text-[9px] tracking-widests uppercase text-center">Fee Charged</p>
            <p class="text-white text-sm font-semibold">6%</p>
        </div>
        <div class="flex flex-col items-center py-5 px-2 gap-1.5">
            <div class="w-9 h-9 rounded-lg bg-[#22c55e]/10 flex items-center justify-center mb-1">
                <x-icon name="bolt" class="text-[#22c55e] text-sm" />
            </div>
            <p class="text-[#64899a] text-[9px] tracking-widests uppercase text-center">Processing</p>
            <p class="text-white text-sm font-semibold">Instant</p>
        </div>
    </div>

    {{-- CTA --}}
    <a href="{{ route('packages') }}"
       class="w-full rounded-lg bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-bold text-xs py-3.5 tracking-[0.2em] uppercase text-center no-underline transition-colors">
        View Investment Plans
    </a>

</div>
@endsection
