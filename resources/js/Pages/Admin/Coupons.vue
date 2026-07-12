<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Icon from '@/components/Icon.vue';
import Pagination from '@/components/Pagination.vue';
import type { Coupon, PaginatedResponse } from '@/types/models';

defineProps<{
    coupons: PaginatedResponse<Coupon>;
}>();

const form = useForm({
    code: '',
    amount: '',
    minutes: '',
    max_uses: '',
});

function submit() {
    form.post(route('admin.coupons.store'), {
        onSuccess: () => form.reset(),
    });
}

const deleteForm = useForm({});

function destroyCoupon(coupon: Coupon) {
    if (!confirm(`Delete coupon ${coupon.code}?`)) return;
    deleteForm.delete(route('admin.coupons.destroy', coupon.id));
}

function isExpired(coupon: Coupon) {
    return coupon.expires_at !== null && new Date(coupon.expires_at).getTime() <= Date.now();
}

function isExhausted(coupon: Coupon) {
    return coupon.max_uses > 0 && coupon.used_count >= coupon.max_uses;
}

function minutesLeft(coupon: Coupon) {
    if (!coupon.expires_at) return 0;
    const diffMs = new Date(coupon.expires_at).getTime() - Date.now();
    return Math.max(0, Math.floor(diffMs / 60000));
}

function formatExpiry(value: string | null) {
    if (!value) return '—';
    return new Date(value)
        .toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false })
        .replace(',', '');
}
</script>

<template>
    <Head title="Coupons - Admin" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Coupon Codes</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Create New Coupon</h2>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 uppercase tracking-widest mb-1">
                            Code <span class="normal-case text-gray-400">(blank = auto)</span>
                        </label>
                        <input
                            v-model="form.code"
                            type="text"
                            placeholder="e.g. PROMO100"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm uppercase tracking-widest focus:outline-none focus:border-blue-500"
                        >
                        <p v-if="form.errors.code" class="text-xs text-red-500 mt-1">{{ form.errors.code }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 uppercase tracking-widest mb-1">Reward (KES)</label>
                        <input
                            v-model="form.amount"
                            type="number"
                            min="1"
                            placeholder="e.g. 200"
                            required
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                        >
                        <p v-if="form.errors.amount" class="text-xs text-red-500 mt-1">{{ form.errors.amount }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 uppercase tracking-widest mb-1">Valid For (Minutes)</label>
                        <input
                            v-model="form.minutes"
                            type="number"
                            min="1"
                            placeholder="e.g. 60"
                            required
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                        >
                        <p v-if="form.errors.minutes" class="text-xs text-red-500 mt-1">{{ form.errors.minutes }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 uppercase tracking-widest mb-1">
                            Max Uses <span class="normal-case text-gray-400">(0 = unlimited)</span>
                        </label>
                        <input
                            v-model="form.max_uses"
                            type="number"
                            min="0"
                            placeholder="0"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                        >
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded transition-colors disabled:opacity-60"
                    >
                        <Icon name="plus" class="mr-1" /> Create Coupon
                    </button>
                    <p class="text-xs text-gray-400">Expiry is calculated from the moment of creation.</p>
                </div>
            </form>
        </div>

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
                    <tr v-for="coupon in coupons.data" :key="coupon.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800 tracking-widest">{{ coupon.code }}</td>
                        <td class="px-4 py-3 text-green-600 font-semibold">Kes {{ Number(coupon.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ formatExpiry(coupon.expires_at) }}</td>
                        <td class="px-4 py-3 text-xs">
                            <span v-if="isExpired(coupon)" class="text-red-400">Expired</span>
                            <template v-else>
                                <span v-if="minutesLeft(coupon) >= 60" class="text-green-600">
                                    {{ Math.floor(minutesLeft(coupon) / 60) }}h {{ minutesLeft(coupon) % 60 }}m
                                </span>
                                <span v-else class="text-yellow-500">{{ minutesLeft(coupon) }}m</span>
                            </template>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ coupon.used_count }}
                            <span v-if="coupon.max_uses > 0" class="text-gray-400">/ {{ coupon.max_uses }}</span>
                            <span v-else class="text-gray-400">/ ∞</span>
                        </td>
                        <td class="px-4 py-3">
                            <span v-if="!isExpired(coupon) && !isExhausted(coupon)" class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-medium">Active</span>
                            <span v-else-if="isExhausted(coupon)" class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full font-medium">Exhausted</span>
                            <span v-else class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full font-medium">Expired</span>
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" class="text-red-500 hover:text-red-700 text-xs font-medium" @click="destroyCoupon(coupon)">
                                <Icon name="trash" class="mr-1" />Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="coupons.data.length === 0">
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">No coupons yet.</td>
                    </tr>
                </tbody>
            </table>

            <div v-if="coupons.links.length > 3" class="px-4 py-3 border-t border-gray-100">
                <Pagination :paginator="coupons" />
            </div>
        </div>
    </AdminLayout>
</template>
