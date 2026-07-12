@extends('layouts.app')
@section('title', 'Sign In - ' . config('app.name'))
@section('content')

<div class="flex flex-col items-center justify-center w-full min-h-screen px-6" style="background:#050f1a;">
    {{-- Logo --}}
    <div class="text-center mb-10">
        <div class="w-14 h-14 rounded-2xl bg-[#00c9a7] flex items-center justify-center mx-auto mb-4">
            <x-icon name="chart-line" class="text-[#050f1a] text-2xl" />
        </div>
        <p class="text-white text-2xl font-bold tracking-widest uppercase">Trade-Swing</p>
        <p class="text-[#64899a] text-xs tracking-[0.3em] uppercase mt-1">Welcome back</p>
    </div>

    @if(session('success'))
        <div class="w-full px-4 py-2 rounded-lg mb-4 text-sm text-[#22c55e] bg-[#22c55e]/10 border border-[#22c55e]/20">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="w-full px-4 py-2 rounded-lg mb-4 text-sm text-[#ef4444] bg-[#ef4444]/10 border border-[#ef4444]/20">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('login') }}" method="post" id="login-form" class="w-full flex flex-col gap-4">
        @csrf
        <div class="rounded-xl px-4 py-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="your@email.com"
                   class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                   required>
        </div>
        <div class="rounded-xl px-4 py-3" style="background:#0d1f35; border:1px solid #1a3a4a;">
            <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Password</label>
            <input type="password" name="password"
                   placeholder="••••••••"
                   class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]"
                   required>
        </div>
        <button type="submit" id="login-btn"
                class="w-full bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-bold py-3.5 rounded-xl tracking-[0.2em] uppercase text-sm mt-2 flex items-center justify-center gap-2">
            Sign In
        </button>
        <div class="flex items-center justify-between mt-1">
            <a href="{{ route('forgot') }}" class="text-[#64899a] text-xs tracking-widest uppercase hover:text-[#00c9a7]">
                Forgot Password?
            </a>
            <a href="{{ route('register') }}" class="text-[#00c9a7] text-xs tracking-widest uppercase hover:text-white">
                Create Account
            </a>
        </div>
    </form>
</div>

{{-- Page Loader --}}
<div id="page-loader" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(5,15,26,0.93);" class="flex flex-col items-center justify-center">
    <div class="w-14 h-14 rounded-full border-4 border-[#1a3a4a] border-t-[#00c9a7] animate-spin mb-5"></div>
    <p class="text-[#64899a] text-[10px] tracking-[0.3em] uppercase">Signing in...</p>
</div>

@push('scripts')
<script>
document.getElementById('login-form').addEventListener('submit', function() {
    document.getElementById('page-loader').style.display = 'flex';
    const btn = document.getElementById('login-btn');
    btn.disabled = true;
    btn.innerHTML = 'Signing in...';
});
</script>
@endpush
@endsection
