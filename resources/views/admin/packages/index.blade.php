@extends('layouts.admin')
@section('title', 'Packages - Admin')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-4">Investment Packages</h1>

{{-- Add Package Form --}}
<div class="bg-white rounded-xl shadow p-5 mb-6">
    <h3 class="text-blue-600 font-bold text-lg mb-4">Add New Package</h3>
    <form action="{{ route('admin.packages.store') }}" method="post" enctype="multipart/form-data"
          class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Package Name</label>
            <input type="text" name="name" placeholder="e.g. SFL 11"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500" required>
        </div>
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Price (Kes)</label>
            <input type="number" step="0.01" name="amount" placeholder="e.g. 500"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500" required>
        </div>
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Daily Income (Kes)</label>
            <input type="number" step="0.01" name="daily" placeholder="e.g. 40"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500" required>
        </div>
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Cycle (days)</label>
            <input type="number" name="days" placeholder="e.g. 20"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500" required>
        </div>
        <div>
            <label class="text-sm text-gray-600 mb-1 block">Thumbnail Image</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-end gap-4">
            <label class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                <input type="checkbox" name="active" value="1" checked class="w-4 h-4 accent-green-600">
                Active
            </label>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg">
                Add Package
            </button>
        </div>
    </form>
</div>

{{-- Packages List --}}
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-green-600 text-white">
            <tr>
                <th class="px-3 py-3">Image</th>
                <th class="px-3 py-3">Name</th>
                <th class="px-3 py-3">Price</th>
                <th class="px-3 py-3">Daily</th>
                <th class="px-3 py-3">Days</th>
                <th class="px-3 py-3">Total</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($packages as $pkg)
            <tr class="hover:bg-gray-50" x-data="{ editing: false }">
                <td class="px-3 py-2">
                    <img src="{{ $pkg->imageUrl() }}" class="w-14 h-14 rounded-lg object-cover" alt="">
                </td>
                <td class="px-3 py-2 font-semibold text-green-700">{{ $pkg->name }}</td>
                <td class="px-3 py-2">Kes {{ number_format($pkg->amount, 2) }}</td>
                <td class="px-3 py-2">Kes {{ number_format($pkg->daily, 2) }}</td>
                <td class="px-3 py-2">{{ $pkg->days }} days</td>
                <td class="px-3 py-2">Kes {{ number_format($pkg->daily * $pkg->days, 2) }}</td>
                <td class="px-3 py-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $pkg->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $pkg->active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-3 py-2 flex gap-2 flex-wrap">
                    {{-- Edit toggle --}}
                    <button onclick="document.getElementById('edit-{{ $pkg->id }}').classList.toggle('hidden')"
                            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg">
                        Edit
                    </button>
                    {{-- Delete --}}
                    <form action="{{ route('admin.packages.destroy', $pkg) }}" method="post" class="inline"
                          onsubmit="return confirm('Delete this package?')">
                        @csrf
                        <button class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg">Delete</button>
                    </form>
                </td>
            </tr>
            {{-- Inline Edit Row --}}
            <tr id="edit-{{ $pkg->id }}" class="hidden bg-blue-50">
                <td colspan="8" class="px-4 py-3">
                    <form action="{{ route('admin.packages.update', $pkg) }}" method="post" enctype="multipart/form-data"
                          class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @csrf
                        <div>
                            <label class="text-xs text-gray-500">Name</label>
                            <input type="text" name="name" value="{{ $pkg->name }}"
                                   class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Price (Kes)</label>
                            <input type="number" step="0.01" name="amount" value="{{ $pkg->amount }}"
                                   class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Daily (Kes)</label>
                            <input type="number" step="0.01" name="daily" value="{{ $pkg->daily }}"
                                   class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Cycle (days)</label>
                            <input type="number" name="days" value="{{ $pkg->days }}"
                                   class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">New Image (optional)</label>
                            <input type="file" name="image" accept="image/*"
                                   class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                        </div>
                        <div class="flex items-end gap-3">
                            <label class="flex items-center gap-1 text-sm text-gray-600">
                                <input type="checkbox" name="active" value="1" {{ $pkg->active ? 'checked' : '' }} class="accent-green-600">
                                Active
                            </label>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-1.5 rounded-lg">
                                Save
                            </button>
                        </div>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-3 py-4 text-center text-gray-400">No packages found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
