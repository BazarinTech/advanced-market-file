@extends('layouts.admin')
@section('title', 'Password Recovery Requests - Admin')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-4">Password Recovery Requests ({{ $requests->count() }})</h1>

@if(session('success'))
    <div class="bg-green-50 border border-green-300 text-green-700 text-sm px-4 py-2 rounded mb-4">{{ session('success') }}</div>
@endif

{{-- Filter tabs --}}
<div class="flex gap-2 mb-4">
    <button onclick="filterTable('all')" id="tab-all"
        class="px-4 py-1.5 rounded text-sm font-medium bg-gray-800 text-white">All</button>
    <button onclick="filterTable('Pending')" id="tab-Pending"
        class="px-4 py-1.5 rounded text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">Pending</button>
    <button onclick="filterTable('Resolved')" id="tab-Resolved"
        class="px-4 py-1.5 rounded text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">Resolved</button>
</div>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm text-left" id="recovery-table">
        <thead class="bg-orange-600 text-white">
            <tr>
                <th class="px-3 py-3">#</th>
                <th class="px-3 py-3">Name</th>
                <th class="px-3 py-3">Email</th>
                <th class="px-3 py-3">Phone</th>
                <th class="px-3 py-3">Network</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Submitted</th>
                <th class="px-3 py-3">Resolved At</th>
                <th class="px-3 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($requests as $i => $r)
            <tr class="hover:bg-gray-50" data-status="{{ $r->status }}">
                <td class="px-3 py-2">{{ $i + 1 }}</td>
                <td class="px-3 py-2 font-medium">{{ $r->name }}</td>
                <td class="px-3 py-2">{{ $r->email }}</td>
                <td class="px-3 py-2">{{ $r->phone }}</td>
                <td class="px-3 py-2">{{ $r->network }}</td>
                <td class="px-3 py-2">
                    @if($r->status === 'Pending')
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Resolved</span>
                    @endif
                </td>
                <td class="px-3 py-2 text-xs text-gray-500">{{ $r->created_at->format('d M Y, H:i') }}</td>
                <td class="px-3 py-2 text-xs text-gray-500">
                    {{ $r->resolved_at ? $r->resolved_at->format('d M Y, H:i') : '—' }}
                </td>
                <td class="px-3 py-2">
                    @if($r->status === 'Pending')
                    <form action="{{ route('admin.recovery-requests.resolve', $r->id) }}" method="post" class="inline">
                        @csrf
                        <button class="px-2 py-1 rounded text-xs text-white bg-green-600 hover:bg-green-700">
                            Mark Resolved
                        </button>
                    </form>
                    @else
                        <span class="text-xs text-gray-400">Done</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-6 text-center text-gray-400">No recovery requests yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function filterTable(status) {
    document.querySelectorAll('#recovery-table tbody tr[data-status]').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
        btn.classList.remove('bg-gray-800','text-white');
        btn.classList.add('bg-gray-200','text-gray-700');
    });
    const active = document.getElementById('tab-' + status);
    if (active) {
        active.classList.remove('bg-gray-200','text-gray-700');
        active.classList.add('bg-gray-800','text-white');
    }
}
</script>

@endsection
