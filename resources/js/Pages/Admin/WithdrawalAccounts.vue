<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import type { PaginatedResponse, WithdrawalAccount } from '@/types/models';

defineProps<{
    accounts: PaginatedResponse<WithdrawalAccount & { updated_at?: string | null }>;
}>();

const editOpen = ref(false);
const editingId = ref<number | null>(null);

const editForm = useForm({
    name: '',
    phone: '',
    crypto_address: '',
});

const editingMethod = ref<'mpesa' | 'crypto'>('mpesa');

function openEdit(account: WithdrawalAccount) {
    editingId.value = account.id;
    editingMethod.value = account.method;
    editForm.name = account.name ?? '';
    editForm.phone = account.phone ?? '';
    editForm.crypto_address = account.crypto_address ?? '';
    editForm.clearErrors();
    editOpen.value = true;
}

function submitEdit() {
    if (editingId.value === null) return;
    editForm.post(route('admin.withdrawal-accounts.update', editingId.value), {
        onSuccess: () => {
            editOpen.value = false;
        },
    });
}

function timeAgo(value?: string | null) {
    if (!value) return '—';
    const diffMs = Date.now() - new Date(value).getTime();
    const diffMins = Math.round(diffMs / 60000);
    if (diffMins < 1) return 'just now';
    if (diffMins < 60) return `${diffMins} minute${diffMins === 1 ? '' : 's'} ago`;
    const diffHours = Math.round(diffMins / 60);
    if (diffHours < 24) return `${diffHours} hour${diffHours === 1 ? '' : 's'} ago`;
    const diffDays = Math.round(diffHours / 24);
    return `${diffDays} day${diffDays === 1 ? '' : 's'} ago`;
}
</script>

<template>
    <Head title="Withdrawal Accounts - Admin" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Withdrawal Accounts</h1>
            <span class="text-sm text-gray-500">{{ accounts.total }} account(s)</span>
        </div>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Method</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Phone / Address</th>
                        <th class="px-4 py-3 text-left">Last Updated</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="(account, i) in accounts.data" :key="account.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ i + 1 }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ account.email }}</td>
                        <td class="px-4 py-3 uppercase text-xs font-semibold" :class="account.method === 'crypto' ? 'text-teal-600' : 'text-blue-600'">{{ account.method }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ account.name ?? '—' }}</td>
                        <td class="px-4 py-3 text-green-600 font-semibold font-mono text-xs break-all">{{ account.method === 'crypto' ? account.crypto_address : account.phone }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ timeAgo(account.updated_at) }}</td>
                        <td class="px-4 py-3">
                            <button
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg"
                                @click="openEdit(account)"
                            >
                                Edit
                            </button>
                        </td>
                    </tr>
                    <tr v-if="accounts.data.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No withdrawal accounts found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :paginator="accounts" />

        <Dialog v-model:open="editOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Edit Withdrawal Account</DialogTitle>
                </DialogHeader>
                <form class="flex flex-col gap-4" @submit.prevent="submitEdit">
                    <template v-if="editingMethod === 'mpesa'">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Full Name (as on M-Pesa)</label>
                            <input
                                v-model="editForm.name"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                                required
                            >
                            <p v-if="editForm.errors.name" class="text-xs text-red-500 mt-1">{{ editForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">M-Pesa Phone Number</label>
                            <input
                                v-model="editForm.phone"
                                type="tel"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                                required
                            >
                            <p v-if="editForm.errors.phone" class="text-xs text-red-500 mt-1">{{ editForm.errors.phone }}</p>
                        </div>
                    </template>
                    <div v-else>
                        <label class="text-xs text-gray-500 mb-1 block">USDT-TRC20 Address</label>
                        <input
                            v-model="editForm.crypto_address"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:border-blue-500"
                            required
                        >
                        <p v-if="editForm.errors.crypto_address" class="text-xs text-red-500 mt-1">{{ editForm.errors.crypto_address }}</p>
                    </div>
                    <div class="flex gap-3 mt-2">
                        <button
                            type="submit"
                            :disabled="editForm.processing"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg text-sm disabled:opacity-60"
                        >
                            Save Changes
                        </button>
                        <button
                            type="button"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 rounded-lg text-sm"
                            @click="editOpen = false"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AdminLayout>
</template>
