@extends('layouts.app')
@section('title', 'Orders - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center justify-center h-14 bg-[#0a1929] border-b border-[#1a3a4a]">
    <p class="text-white text-xs font-light tracking-[0.3em] uppercase">My Orders</p>
</div>

@if(session('success'))
    <div class="mx-4 mt-3 px-4 py-2 rounded-lg text-sm text-[#22c55e] bg-[#22c55e]/10 border border-[#22c55e]/20">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mx-4 mt-3 px-4 py-2 rounded-lg text-sm text-[#ef4444] bg-[#ef4444]/10 border border-[#ef4444]/20">{{ session('error') }}</div>
@endif

<div class="flex flex-col items-center w-full px-4 pb-24 mt-4">

    {{-- Claim card --}}
    <form action="{{ route('task.claim') }}" method="post" id="claim-form"
          class="w-full rounded-xl p-5 flex flex-col items-center" style="background:#0d1f35; border:1px solid #1a3a4a;">
        @csrf
        <img src="{{ $claimImgSrc }}" class="w-16 h-16 object-cover mx-auto mb-3 rounded-lg" alt="">
        <p class="text-[#64899a] text-center text-xs tracking-wide">Collect your daily earnings from active plans</p>
        @if($canClaim)
            <button id="claim-btn" name="claim"
                    class="mt-4 w-[75%] bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-semibold py-2.5 rounded-lg tracking-widest uppercase text-xs flex items-center justify-center gap-2">
                <i class="fa-solid fa-bolt"></i> Claim Earnings
            </button>
        @else
            <div class="mt-4 text-center">
                <p class="text-[#64899a] text-[10px] tracking-widest uppercase mb-1">Next claim available</p>
                @php
                    $nextOrder = $orders->where('status','Active')->sortByDesc('last_claimed_at')->first();
                    $nextAt    = $nextOrder && $nextOrder->last_claimed_at
                        ? $nextOrder->nextClaimAt()
                        : null;
                @endphp
                <p class="text-[#00c9a7] text-sm font-medium">
                    {{ $nextAt ? '9:00 AM ¡¤ ' . $nextAt->format('d M Y') : '9:00 AM tomorrow' }}
                </p>
            </div>
        @endif
    </form>

    {{-- Tabs --}}
    <div class="w-full flex mt-4 rounded-lg overflow-hidden" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <button id="tab-active" onclick="switchTab('active')"
                class="flex-1 py-2.5 text-xs tracking-widest uppercase font-medium transition-colors tab-btn tab-on">
            Active
        </button>
        <button id="tab-inactive" onclick="switchTab('inactive')"
                class="flex-1 py-2.5 text-xs tracking-widest uppercase font-medium transition-colors tab-btn tab-off">
            Completed
        </button>
    </div>

    {{-- Active Orders --}}
    <div id="panel-active" class="w-full">
        @forelse($orders->where('status', 'Active') as $order)
        @php
            $imgFile  = $packageImages[$order->package] ?? null;
            $imgSrc   = $imgFile ? asset('images/packages/' . $imgFile) : asset('images/8.jpeg');
            $progress = $order->totals > 0 ? min(100, round(($order->earnings / $order->totals) * 100)) : 0;
        @endphp
        <div class="w-full mt-3 rounded-xl overflow-hidden" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <div class="flex gap-3 p-3">
                <div class="relative w-24 h-20 shrink-0 rounded-lg overflow-hidden">
                    <img src="{{ $imgSrc }}" class="w-full h-full object-cover" alt="{{ $order->package }}">
                    <div class="absolute inset-0" style="background:rgba(5,15,26,0.3);"></div>
                </div>
                <div class="flex-1 space-y-1">
                    <p class="text-white font-semibold tracking-widest uppercase text-xs">{{ $order->package }}</p>
                    <div class="grid grid-cols-2 gap-x-3 gap-y-0.5 text-[10px]">
                        <span class="text-[#64899a]">Cycle: <span class="text-white">{{ $order->cycle }}d</span></span>
                        <span class="text-[#64899a]">Daily: <span class="text-[#00c9a7]">Kes {{ number_format($order->daily, 0) }}</span></span>
                        <span class="text-[#64899a]">Total: <span class="text-white">Kes {{ number_format($order->totals, 0) }}</span></span>
                        <span class="text-[#64899a]">Earned: <span class="text-[#22c55e]">Kes {{ number_format($order->earnings, 0) }}</span></span>
                    </div>
                    @if($order->canClaim())
                        <p class="text-[#00c9a7] text-[10px] uppercase tracking-widest font-medium">&#x25cf; Ready to claim</p>
                    @else
                        <p class="text-[#64899a] text-[10px]">
                            Available at 9:00 AM ¡¤ {{ $order->nextClaimAt()->format('d M') }}
                        </p>
                    @endif
                </div>
            </div>
            {{-- Progress bar --}}
            <div class="px-3 pb-3">
                <div class="flex justify-between text-[9px] text-[#64899a] mb-1">
                    <span>Progress</span><span>{{ $progress }}%</span>
                </div>
                <div class="w-full h-1 rounded-full bg-[#1a3a4a]">
                    <div class="h-1 rounded-full bg-[#00c9a7] transition-all" style="width:{{ $progress }}%;"></div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center mt-10">
            <i class="fa-solid fa-layer-group text-[#1a3a4a] text-4xl mb-3"></i>
            <p class="text-[#64899a] tracking-widest uppercase text-xs">No active plans</p>
            <a href="{{ route('packages') }}" class="inline-block mt-3 px-5 py-2 rounded-lg bg-[#00c9a7] text-[#050f1a] text-xs font-semibold tracking-widest uppercase no-underline">Browse Plans</a>
        </div>
        @endforelse
    </div>

    {{-- Completed Orders --}}
    <div id="panel-inactive" class="w-full hidden">
        @forelse($orders->where('status', '!=', 'Active') as $order)
        @php
            $imgFile = $packageImages[$order->package] ?? null;
            $imgSrc  = $imgFile ? asset('images/packages/' . $imgFile) : asset('images/8.jpeg');
        @endphp
        <div class="w-full mt-3 rounded-xl overflow-hidden opacity-60" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <div class="flex gap-3 p-3">
                <div class="relative w-24 h-20 shrink-0 rounded-lg overflow-hidden">
                    <img src="{{ $imgSrc }}" class="w-full h-full object-cover grayscale" alt="{{ $order->package }}">
                </div>
                <div class="flex-1 space-y-1">
                    <p class="text-white font-semibold tracking-widest uppercase text-xs">{{ $order->package }}</p>
                    <div class="grid grid-cols-2 gap-x-3 gap-y-0.5 text-[10px]">
                        <span class="text-[#64899a]">Cycle: <span class="text-white">{{ $order->cycle }}d</span></span>
                        <span class="text-[#64899a]">Daily: <span class="text-white">Kes {{ number_format($order->daily, 0) }}</span></span>
                        <span class="text-[#64899a]">Earned: <span class="text-white">Kes {{ number_format($order->earnings, 0) }}</span></span>
                    </div>
                    <p class="text-[#22c55e] text-[10px] uppercase tracking-widest">&#x25cf; Cycle complete</p>
                </div>
            </div>
        </div>
        @empty
        <p class="text-[#64899a] text-center mt-10 tracking-widest uppercase text-xs">No completed orders</p>
        @endforelse
    </div>

</div>

@include('partials.bottom-nav')

@push('styles')
<style>
    .tab-on  { background:#00c9a7; color:#050f1a; }
    .tab-off { background:transparent; color:#64899a; }
</style>
@endpush

@push('scripts')
<script>
    function switchTab(tab) {
        ['active','inactive'].forEach(function(k) {
            const panel = document.getElementById('panel-' + k);
            const btn   = document.getElementById('tab-' + k);
            if (k === tab) {
                panel.classList.remove('hidden');
                btn.classList.add('tab-on'); btn.classList.remove('tab-off');
            } else {
                panel.classList.add('hidden');
                btn.classList.remove('tab-on'); btn.classList.add('tab-off');
            }
        });
    }

    const claimForm = document.getElementById('claim-form');
    if (claimForm) {
        claimForm.addEventListener('submit', function() {
            const btn = document.getElementById('claim-btn');
            if (!btn) return;
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';
        });
    }
</script>
@endpush
@endsection
