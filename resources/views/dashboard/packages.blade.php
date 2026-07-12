@extends('layouts.app')
@section('title', 'Investment Plans - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center h-14 px-4 bg-[#0a1929] border-b border-[#1a3a4a]">
    <div class="w-7 h-7 rounded bg-[#00c9a7] flex items-center justify-center mr-3">
        <x-icon name="layer-group" class="text-[#050f1a] text-xs" />
    </div>
    <p class="text-white text-xs font-light tracking-[0.3em] uppercase">Investment Plans</p>
    <div class="ml-auto text-right">
        <p class="text-[#64899a] text-[10px] tracking-widest uppercase">Balance</p>
        <p class="text-[#00c9a7] text-xs font-medium">Kes {{ number_format($earnings->balance, 2) }}</p>
    </div>
</div>

@if(session('success'))
    <div class="mx-4 mt-3 px-4 py-2 rounded-lg text-sm text-[#22c55e] bg-[#22c55e]/10 border border-[#22c55e]/20">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mx-4 mt-3 px-4 py-2 rounded-lg text-sm text-[#ef4444] bg-[#ef4444]/10 border border-[#ef4444]/20">
        {{ session('error') }}
    </div>
@endif

{{-- Info strip --}}
<div class="mx-4 mt-4 rounded-lg px-4 py-3 flex items-start gap-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
    <x-icon name="circle-info" class="text-[#00c9a7] mt-0.5 text-sm" />
    <p class="text-[#64899a] text-xs leading-relaxed">
        Select a plan to invest. Earnings are claimable daily from <span class="text-[#00c9a7]">9:00 AM</span> each day.
        Multiple plans can be held simultaneously.
    </p>
</div>

{{-- Package cards --}}
<div class="w-full px-4 mt-4 flex flex-col gap-4 pb-28">
    @foreach($packages as $pkg)
    @php $roi = $pkg->amount > 0 ? round(($pkg->daily * $pkg->days / $pkg->amount) * 100) : 0; @endphp
    <form action="{{ route('home.buy') }}" method="post" class="package-form">
        @csrf
        <input type="hidden" name="package" value="{{ $pkg->id }}">
        <div class="rounded-xl overflow-hidden" style="background:#0d1f35; border:1px solid #1a3a4a;">
            {{-- Package header --}}
            <div class="relative">
                <img src="{{ $pkg->imageUrl() }}" class="w-full object-cover" style="height:120px;" alt="{{ $pkg->name }}">
                <div class="absolute inset-0" style="background:linear-gradient(to bottom, rgba(5,15,26,0.2), rgba(5,15,26,0.85));"></div>
                <div class="absolute bottom-0 left-0 right-0 px-4 pb-3 flex items-end justify-between">
                    <p class="text-white font-semibold tracking-widest uppercase text-sm">{{ $pkg->name }}</p>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium text-[#050f1a] bg-[#00c9a7]">
                        {{ $roi }}% ROI
                    </span>
                </div>
            </div>
            {{-- Package details --}}
            <div class="px-4 py-4">
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="text-center">
                        <p class="text-[#64899a] text-[9px] tracking-widest uppercase">Price</p>
                        <p class="text-[#00c9a7] text-sm font-semibold mt-0.5">Kes {{ number_format($pkg->amount, 0) }}</p>
                    </div>
                    <div class="text-center border-l border-r border-[#1a3a4a]">
                        <p class="text-[#64899a] text-[9px] tracking-widest uppercase">Daily</p>
                        <p class="text-white text-sm font-semibold mt-0.5">Kes {{ number_format($pkg->daily, 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[#64899a] text-[9px] tracking-widest uppercase">{{ $pkg->days }}d Total</p>
                        <p class="text-[#22c55e] text-sm font-semibold mt-0.5">Kes {{ number_format($pkg->daily * $pkg->days, 0) }}</p>
                    </div>
                </div>
                <button type="submit"
                        class="buy-btn w-full py-3 rounded-lg text-xs font-semibold tracking-widest uppercase transition-opacity
                               {{ $earnings->balance >= $pkg->amount ? 'text-[#050f1a] bg-[#00c9a7] hover:bg-[#00a88a]' : 'text-[#64899a] cursor-not-allowed' }}"
                        {{ $earnings->balance < $pkg->amount ? 'disabled' : '' }}>
                    {{ $earnings->balance >= $pkg->amount ? 'Activate Plan' : 'Insufficient Balance' }}
                </button>
            </div>
        </div>
    </form>
    @endforeach
</div>

@include('partials.bottom-nav')

@push('scripts')
<script>
document.querySelectorAll('.package-form').forEach(function(form) {
    form.addEventListener('submit', function() {
        const btn = form.querySelector('.buy-btn');
        if (btn.disabled) return;
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin w-4 h-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>Processing…`;
    });
});
</script>
@endpush
@endsection
