@extends('layouts.app')
@section('title', 'Home - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center justify-between h-14 px-4 bg-[#0a1929] border-b border-[#1a3a4a]">
    <div class="flex items-center gap-2">
        <div class="w-7 h-7 rounded bg-[#00c9a7] flex items-center justify-center">
            <x-icon name="chart-line" class="text-[#050f1a] text-xs" />
        </div>
        <span class="text-white font-semibold tracking-widest text-sm uppercase">Trade-Swing</span>
    </div>
    <div class="text-right">
        <p class="text-[#64899a] text-[10px] tracking-widest uppercase">Welcome back</p>
        <p class="text-[#00c9a7] text-xs tracking-wide">{{ $user->phone }}</p>
    </div>
</div>

{{-- Market ticker strip --}}
<div class="w-full bg-[#0a1929] border-b border-[#1a3a4a] overflow-hidden py-2">
    <div class="flex gap-6 ticker-track whitespace-nowrap px-4" style="width:max-content;">
        @foreach([
            ['BTC/USD','$67,420','▲ 2.4%', true],
            ['ETH/USD','$3,512','▲ 1.8%', true],
            ['BNB/USD','$412','▼ 0.6%', false],
            ['SOL/USD','$178','▲ 4.1%', true],
            ['XRP/USD','$0.612','▲ 0.9%', true],
            ['ADA/USD','$0.48','▼ 1.2%', false],
            ['BTC/USD','$67,420','▲ 2.4%', true],
            ['ETH/USD','$3,512','▲ 1.8%', true],
            ['BNB/USD','$412','▼ 0.6%', false],
            ['SOL/USD','$178','▲ 4.1%', true],
        ] as [$pair, $price, $change, $up])
        <span class="inline-flex items-center gap-1.5 text-[11px]">
            <span class="text-[#64899a]">{{ $pair }}</span>
            <span class="text-white font-medium">{{ $price }}</span>
            <span class="{{ $up ? 'text-[#22c55e]' : 'text-[#ef4444]' }}">{{ $change }}</span>
        </span>
        @endforeach
    </div>
</div>

{{-- Portfolio Value Card --}}
<div class="w-full px-4 mt-4">
    <div class="w-full rounded-xl p-5 relative overflow-hidden"
         style="background: linear-gradient(135deg, #0d2a40 0%, #0a3d30 100%); border: 1px solid #1a3a4a;">
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-10"
             style="background: #00c9a7; transform: translate(30%, -30%);"></div>
        <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase">Portfolio Value</p>
        <p class="text-white text-3xl font-light mt-1 tracking-wide">
            Kes <span class="font-semibold">{{ number_format($earnings->balance, 2) }}</span>
        </p>
        <div class="w-12 h-px bg-[#00c9a7] mt-3 mb-3"></div>
        <div class="flex gap-6">
            <div>
                <p class="text-[#64899a] text-[10px] tracking-widest uppercase">Earned</p>
                <p class="text-[#22c55e] text-sm font-medium">Kes {{ number_format($earnings->referral + $earnings->deposit, 2) }}</p>
            </div>
            <div>
                <p class="text-[#64899a] text-[10px] tracking-widest uppercase">Deposited</p>
                <p class="text-white text-sm">Kes {{ number_format($earnings->deposit, 2) }}</p>
            </div>
            <div>
                <p class="text-[#64899a] text-[10px] tracking-widest uppercase">Team</p>
                <p class="text-white text-sm">{{ $downline }}</p>
            </div>
        </div>
    </div>
</div>

{{-- TradingView Chart --}}
<div class="w-full px-4 mt-4">
    <div class="w-full rounded-xl overflow-hidden" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="flex items-center justify-between px-4 pt-3 pb-1">
            <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase">Live Market</p>
            <span class="text-[#00c9a7] text-[10px] tracking-widest uppercase">TradingView</span>
        </div>
        <div class="tradingview-widget-container" style="height:220px;">
            <div class="tradingview-widget-container__widget" style="height:220px;width:100%;"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js" async>
            {
                "symbol": "BINANCE:BTCUSDT",
                "width": "100%",
                "height": 220,
                "locale": "en",
                "dateRange": "1M",
                "colorTheme": "dark",
                "trendLineColor": "rgba(0,201,167,1)",
                "underLineColor": "rgba(0,201,167,0.25)",
                "underLineBottomColor": "rgba(0,201,167,0)",
                "isTransparent": true,
                "autosize": true,
                "largeChartUrl": ""
            }
            </script>
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="w-full px-4 mt-4 grid grid-cols-3 gap-3">
    <div class="rounded-lg p-3 text-center" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <x-icon name="arrow-trend-up" class="text-[#00c9a7] mb-1" />
        <p class="text-[#64899a] text-[9px] tracking-widest uppercase">Withdrawn</p>
        <p class="text-white text-xs font-medium mt-0.5">{{ number_format($earnings->withdraw, 0) }}</p>
    </div>
    <div class="rounded-lg p-3 text-center" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <x-icon name="users" class="text-[#00c9a7] mb-1" />
        <p class="text-[#64899a] text-[9px] tracking-widest uppercase">Active Team</p>
        <p class="text-white text-xs font-medium mt-0.5">{{ $numActive }}</p>
    </div>
    <div class="rounded-lg p-3 text-center" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <x-icon name="coins" class="text-[#00c9a7] mb-1" />
        <p class="text-[#64899a] text-[9px] tracking-widest uppercase">Referral</p>
        <p class="text-white text-xs font-medium mt-0.5">{{ number_format($earnings->referral, 0) }}</p>
    </div>
</div>

{{-- Quick Actions --}}
<div class="w-full px-4 mt-4 grid grid-cols-2 gap-3 pb-28">
    <a href="{{ route('deposit') }}"
       class="flex items-center gap-3 rounded-xl p-4 no-underline"
       style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="w-9 h-9 rounded-lg bg-[#00c9a7]/10 flex items-center justify-center">
            <x-icon name="plus" class="text-[#00c9a7]" />
        </div>
        <div>
            <p class="text-white text-xs font-medium">Deposit</p>
            <p class="text-[#64899a] text-[10px]">Add funds</p>
        </div>
    </a>
    <a href="{{ route('withdraw') }}"
       class="flex items-center gap-3 rounded-xl p-4 no-underline"
       style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="w-9 h-9 rounded-lg bg-[#22c55e]/10 flex items-center justify-center">
            <x-icon name="arrow-up-from-bracket" class="text-[#22c55e]" />
        </div>
        <div>
            <p class="text-white text-xs font-medium">Withdraw</p>
            <p class="text-[#64899a] text-[10px]">Cash out</p>
        </div>
    </a>
    <a href="{{ route('packages') }}"
       class="flex items-center gap-3 rounded-xl p-4 no-underline"
       style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="w-9 h-9 rounded-lg bg-[#22d3ee]/10 flex items-center justify-center">
            <x-icon name="layer-group" class="text-[#22d3ee]" />
        </div>
        <div>
            <p class="text-white text-xs font-medium">Plans</p>
            <p class="text-[#64899a] text-[10px]">View & invest</p>
        </div>
    </a>
    <a href="{{ route('task') }}"
       class="flex items-center gap-3 rounded-xl p-4 no-underline"
       style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="w-9 h-9 rounded-lg bg-[#f59e0b]/10 flex items-center justify-center">
            <x-icon name="briefcase" class="text-[#f59e0b]" />
        </div>
        <div>
            <p class="text-white text-xs font-medium">Orders</p>
            <p class="text-[#64899a] text-[10px]">Claim earnings</p>
        </div>
    </a>
</div>

@include('partials.bottom-nav')

@endsection
