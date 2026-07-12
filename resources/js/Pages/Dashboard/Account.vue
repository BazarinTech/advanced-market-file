<script setup lang="ts">
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Icon from '@/components/Icon.vue';

defineProps<{
    downline: number;
    numActive: number;
    link_whatsapp: string | null;
    link_telegram: string | null;
    link_customer_support: string | null;
    link_download_app: string | null;
}>();

const page = usePage();
const earnings = page.props.auth.user!.earnings!;

function money(v: string | number) {
    return Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

const menuItems = [
    { route: 'deposit', icon: 'plus-circle', label: 'Deposit', desc: 'Add funds to your account' },
    { route: 'transaction', icon: 'receipt', label: 'Transactions', desc: 'View transaction history' },
    { route: 'withdraw', icon: 'arrow-up-from-bracket', label: 'Withdraw', desc: 'Cash out your earnings' },
    { route: 'coupon', icon: 'ticket', label: 'Redeem Coupon', desc: 'Enter a code to claim your reward' },
    { route: 'user', icon: 'user-tie', label: 'Profile Settings', desc: 'Manage your account details' },
    { route: 'team', icon: 'share-nodes', label: 'My Team', desc: 'View referrals & earnings' },
    { route: 'packages.table', icon: 'table-list', label: 'Browse Tasks', desc: 'Compare all available tasks' },
] as const;

const logoutForm = useForm({});

function logout() {
    logoutForm.post(route('logout'));
}
</script>

<template>
    <Head title="Account" />

    <AppLayout show-bottom-nav>
        <!-- Top Bar -->
        <div class="w-full flex items-center justify-center h-14 bg-card border-b border-border">
            <p class="text-foreground text-xs font-light tracking-[0.3em] uppercase">Account</p>
        </div>

        <!-- Balance Card -->
        <div class="w-full px-4 mt-4">
            <div class="w-full rounded-2xl p-5 grid grid-cols-3 text-center bg-card border border-border">
                <div class="flex flex-col gap-1.5 items-center">
                    <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Balance</p>
                    <p class="text-foreground text-sm font-medium">Kes {{ money(earnings.balance) }}</p>
                </div>
                <div class="flex flex-col gap-1.5 items-center border-l border-r border-border">
                    <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Deposited</p>
                    <p class="text-foreground text-sm font-medium">Kes {{ money(earnings.deposit) }}</p>
                </div>
                <div class="flex flex-col gap-1.5 items-center">
                    <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Withdrawn</p>
                    <p class="text-foreground text-sm font-medium">Kes {{ money(earnings.withdraw) }}</p>
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        <div class="w-full px-4 mt-4 pb-32 flex flex-col gap-2">
            <Link
                v-for="item in menuItems"
                :key="item.route"
                :href="route(item.route)"
                class="flex items-center justify-between px-4 py-3.5 rounded-2xl no-underline transition-colors bg-card border border-border"
            >
                <span class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <Icon :name="item.icon" class="text-primary text-sm" />
                    </div>
                    <span>
                        <span class="block text-foreground text-sm tracking-wide font-light">{{ item.label }}</span>
                        <span class="block text-muted-foreground text-[10px] mt-0.5">{{ item.desc }}</span>
                    </span>
                </span>
                <Icon name="angle-right" class="text-border text-sm" />
            </Link>

            <a
                v-if="link_whatsapp"
                :href="link_whatsapp"
                target="_blank"
                class="flex items-center justify-between px-4 py-3.5 rounded-2xl no-underline transition-colors bg-card border border-border"
            >
                <span class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-success/10 flex items-center justify-center shrink-0">
                        <Icon name="whatsapp" class="text-success text-sm" />
                    </div>
                    <span>
                        <span class="block text-foreground text-sm tracking-wide font-light">WhatsApp Group</span>
                        <span class="block text-muted-foreground text-[10px] mt-0.5">Join our community</span>
                    </span>
                </span>
                <Icon name="angle-right" class="text-border text-sm" />
            </a>

            <a
                v-if="link_telegram"
                :href="link_telegram"
                target="_blank"
                class="flex items-center justify-between px-4 py-3.5 rounded-2xl no-underline transition-colors bg-card border border-border"
            >
                <span class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-info/10 flex items-center justify-center shrink-0">
                        <Icon name="telegram" class="text-info text-sm" />
                    </div>
                    <span>
                        <span class="block text-foreground text-sm tracking-wide font-light">Telegram Group</span>
                        <span class="block text-muted-foreground text-[10px] mt-0.5">Get updates &amp; announcements</span>
                    </span>
                </span>
                <Icon name="angle-right" class="text-border text-sm" />
            </a>

            <a
                v-if="link_customer_support"
                :href="link_customer_support"
                target="_blank"
                class="flex items-center justify-between px-4 py-3.5 rounded-2xl no-underline transition-colors bg-card border border-border"
            >
                <span class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-info/10 flex items-center justify-center shrink-0">
                        <Icon name="headset" class="text-info text-sm" />
                    </div>
                    <span>
                        <span class="block text-foreground text-sm tracking-wide font-light">Customer Support</span>
                        <span class="block text-muted-foreground text-[10px] mt-0.5">Get help from our team</span>
                    </span>
                </span>
                <Icon name="angle-right" class="text-border text-sm" />
            </a>

            <a
                v-if="link_download_app"
                :href="link_download_app"
                target="_blank"
                class="flex items-center justify-between px-4 py-3.5 rounded-2xl no-underline transition-colors bg-card border border-border"
            >
                <span class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-warning/10 flex items-center justify-center shrink-0">
                        <Icon name="download" class="text-warning text-sm" />
                    </div>
                    <span>
                        <span class="block text-foreground text-sm tracking-wide font-light">Download App</span>
                        <span class="block text-muted-foreground text-[10px] mt-0.5">Get the mobile application</span>
                    </span>
                </span>
                <Icon name="angle-right" class="text-border text-sm" />
            </a>

            <!-- Logout -->
            <form class="mt-2" @submit.prevent="logout">
                <button
                    type="submit"
                    :disabled="logoutForm.processing"
                    class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl transition-colors text-left disabled:opacity-70 bg-destructive/10 border border-destructive/20"
                >
                    <span class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-destructive/10 flex items-center justify-center shrink-0">
                            <Icon name="right-from-bracket" class="text-destructive text-sm" />
                        </div>
                        <span class="text-destructive text-sm tracking-wide font-light">Sign Out</span>
                    </span>
                    <Icon name="angle-right" class="text-destructive/50 text-sm" />
                </button>
            </form>
        </div>
    </AppLayout>
</template>
