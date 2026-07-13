<script setup lang="ts">
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import Icon from '@/components/Icon.vue';
import { useFlashToasts } from '@/composables/useFlashToasts';

useFlashToasts();

const sidebarOpen = ref(window.innerWidth >= 1024);

const navLinks = [
    { route: 'admin.dashboard', icon: 'wallet', label: 'Dashboard' },
    { route: 'admin.deposits', icon: 'money', label: 'Deposits' },
    { route: 'admin.withdrawals', icon: 'money-bill-trend-up', label: 'Withdrawals' },
    { route: 'admin.users', icon: 'users', label: 'Users' },
    { route: 'admin.packages', icon: 'box', label: 'Packages' },
    { route: 'admin.withdrawal-accounts', icon: 'id-card', label: 'W. Accounts' },
    { route: 'admin.wallets', icon: 'wallet', label: 'Wallets' },
    { route: 'admin.coupons.index', icon: 'ticket', label: 'Coupons' },
    { route: 'admin.settings', icon: 'gear', label: 'Settings' },
] as const;

const logoutForm = useForm({});
function logout() {
    logoutForm.post(route('logout'));
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 font-sans">
        <aside
            class="bg-blue-700 text-white flex flex-col w-56 min-h-screen p-4 transition-all duration-300"
            :class="sidebarOpen ? '' : 'hidden'"
        >
            <div class="flex items-center justify-between border-b border-blue-500 pb-3 mb-4">
                <span class="text-2xl font-bold">Admin</span>
                <button class="text-2xl font-bold lg:hidden" @click="sidebarOpen = false">&times;</button>
            </div>
            <Link
                v-for="link in navLinks"
                :key="link.route"
                :href="route(link.route)"
                class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-yellow-300 text-lg mb-1"
            >
                <Icon :name="link.icon" class="w-5" /> {{ link.label }}
            </Link>
            <button
                class="flex items-center gap-2 py-2 px-3 rounded hover:bg-blue-600 hover:text-red-300 text-lg w-full text-left mt-auto"
                @click="logout"
            >
                <Icon name="right-from-bracket" class="w-5" /> Logout
            </button>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow flex items-center px-4 py-3">
                <button v-if="!sidebarOpen" class="text-xl mr-3 lg:hidden" @click="sidebarOpen = true">
                    <Icon name="bars" />
                </button>
                <span class="font-bold text-green-600 text-lg">Bazarin Technologies</span>
            </header>

            <main class="p-6 flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
