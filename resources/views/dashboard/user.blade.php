@extends('layouts.app')
@section('title', 'Profile Settings - ' . config('app.name'))
@section('content')

{{-- Top Bar --}}
<div class="w-full flex items-center h-14 px-4 bg-[#0a1929] border-b border-[#1a3a4a]">
    <a href="{{ route('account') }}" class="text-[#64899a] hover:text-white text-lg mr-4 no-underline">
        <x-icon name="arrow-left" />
    </a>
    <p class="flex-1 text-center text-white text-xs font-light tracking-[0.3em] uppercase pr-8">Profile Settings</p>
</div>

@if(session('success'))
    <div class="mx-4 mt-3 px-4 py-2 rounded-lg text-sm text-[#22c55e] bg-[#22c55e]/10 border border-[#22c55e]/20">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mx-4 mt-3 px-4 py-2 rounded-lg text-sm text-[#ef4444] bg-[#ef4444]/10 border border-[#ef4444]/20">{{ session('error') }}</div>
@endif

<div class="w-full px-4 mt-4 flex flex-col gap-2">

    {{-- Info rows --}}
    <div class="rounded-xl overflow-hidden" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <div class="flex items-center justify-between px-4 py-4 border-b border-[#1a3a4a]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#00c9a7]/10 flex items-center justify-center shrink-0">
                    <x-icon name="envelope" class="text-[#00c9a7] text-sm" />
                </div>
                <div>
                    <p class="text-[#64899a] text-[10px] tracking-widest uppercase">Email</p>
                    <p class="text-white text-sm font-light mt-0.5">{{ $user->email }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between px-4 py-4 border-b border-[#1a3a4a]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#00c9a7]/10 flex items-center justify-center shrink-0">
                    <x-icon name="phone" class="text-[#00c9a7] text-sm" />
                </div>
                <div>
                    <p class="text-[#64899a] text-[10px] tracking-widest uppercase">Phone</p>
                    <p class="text-white text-sm font-light mt-0.5">{{ $user->phone }}</p>
                </div>
            </div>
        </div>
        <button id="btn" onclick="openPassModal()"
                class="w-full flex items-center justify-between px-4 py-4 text-left hover:bg-[#0a1929] transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#f59e0b]/10 flex items-center justify-center shrink-0">
                    <x-icon name="lock" class="text-[#f59e0b] text-sm" />
                </div>
                <div>
                    <p class="text-white text-sm font-light">Change Password</p>
                    <p class="text-[#64899a] text-[10px] mt-0.5">Update your login password</p>
                </div>
            </div>
            <x-icon name="angle-right" class="text-[#1a3a4a] text-sm" />
        </button>
    </div>

    {{-- Sign out --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-colors"
                style="background:#1a0a0a; border:1px solid #3a1a1a;">
            <div class="w-9 h-9 rounded-lg bg-[#ef4444]/10 flex items-center justify-center shrink-0">
                <x-icon name="right-from-bracket" class="text-[#ef4444] text-sm" />
            </div>
            <span class="text-[#ef4444] text-sm tracking-wide font-light">Sign Out</span>
        </button>
    </form>
</div>

{{-- Password Modal --}}
<div id="passModal" class="hidden fixed inset-0 bg-black/60 items-center justify-center z-50 px-4">
    <div class="w-full max-w-sm rounded-2xl p-6 relative" style="background:#0d1f35; border:1px solid #1a3a4a;">
        <button onclick="closePassModal()" class="absolute top-4 right-4 text-[#64899a] hover:text-white text-xl">&times;</button>
        <p class="text-white text-xs tracking-[0.3em] uppercase text-center mb-5">Update Password</p>
        <form action="{{ route('user.update') }}" method="post" class="flex flex-col gap-3">
            @csrf
            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Current Password</label>
                <input type="password" name="prevPass" placeholder="••••••••"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]">
            </div>
            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">New Password (min 8)</label>
                <input type="password" name="newPass" placeholder="••••••••"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]">
            </div>
            <div class="rounded-lg px-4 py-3" style="background:#0a1929; border:1px solid #1a3a4a;">
                <label class="text-[#64899a] text-[9px] tracking-[0.3em] uppercase block mb-1">Confirm New Password</label>
                <input type="password" name="conPass" placeholder="••••••••"
                       class="w-full bg-transparent outline-none text-sm text-white placeholder-[#1a3a4a]">
            </div>
            <button name="update" type="submit"
                    class="w-full bg-[#00c9a7] hover:bg-[#00a88a] text-[#050f1a] font-bold py-3 rounded-lg tracking-widest uppercase text-xs mt-1">
                Update Password
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openPassModal()  { const m = document.getElementById('passModal'); m.classList.remove('hidden'); m.style.display = 'flex'; }
    function closePassModal() { const m = document.getElementById('passModal'); m.classList.add('hidden'); m.style.display = ''; }
    document.getElementById('passModal').addEventListener('click', function(e) { if (e.target === this) closePassModal(); });
</script>
@endpush
@endsection
