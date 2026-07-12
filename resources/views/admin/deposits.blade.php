@extends('layouts.admin')
@section('title', 'Deposits - Admin')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-4">Deposits</h1>

{{-- Manual Deposit Form --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <h3 class="text-blue-600 font-bold text-lg mb-3">Manual Deposit</h3>
    <form action="{{ route('admin.deposits.store') }}" method="post" class="flex flex-col gap-3 max-w-md">
        @csrf
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Amount</label>
            <input type="number" step="0.01" name="amount" placeholder="Enter Amount (Kes)"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                   required>
        </div>
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Phone Number</label>
            <input type="tel" name="phone" placeholder="07xxxxxxxxx"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                   required>
        </div>
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Email</label>
            <input type="email" name="email" placeholder="Enter Email"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                   required>
        </div>
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Transaction ID</label>
            <input type="text" name="transactionID" placeholder="Enter Transaction ID"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                   required>
        </div>
        <button type="submit" class="w-1/2 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 rounded-lg">
            Deposit
        </button>
    </form>
</div>

<h2 class="text-xl font-bold text-teal-600 mb-3">All Deposits</h2>
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-green-600 text-white">
            <tr>
                <th class="px-3 py-3">#</th>
                <th class="px-3 py-3">ID</th>
                <th class="px-3 py-3">Email</th>
                <th class="px-3 py-3">Amount</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Phone</th>
                <th class="px-3 py-3">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($deposits as $i => $d)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2">{{ $i + 1 }}</td>
                <td class="px-3 py-2">{{ $d->ID }}</td>
                <td class="px-3 py-2">{{ $d->email }}</td>
                <td class="px-3 py-2">Kes {{ number_format($d->amount, 2) }}</td>
                <td class="px-3 py-2 font-semibold {{ $d->status === 'Success' ? 'text-green-600' : 'text-red-500' }}">{{ $d->status }}</td>
                <td class="px-3 py-2">{{ $d->phone }}</td>
                <td class="px-3 py-2 text-xs text-gray-500">{{ $d->date }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-3 py-4 text-center text-gray-400">No deposits found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($deposits->hasPages())
<div class="mt-4">{{ $deposits->links() }}</div>
@endif
@endsection
