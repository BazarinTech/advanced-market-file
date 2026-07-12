@extends('layouts.admin')
@section('title', 'Withdrawals - Admin')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-4">Withdrawals</h1>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-green-600 text-white">
            <tr>
                <th class="px-3 py-3">#</th>
                <th class="px-3 py-3">ID</th>
                <th class="px-3 py-3">Email</th>
                <th class="px-3 py-3">Amount</th>
                <th class="px-3 py-3">Rec. Amount</th>
                <th class="px-3 py-3">Phone</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Date</th>
                <th class="px-3 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($withdrawals as $i => $w)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2">{{ $i + 1 }}</td>
                <td class="px-3 py-2">{{ $w->ID }}</td>
                <td class="px-3 py-2">{{ $w->email }}</td>
                <td class="px-3 py-2">Kes {{ number_format($w->amount, 2) }}</td>
                <td class="px-3 py-2">Kes {{ number_format($w->RecAmount, 2) }}</td>
                <td class="px-3 py-2">{{ $w->phone }}</td>
                <td class="px-3 py-2 font-semibold {{ in_array($w->status, ['Success','Approved']) ? 'text-green-600' : 'text-red-500' }}">
                    {{ $w->status }}
                </td>
                <td class="px-3 py-2 text-xs text-gray-500">{{ $w->date }}</td>
                <td class="px-3 py-2 flex flex-wrap gap-1">
                    @if($w->status === 'Pending')
                    <form action="{{ route('admin.withdrawals.approve', $w->ID) }}" method="post" class="inline">
                        @csrf
                        <button class="px-2 py-1 rounded text-xs text-white bg-green-600 hover:bg-green-700">Approve</button>
                    </form>
                    <form action="{{ route('admin.withdrawals.reject', $w->ID) }}" method="post" class="inline">
                        @csrf
                        <button class="px-2 py-1 rounded text-xs text-white bg-red-500 hover:bg-red-600">Reject</button>
                    </form>
                    @else
                    <span class="text-gray-400 text-xs">{{ $w->status }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="px-3 py-4 text-center text-gray-400">No withdrawals found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($withdrawals->hasPages())
<div class="mt-4">{{ $withdrawals->links() }}</div>
@endif
@endsection
