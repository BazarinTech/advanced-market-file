<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>@yield('title', 'Admin - ' . config('app.name'))</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside id="sidebar" class="bg-blue-700 text-white flex flex-col w-56 min-h-screen p-4 transition-all duration-300">
        <div class="flex items-center justify-between border-b border-blue-500 pb-3 mb-4">
            <span class="text-2xl font-bold">Admin</span>
            <button id="close" class="text-2xl font-bold lg:hidden">&times;</button>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="wallet" class="w-5" /> Dashboard
        </a>
        <a href="{{ route('admin.deposits') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="money" class="w-5" /> Deposits
        </a>
        <a href="{{ route('admin.withdrawals') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="money-bill-trend-up" class="w-5" /> Withdrawals
        </a>
        <a href="{{ route('admin.users') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="users" class="w-5" /> Users
        </a>
        <a href="{{ route('admin.recovery-requests') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="key" class="w-5" /> Recovery Requests
        </a>
        <a href="{{ route('admin.packages') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="box" class="w-5" /> Packages
        </a>
        <a href="{{ route('admin.withdrawal-accounts') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="id-card" class="w-5" /> W. Accounts
        </a>
        <a href="{{ route('admin.wallets') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="wallet" class="w-5" /> Wallets
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="ticket" class="w-5" /> Coupons
        </a>
        <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1">
            <x-icon name="gear" class="w-5" /> Settings
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-auto">
            @csrf
            <button class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-red-300 text-lg w-full text-left">
                <x-icon name="right-from-bracket" class="w-5" /> Logout
            </button>
        </form>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col" id="main-content">
        <header class="bg-white shadow flex items-center px-4 py-3">
            <button id="btn-hide" class="text-xl mr-3 lg:hidden"><x-icon name="bars" /></button>
            <span class="font-bold text-green-600 text-lg">Bazarin Technologies</span>
        </header>

        <main class="p-6 flex-1">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 rounded px-4 py-3 mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 rounded px-4 py-3 mb-4">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const btnHide = document.getElementById('btn-hide');
    const closeBtn = document.getElementById('close');

    if (window.innerWidth < 1024) {
        sidebar.classList.add('hidden');
        btnHide.classList.remove('hidden');
    }

    btnHide.addEventListener('click', () => {
        sidebar.classList.remove('hidden');
        sidebar.classList.add('fixed', 'inset-y-0', 'left-0', 'z-50');
    });

    closeBtn.addEventListener('click', () => {
        sidebar.classList.add('hidden');
        sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-50');
    });
</script>
@stack('scripts')
</body>
</html>
