<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { formatDateTime } from '@/lib/utils';
import type { PaginatedResponse, Transaction } from '@/types/models';

defineProps<{
    deposits: PaginatedResponse<Transaction>;
}>();

const form = useForm({
    amount: '',
    phone: '',
    email: '',
    transactionID: '',
});

function submit() {
    form.post(route('admin.deposits.store'), {
        onSuccess: () => form.reset(),
    });
}

function approve(id: number) {
    router.post(route('admin.deposits.approve', id));
}

function reject(id: number) {
    router.post(route('admin.deposits.reject', id));
}
</script>

<template>
    <Head title="Deposits - Admin" />

    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Deposits</h1>

        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <h3 class="text-blue-600 font-bold text-lg mb-3">Manual Deposit</h3>
            <form class="flex flex-col gap-3 max-w-md" @submit.prevent="submit">
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Amount</label>
                    <input
                        v-model="form.amount"
                        type="number"
                        step="0.01"
                        placeholder="Enter Amount (Kes)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                        required
                    >
                    <p v-if="form.errors.amount" class="text-xs text-red-500 mt-1">{{ form.errors.amount }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Phone Number</label>
                    <input
                        v-model="form.phone"
                        type="tel"
                        placeholder="07xxxxxxxxx"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                        required
                    >
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="Enter Email"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                        required
                    >
                    <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Transaction ID</label>
                    <input
                        v-model="form.transactionID"
                        type="text"
                        placeholder="Enter Transaction ID"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                        required
                    >
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-1/2 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 rounded-lg disabled:opacity-60"
                >
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
                        <th class="px-3 py-3">Method</th>
                        <th class="px-3 py-3">Amount</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Phone</th>
                        <th class="px-3 py-3">Date</th>
                        <th class="px-3 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="(d, i) in deposits.data" :key="d.ID" class="hover:bg-gray-50">
                        <td class="px-3 py-2">{{ i + 1 }}</td>
                        <td class="px-3 py-2">{{ d.ID }}</td>
                        <td class="px-3 py-2">{{ d.email }}</td>
                        <td class="px-3 py-2 uppercase text-xs font-semibold" :class="d.method === 'crypto' ? 'text-teal-600' : 'text-blue-600'">{{ d.method ?? 'mpesa' }}</td>
                        <td class="px-3 py-2">Kes {{ Number(d.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                        <td class="px-3 py-2 font-semibold" :class="d.status === 'Success' ? 'text-green-600' : d.status === 'Pending' ? 'text-amber-500' : 'text-red-500'">{{ d.status }}</td>
                        <td class="px-3 py-2">{{ d.phone }}</td>
                        <td class="px-3 py-2 text-xs text-gray-500">{{ formatDateTime(d.date) }}</td>
                        <td class="px-3 py-2 flex flex-wrap gap-1">
                            <template v-if="d.status === 'Pending'">
                                <button class="px-2 py-1 rounded text-xs text-white bg-green-600 hover:bg-green-700" @click="approve(d.ID)">
                                    Approve
                                </button>
                                <button class="px-2 py-1 rounded text-xs text-white bg-red-500 hover:bg-red-600" @click="reject(d.ID)">
                                    Reject
                                </button>
                            </template>
                        </td>
                    </tr>
                    <tr v-if="deposits.data.length === 0">
                        <td colspan="9" class="px-3 py-4 text-center text-gray-400">No deposits found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :paginator="deposits" />
    </AdminLayout>
</template>
