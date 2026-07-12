@extends('layouts.admin')
@section('title', 'Users - Admin')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-4">Users ({{ $users->total() }})</h1>

@if(session('success'))
    <div class="bg-green-50 border border-green-300 text-green-700 text-sm px-4 py-2 rounded mb-4">{{ session('success') }}</div>
@endif

{{-- Search --}}
<form method="GET" action="{{ route('admin.users') }}" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ $search }}"
           placeholder="Search by email, phone or ID..."
           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-green-500">
    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">Search</button>
    @if($search)
        <a href="{{ route('admin.users') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-green-600 text-white">
            <tr>
                <th class="px-3 py-3">#</th>
                <th class="px-3 py-3">ID</th>
                <th class="px-3 py-3">Email</th>
                <th class="px-3 py-3">Phone</th>
                <th class="px-3 py-3">Country</th>
                <th class="px-3 py-3">Upline</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Role</th>
                <th class="px-3 py-3">Date</th>
                <th class="px-3 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $u)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2">{{ $users->firstItem() + $loop->index }}</td>
                <td class="px-3 py-2">{{ $u->ID }}</td>
                <td class="px-3 py-2">{{ $u->email }}</td>
                <td class="px-3 py-2">{{ $u->phone }}</td>
                <td class="px-3 py-2">{{ $u->country }}</td>
                <td class="px-3 py-2 text-xs text-gray-500">
                    {{ $uplineEmails[$u->refer] ?? ($u->refer ? '#'.$u->refer : '—') }}
                </td>
                <td class="px-3 py-2 font-semibold {{ $u->status === 'Active' ? 'text-green-600' : 'text-red-500' }}">{{ $u->status }}</td>
                <td class="px-3 py-2">{{ $u->role }}</td>
                <td class="px-3 py-2 text-xs text-gray-500">{{ $u->date }}</td>
                <td class="px-3 py-2 flex flex-wrap gap-1">
                    {{-- Toggle Status --}}
                    <form action="{{ route('admin.users.status', $u->ID) }}" method="post" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="{{ $u->status === 'Active' ? 'Inactive' : 'Active' }}">
                        <button class="px-2 py-1 rounded text-xs text-white {{ $u->status === 'Active' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-600 hover:bg-green-700' }}">
                            {{ $u->status === 'Active' ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    {{-- Make Admin --}}
                    @if($u->role !== 'admin')
                    <form action="{{ route('admin.users.make-admin', $u->ID) }}" method="post" class="inline">
                        @csrf
                        <button class="px-2 py-1 rounded text-xs text-white bg-blue-600 hover:bg-blue-700">Make Admin</button>
                    </form>
                    @endif
                    {{-- Reset Password --}}
                    <button onclick="openReset('{{ $u->ID }}', '{{ $u->email }}')"
                            class="px-2 py-1 rounded text-xs text-white bg-orange-500 hover:bg-orange-600">
                        Reset Password
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="px-3 py-4 text-center text-gray-400">No users found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($users->hasPages())
<div class="mt-4">
    {{ $users->links() }}
</div>
@endif

{{-- Reset Password Modal --}}
<div id="resetModal" class="hidden fixed inset-0 bg-black/50 items-center justify-center z-50 px-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 relative">
        <button onclick="closeReset()" class="absolute top-3 right-4 text-xl text-gray-400 hover:text-red-500">&times;</button>

        <h2 class="text-base font-semibold text-gray-700 mb-1">Reset Password</h2>
        <p id="resetUserEmail" class="text-xs text-gray-400 mb-5"></p>

        <form id="resetForm" method="POST" class="flex flex-col gap-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase tracking-wide">New Password</label>
                <input type="password" name="password" minlength="6"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-orange-500"
                       placeholder="Min. 6 characters" required>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 uppercase tracking-wide">Confirm Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-orange-500"
                       placeholder="Repeat new password" required>
            </div>
            <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg text-sm mt-1">
                Reset Password
            </button>
        </form>
    </div>
</div>

<script>
    function openReset(userId, email) {
        const modal = document.getElementById('resetModal');
        document.getElementById('resetUserEmail').textContent = email;
        document.getElementById('resetForm').action = '/admin/users/' + userId + '/reset-password';
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
    function closeReset() {
        const modal = document.getElementById('resetModal');
        modal.classList.add('hidden');
        modal.style.display = '';
    }
    // Close on backdrop click
    document.getElementById('resetModal').addEventListener('click', function(e) {
        if (e.target === this) closeReset();
    });
</script>
@endsection
