@extends('layouts.admin')
@section('title', 'Coupons - Admin')
@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Coupon Codes</h1>
</div>

{{-- Create form --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Create New Coupon</h2>
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-widest mb-1">Code <span class="normal-case text-gray-400">(blank = auto)</span></label>
                <input type="text" name="code" placeholder="e.g. PROMO100"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm uppercase tracking-widest focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-widest mb-1">Reward (KES)</label>
                <input type="number" name="amount" min="1" placeholder="e.g. 200" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-widest mb-1">Valid For (Minutes)</label>
                <input type="number" name="minutes" min="1" placeholder="e.g. 60" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-widest mb-1">Max Uses <span class="normal-case text-gray-400">(0 = unlimited)</span></label>
                <input type="number" name="max_uses" min="0" placeholder="0"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
        </div>
        <div class="mt-4 flex items-center gap-3">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded transition-colors">
                <x-icon name="plus" class="mr-1" /> Create Coupon
            </button>
            <p class="text-xs text-gray-400">Expiry is calculated from the moment of creation.</p>
        </div>
    </form>
</div>

{{-- Coupons table --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">Code</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">Reward</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">Expires At</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">Time Left</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">Uses</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($coupons as $coupon)
            @php
                $expired    = $coupon->isExpired();
                $exhausted  = $coupon->isExhausted();
                $valid      = !$expired && !$exhausted;
                $minsLeft   = $coupon->minutesLeft();
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono font-semibold text-gray-800 tracking-widest">{{ $coupon->code }}</td>
                <td class="px-4 py-3 text-green-600 font-semibold">Kes {{ number_format($coupon->amount, 2) }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">
                    {{ $coupon->expires_at ? $coupon->expires_at->format('d M Y H:i') : '—' }}
                </td>
                <td class="px-4 py-3 text-xs">
                    @if($expired)
                        <span class="text-red-400">Expired</span>
                    @else
                        @if($minsLeft >= 60)
                            <span class="text-green-600">{{ floor($minsLeft/60) }}h {{ $minsLeft % 60 }}m</span>
                        @else
                            <span class="text-yellow-500">{{ $minsLeft }}m</span>
                        @endif
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $coupon->used_count }}
                    @if($coupon->max_uses > 0)
                        <span class="text-gray-400">/ {{ $coupon->max_uses }}</span>
                    @else
                        <span class="text-gray-400">/ ∞</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($valid)
                        <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-medium">Active</span>
                    @elseif($exhausted)
                        <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full font-medium">Exhausted</span>
                    @else
                        <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full font-medium">Expired</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                          onsubmit="return confirm('Delete coupon {{ $coupon->code }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">
                            <x-icon name="trash" class="mr-1" />Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">No coupons yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($coupons->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $coupons->links() }}
    </div>
    @endif
</div>

@endsection
