@extends('layouts.admin')
@section('title', 'Withdrawal Accounts - Admin')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Withdrawal Accounts</h1>
    <span class="text-sm text-gray-500">{{ $accounts->count() }} account(s)</span>
</div>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Phone</th>
                <th class="px-4 py-3 text-left">Last Updated</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($accounts as $i => $account)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $account->email }}</td>
                <td class="px-4 py-3 font-semibold text-gray-800">{{ $account->name }}</td>
                <td class="px-4 py-3 text-green-600 font-semibold">{{ $account->phone }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $account->updated_at ? $account->updated_at->diffForHumans() : '—' }}</td>
                <td class="px-4 py-3">
                    <button onclick="openEdit({{ $account->id }}, '{{ addslashes($account->name) }}', '{{ $account->phone }}')"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg">
                        Edit
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No withdrawal accounts found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($accounts->hasPages())
<div class="mt-4">{{ $accounts->links() }}</div>
@endif

{{-- Edit Modal --}}
<div id="edit-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 mx-4">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Edit Withdrawal Account</h2>
        <form id="edit-form" method="POST" class="flex flex-col gap-4">
            @csrf
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Full Name (as on M-Pesa)</label>
                <input type="text" name="name" id="edit-name"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                    required>
            </div>
            <div>
                <label class="text-xs text-gray-500 mb-1 block">M-Pesa Phone Number</label>
                <input type="tel" name="phone" id="edit-phone"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                    required>
            </div>
            <div class="flex gap-3 mt-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg text-sm">
                    Save Changes
                </button>
                <button type="button" onclick="closeEdit()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 rounded-lg text-sm">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const baseUrl = '{{ url("admin/withdrawal-accounts") }}';

    function openEdit(id, name, phone) {
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-phone').value = phone;
        document.getElementById('edit-form').action = baseUrl + '/' + id;
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    function closeEdit() {
        document.getElementById('edit-modal').classList.add('hidden');
    }

    document.getElementById('edit-modal').addEventListener('click', function(e) {
        if (e.target === this) closeEdit();
    });
</script>
@endpush

@endsection
