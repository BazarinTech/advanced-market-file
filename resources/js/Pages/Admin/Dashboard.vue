<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Icon from '@/components/Icon.vue';

const props = defineProps<{
    totalDeps: number;
    totalWith: number;
    totalBals: number;
    totalUsers: number;
    active: number;
    inactive: number;
    joinedToday: number;
    usersDeposited: number;
}>();

function money(value: number) {
    return 'Kes ' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

const cards = computed(() => [
    { label: 'Payment Balance', value: money(0), border: 'border-purple-400', icon: 'money' },
    { label: 'Total Withdrawals', value: money(props.totalWith), border: 'border-blue-400', icon: 'money-bill-trend-up' },
    { label: 'Total Account Bals', value: money(props.totalBals), border: 'border-yellow-400', icon: 'money-bill-trend-up' },
    { label: 'Total Deposits', value: money(props.totalDeps), border: 'border-amber-400', icon: 'money-bill-1-wave' },
    { label: 'Active Users', value: props.active, border: 'border-purple-400', icon: 'users' },
    { label: 'Total Users', value: props.totalUsers, border: 'border-green-400', icon: 'user' },
    { label: 'Joined Today', value: props.joinedToday, border: 'border-green-400', icon: 'user' },
    { label: 'Users Deposited', value: props.usersDeposited, border: 'border-amber-400', icon: 'circle-check' },
]);
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Admin Dashboard</h1>

        <div class="flex flex-wrap gap-2 mb-6">
            <Link :href="route('admin.deposits')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Deposits</Link>
            <Link :href="route('admin.withdrawals')" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Withdrawals</Link>
            <Link :href="route('admin.users')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Users</Link>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="card in cards"
                :key="card.label"
                class="bg-white rounded-xl shadow p-4 flex items-center justify-between border-l-4"
                :class="card.border"
            >
                <div>
                    <p class="text-green-600 text-sm">{{ card.label }}</p>
                    <p class="font-bold text-gray-700 text-lg">{{ card.value }}</p>
                </div>
                <span class="text-gray-300 text-3xl"><Icon :name="card.icon" /></span>
            </div>
        </div>
    </AdminLayout>
</template>
