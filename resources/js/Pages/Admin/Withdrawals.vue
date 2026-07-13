<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import type { PaginatedResponse, Transaction } from '@/types/models';

defineProps<{
    withdrawals: PaginatedResponse<Transaction>;
}>();

function money(value: string) {
    return 'Kes ' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function approve(id: number) {
    router.post(route('admin.withdrawals.approve', id));
}

function reject(id: number) {
    router.post(route('admin.withdrawals.reject', id));
}
</script>

<template>
    <Head title="Withdrawals - Admin" />

    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Withdrawals</h1>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-green-600 text-white">
                    <tr>
                        <th class="px-3 py-3">#</th>
                        <th class="px-3 py-3">ID</th>
                        <th class="px-3 py-3">Email</th>
                        <th class="px-3 py-3">Method</th>
                        <th class="px-3 py-3">Amount</th>
                        <th class="px-3 py-3">Rec. Amount</th>
                        <th class="px-3 py-3">Destination</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Date</th>
                        <th class="px-3 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="(w, i) in withdrawals.data" :key="w.ID" class="hover:bg-gray-50">
                        <td class="px-3 py-2">{{ i + 1 }}</td>
                        <td class="px-3 py-2">{{ w.ID }}</td>
                        <td class="px-3 py-2">{{ w.email }}</td>
                        <td class="px-3 py-2 uppercase text-xs font-semibold" :class="w.method === 'crypto' ? 'text-teal-600' : 'text-blue-600'">{{ w.method ?? 'mpesa' }}</td>
                        <td class="px-3 py-2">{{ money(w.amount) }}</td>
                        <td class="px-3 py-2">{{ money(w.RecAmount) }}</td>
                        <td class="px-3 py-2 font-mono text-xs break-all max-w-40">{{ w.method === 'crypto' ? w.payout_address : w.phone }}</td>
                        <td class="px-3 py-2 font-semibold" :class="['Success', 'Approved'].includes(w.status) ? 'text-green-600' : 'text-red-500'">
                            {{ w.status }}
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-500">{{ w.date }}</td>
                        <td class="px-3 py-2 flex flex-wrap gap-1">
                            <template v-if="w.status === 'Pending'">
                                <button class="px-2 py-1 rounded text-xs text-white bg-green-600 hover:bg-green-700" @click="approve(w.ID)">
                                    Approve
                                </button>
                                <button class="px-2 py-1 rounded text-xs text-white bg-red-500 hover:bg-red-600" @click="reject(w.ID)">
                                    Reject
                                </button>
                            </template>
                            <span v-else class="text-gray-400 text-xs">{{ w.status }}</span>
                        </td>
                    </tr>
                    <tr v-if="withdrawals.data.length === 0">
                        <td colspan="10" class="px-3 py-4 text-center text-gray-400">No withdrawals found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :paginator="withdrawals" />
    </AdminLayout>
</template>
