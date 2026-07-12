@extends('layouts.admin')
@section('title', 'Wallets - Admin')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">User Wallets</h1>
    <span class="text-sm text-gray-500">{{ $wallets->total() }} wallet(s)</span>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.wallets') }}" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search by email..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72 focus:outline-none focus:border-blue-500">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    @if(request('search'))
        <a href="{{ route('admin.wallets') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-right">Balance</th>
                <th class="px-4 py-3 text-right">Deposits</th>
                <th class="px-4 py-3 text-right">Withdrawals</th>
                <th class="px-4 py-3 text-right">Referral</th>
                <th class="px-4 py-3 text-right">Bonus</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($wallets as $wallet)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $wallet->ID }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $wallet->email }}</td>
                <td class="px-4 py-3 text-right font-semibold text-green-600">{{ number_format($wallet->balance, 2) }}</td>
                <td class="px-4 py-3 text-right text-gray-600">{{ number_format($wallet->deposit, 2) }}</td>
                <td class="px-4 py-3 text-right text-gray-600">{{ number_format($wallet->withdraw, 2) }}</td>
                <td class="px-4 py-3 text-right text-gray-600">{{ number_format($wallet->referral, 2) }}</td>
                <td class="px-4 py-3 text-right text-gray-600">{{ number_format($wallet->bonus, 2) }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('admin.wallets.edit', $wallet->ID) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg">
                        Edit
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">No wallets found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($wallets->hasPages())
<div class="mt-4">{{ $wallets->links() }}</div>
@endif

@endsection
