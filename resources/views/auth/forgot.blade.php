@extends('layouts.app')
@section('title', 'Forgot Password - ' . config('app.name'))
@section('content')

<div class="flex flex-col items-center justify-center w-full min-h-screen px-6 py-10" style="background:#050f1a;">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-[#00c9a7] flex items-center justify-center mx-auto mb-4">
            <x-icon name="lock" class="text-[#050f1a] text-2xl" />
        </div>
        <p class="text-white text-2xl font-bold tracking-widest uppercase">{{ config('app.name') }}</p>
        <p class="text-[#64899a] text-xs tracking-[0.3em] uppercase mt-1">Account Recovery</p>
    </div>

    @if(session('success'))
        <div class="w-full px-4 py-2 rounded-lg mb-4 text-sm text-[#22c55e] bg-[#22c55e]/10 border border-[#22c55e]/20">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="w-full px-4 py-2 rounded-lg mb-4 text-sm text-[#ef4444] bg-[#ef4444]/10 border border-[#ef4444]/20">
            @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    {{-- Instructions --}}
    <div class="w-full rounded-xl px-4 py-4 mb-4 flex items-start gap-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <x-icon name="circle-info" class="text-[#00c9a7] mt-0.5 text-sm shrink-0" />
        <div class="space-y-1.5">
            <p class="text-[#64899a] text-xs tracking-wide leading-relaxed">Fill in the form below with the details you used to create your account.</p>
            <p class="text-[#64899a] text-xs tracking-wide leading-relaxed">Our support team will verify your identity and assist you within 24 hours.</p>
            <p class="text-[#64899a] text-xs tracking-wide leading-relaxed">Alternatively, reach us directly via the contact link below.</p>
        </div>
    </div>

    {{-- Support Link --}}
    @if($support_url)
    <div class="w-full mb-4">
        <a href="{{ $support_url }}" target="_blank"
           class="w-full flex items-center justify-between rounded-xl px-4 py-3.5 no-underline transition-colors"
           style="background:#0d1f35; border:1px solid #1a3a4a;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#22c55e]/10 flex items-center justify-center shrink-0">
                    <x-icon name="whatsapp" class="text-[#22c55e] text-base" />
                </div>
                <span class="text-white text-sm font-light tracking-wide">Contact Support Now</span>
            </div>
            <x-icon name="angle-right" class="text-[#1a3a4a] text-sm" />
        </a>
    </div>
    @endif

    {{-- Recovery Form --}}
    <div class="w-full rounded-xl p-5 mb-6" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase mb-4">Submit Recovery Request</p>

        <form action="{{ route('forgot.submit') }}" method="POST" id="forgot-form" class="flex flex-col gap-3">
            @csrf

            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="As used on account"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                       required>
            </div>

            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="your@email.com"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                       required>
            </div>

            <div class="rounded-lg px-4 py-3 flex gap-3 items-start" style="background:#0a1929; border:1px solid #1a3a4a;">
                <div class="flex-1">
                    <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           placeholder="07xxxxxxxx"
                           class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                           required>
                </div>
                <div class="shrink-0 pt-5">
                    <select name="network"
                            class="bg-transparent outline-none text-xs tracking-wide text-[#64899a] rounded-lg px-2 py-1"
                            style="border:1px solid #1a3a4a; background:#0a1929;"
                            required>
                        <option value="" disabled {{ old('network') ? '' : 'selected' }}>Network</option>
                        <option value="Safaricom" style="background:#0a1929;" {{ old('network')=='Safaricom' ? 'selected' : '' }}>Safaricom</option>
                        <option value="Airtel"    style="background:#0a1929;" {{ old('network')=='Airtel'    ? 'selected' : '' }}>Airtel</option>
                        <option value="Telkom"    style="background:#0a1929;" {{ old('network')=='Telkom'    ? 'selected' : '' }}>Telkom</option>
                        <option value="Faiba"     style="background:#0a1929;" {{ old('network')=='Faiba'     ? 'selected' : '' }}>Faiba</option>
                    </select>
                </div>
            </div>

            <button type="submit" id="forgot-btn"
                    class="w-full bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-bold py-3.5 rounded-lg tracking-[0.2em] uppercase text-xs mt-1">
                Submit Request
            </button>
        </form>
    </div>

    <a href="{{ route('login') }}" class="text-[#64899a] text-xs tracking-widests uppercase hover:text-[#00c9a7]">
        &larr; Back to Login
    </a>

</div>

{{-- Page Loader --}}
<div id="page-loader" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(5,15,26,0.93);" class="flex flex-col items-center justify-center">
    <div class="w-14 h-14 rounded-full border-4 border-[#1a3a4a] border-t-[#00c9a7] animate-spin mb-5"></div>
    <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase">Submitting request...</p>
</div>

@push('scripts')
<script>
document.getElementById('forgot-form').addEventListener('submit', function() {
    document.getElementById('page-loader').style.display = 'flex';
    const btn = document.getElementById('forgot-btn');
    btn.disabled = true;
    btn.textContent = 'Submitting...';
});
</script>
@endpush
@endsection
