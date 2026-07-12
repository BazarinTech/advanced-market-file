<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import type { PaginatedResponse, Wallet } from '@/types/models';

defineProps<{
    wallets: PaginatedResponse<Wallet>;
}>();

// The controller doesn't pass `search` as a prop (it reads request('search') server-side
// only for filtering); mirror the original blade's `request('search')` by reading the
// current URL's query string on the client.
const page = usePage();
const initialSearch = new URLSearchParams(page.url.split('?')[1] ?? '').get('search') ?? '';
const search = ref(initialSearch);

function submitSearch() {
    router.get(route('admin.wallets'), { search: search.value }, { preserveState: true });
}

function clearSearch() {
    search.value = '';
    router.get(route('admin.wallets'));
}

function fmt(value: string) {
    return Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>

<template>
    <Head title="Wallets - Admin" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">User Wallets</h1>
            <span class="text-sm text-gray-500">{{ wallets.total }} wallet(s)</span>
        </div>

        <form class="mb-4 flex gap-2" @submit.prevent="submitSearch">
            <input
                v-model="search"
                type="text"
                placeholder="Search by email..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72 focus:outline-none focus:border-blue-500"
            >
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">Search</button>
            <button v-if="search" type="button" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg" @click="clearSearch">
                Clear
            </button>
        </form>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-right">Balance</th>
                        <th class="px-4 py-3 text-right">Deposits</th>
                        <th class="px-4 py-3 text-right">Withdrawals</th>
                        <th class="px-4 py-3 text-right">Referral</th>
                        <th class="px-4 py-3 text-right">Bonus</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="wallet in wallets.data" :key="wallet.ID" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ wallet.ID }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ wallet.email }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">{{ fmt(wallet.balance) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ fmt(wallet.deposit) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ fmt(wallet.withdraw) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ fmt(wallet.referral) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ fmt(wallet.bonus) }}</td>
                        <td class="px-4 py-3 text-center">
                            <Link
                                :href="route('admin.wallets.edit', wallet.ID)"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg"
                            >
                                Edit
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="wallets.data.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">No wallets found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :paginator="wallets" />
    </AdminLayout>
</template>
