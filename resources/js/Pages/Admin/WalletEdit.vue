<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Icon from '@/components/Icon.vue';
import type { Wallet } from '@/types/models';

const props = defineProps<{
    wallet: Wallet;
}>();

const form = useForm({
    balance: Number(props.wallet.balance),
    deposit: Number(props.wallet.deposit),
    withdraw: Number(props.wallet.withdraw),
    referral: Number(props.wallet.referral),
    bonus: Number(props.wallet.bonus),
});

function submit() {
    form.post(route('admin.wallets.update', props.wallet.ID));
}
</script>

<template>
    <Head title="Edit Wallet - Admin" />

    <AdminLayout>
        <div class="flex items-center gap-3 mb-6">
            <Link :href="route('admin.wallets')" class="text-gray-500 hover:text-gray-700">
                <Icon name="arrow-left" />
            </Link>
            <h1 class="text-2xl font-bold text-gray-800">Edit Wallet</h1>
        </div>

        <div class="max-w-lg">
            <div class="bg-white rounded-xl shadow p-4 mb-4 flex items-center gap-4">
                <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center">
                    <Icon name="user" class="text-blue-600 text-xl" />
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ wallet.email }}</p>
                    <p v-if="wallet.user" class="text-xs text-gray-400">
                        Phone: {{ wallet.user.phone }} &nbsp;|&nbsp; Status: {{ wallet.user.status }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-sm font-semibold text-gray-600 mb-4 border-b pb-2">Wallet Balances (Kes)</p>

                <form class="flex flex-col gap-4" @submit.prevent="submit">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Balance</label>
                            <input
                                v-model.number="form.balance"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                                required
                            >
                            <p v-if="form.errors.balance" class="text-xs text-red-500 mt-1">{{ form.errors.balance }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Total Deposits</label>
                            <input
                                v-model.number="form.deposit"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                                required
                            >
                            <p v-if="form.errors.deposit" class="text-xs text-red-500 mt-1">{{ form.errors.deposit }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Total Withdrawals</label>
                            <input
                                v-model.number="form.withdraw"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                                required
                            >
                            <p v-if="form.errors.withdraw" class="text-xs text-red-500 mt-1">{{ form.errors.withdraw }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Referral Earnings</label>
                            <input
                                v-model.number="form.referral"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                                required
                            >
                            <p v-if="form.errors.referral" class="text-xs text-red-500 mt-1">{{ form.errors.referral }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Bonus</label>
                            <input
                                v-model.number="form.bonus"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                                required
                            >
                            <p v-if="form.errors.bonus" class="text-xs text-red-500 mt-1">{{ form.errors.bonus }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg text-sm disabled:opacity-60"
                        >
                            Save Changes
                        </button>
                        <Link
                            :href="route('admin.wallets')"
                            class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 rounded-lg text-sm"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
