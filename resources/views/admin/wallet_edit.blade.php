@extends('layouts.admin')
@section('title', 'Edit Wallet - Admin')
@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.wallets') }}" class="text-gray-500 hover:text-gray-700">
        <x-icon name="arrow-left" />
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Edit Wallet</h1>
</div>

<div class="max-w-lg">
    {{-- User info --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4 flex items-center gap-4">
        <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center">
            <x-icon name="user" class="text-blue-600 text-xl" />
        </div>
        <div>
            <p class="font-semibold text-gray-800">{{ $wallet->email }}</p>
            @if($wallet->user)
            <p class="text-xs text-gray-400">Phone: {{ $wallet->user->phone }} &nbsp;|&nbsp; Status: {{ $wallet->user->status }}</p>
            @endif
        </div>
    </div>

    {{-- Edit form --}}
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm font-semibold text-gray-600 mb-4 border-b pb-2">Wallet Balances (Kes)</p>

        <form action="{{ route('admin.wallets.update', $wallet->ID) }}" method="POST" class="flex flex-col gap-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Balance</label>
                    <input type="number" name="balance" step="0.01" min="0"
                           value="{{ old('balance', $wallet->balance) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Total Deposits</label>
                    <input type="number" name="deposit" step="0.01" min="0"
                           value="{{ old('deposit', $wallet->deposit) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Total Withdrawals</label>
                    <input type="number" name="withdraw" step="0.01" min="0"
                           value="{{ old('withdraw', $wallet->withdraw) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Referral Earnings</label>
                    <input type="number" name="referral" step="0.01" min="0"
                           value="{{ old('referral', $wallet->referral) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bonus</label>
                    <input type="number" name="bonus" step="0.01" min="0"
                           value="{{ old('bonus', $wallet->bonus) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                           required>
                </div>
            </div>

            <div class="flex gap-3 mt-2">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg text-sm">
                    Save Changes
                </button>
                <a href="{{ route('admin.wallets') }}"
                   class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
