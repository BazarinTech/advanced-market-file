@extends('layouts.app')
@section('title', 'Create Account - ' . config('app.name'))
@section('content')

<div class="flex flex-col items-center justify-center w-full min-h-screen px-6 py-10" style="background:#050f1a;">
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-[#00c9a7] flex items-center justify-center mx-auto mb-4">
            <x-icon name="chart-line" class="text-[#050f1a] text-2xl" />
        </div>
        <p class="text-white text-2xl font-bold tracking-widest uppercase">Trade-Swing</p>
        <p class="text-[#64899a] text-xs tracking-[0.3em] uppercase mt-1">Create your account</p>
    </div>

    @if($errors->any())
        <div class="w-full px-4 py-2 rounded-lg mb-4 text-sm text-[#ef4444] bg-[#ef4444]/10 border border-[#ef4444]/20">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <form action="{{ route('register') }}" method="post" id="register-form" class="w-full flex flex-col gap-3">
        @csrf
        <div class="rounded-xl px-4 py-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="your@email.com"
                   class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                   required>
        </div>
        <div class="rounded-xl px-4 py-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Phone Number</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   placeholder="07xxxxxxxx"
                   class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                   required>
        </div>
        <div class="rounded-xl px-4 py-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Country</label>
            <select name="country" class="w-full bg-transparent outline-none text-sm text-white" required
                    style="background:#0d1f35;">
                <option value="254" style="background:#0d1f35;" {{ old('country')=='254'?'selected':'' }}>Kenya</option>
                <option value="256" style="background:#0d1f35;" {{ old('country')=='256'?'selected':'' }}>Uganda</option>
                <option value="255" style="background:#0d1f35;" {{ old('country')=='255'?'selected':'' }}>Tanzania</option>
                <option value="250" style="background:#0d1f35;" {{ old('country')=='250'?'selected':'' }}>Other</option>
            </select>
        </div>
        <div class="rounded-xl px-4 py-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Referral Code</label>
            <input type="text" name="ref" value="{{ $ref }}"
                   class="w-full bg-transparent outline-none text-sm text-[#64899a]"
                   readonly required>
        </div>
        <div class="rounded-xl px-4 py-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Password</label>
            <input type="password" name="password"
                   placeholder="Min. 8 characters"
                   class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                   required>
        </div>
        <div class="rounded-xl px-4 py-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation"
                   placeholder="Repeat password"
                   class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                   required>
        </div>
        <button type="submit" id="register-btn"
                class="w-full bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-bold py-3.5 rounded-xl tracking-[0.2em] uppercase text-sm mt-2">
            Create Account
        </button>
        <div class="text-center mt-1">
            <a href="{{ route('login') }}" class="text-[#64899a] text-xs tracking-widests uppercase hover:text-[#00c9a7]">
                Already have an account? Sign In
            </a>
        </div>
    </form>
</div>

{{-- Page Loader --}}
<div id="page-loader" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(5,15,26,0.93);" class="flex flex-col items-center justify-center">
    <div class="w-14 h-14 rounded-full border-4 border-[#1a3a4a] border-t-[#00c9a7] animate-spin mb-5"></div>
    <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase">Creating account...</p>
</div>

@push('scripts')
<script>
document.getElementById('register-form').addEventListener('submit', function() {
    document.getElementById('page-loader').style.display = 'flex';
    const btn = document.getElementById('register-btn');
    btn.disabled = true;
    btn.textContent = 'Creating account...';
});
</script>
@endpush
@endsection
