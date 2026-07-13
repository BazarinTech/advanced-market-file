<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { HugeiconsIcon } from '@hugeicons/vue';
import {
    Notification03Icon,
    ViewIcon,
    ViewOffIcon,
    MoneyAdd02Icon,
    MoneySend02Icon,
    Task01Icon,
    BriefcaseDollarIcon,
    Invoice01Icon,
    TicketIcon,
    UserGroupIcon,
    UserSettings01Icon,
    Analytics02Icon,
    WhatsappIcon,
    TelegramIcon,
    CustomerSupportIcon,
    Download04Icon,
    Logout02Icon,
} from '@hugeicons/core-free-icons';

const props = defineProps<{
    downline: number;
    numActive: number;
    link_whatsapp: string | null;
    link_telegram: string | null;
    link_customer_support: string | null;
    link_download_app: string | null;
}>();

const page = usePage();
const user = page.props.auth.user!;
const earnings = user.earnings!;

const hideBalance = ref(false);

function money(v: string | number) {
    return Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

const logoutForm = useForm({});
function logout() {
    logoutForm.post(route('logout'));
}

type Tile =
    | { label: string; icon: object; bg: string; type: 'route'; route: string }
    | { label: string; icon: object; bg: string; type: 'external'; href: string }
    | { label: string; icon: object; bg: string; type: 'logout' };

const tiles = computed<Tile[]>(() => {
    const items: Tile[] = [
        { label: 'Deposit', icon: MoneyAdd02Icon, bg: 'bg-emerald-500', type: 'route', route: 'deposit' },
        { label: 'Withdraw', icon: MoneySend02Icon, bg: 'bg-blue-500', type: 'route', route: 'withdraw' },
        { label: 'Tasks', icon: Task01Icon, bg: 'bg-amber-500', type: 'route', route: 'packages' },
        { label: 'My Tasks', icon: BriefcaseDollarIcon, bg: 'bg-violet-500', type: 'route', route: 'task' },
        { label: 'History', icon: Invoice01Icon, bg: 'bg-teal-500', type: 'route', route: 'transaction' },
        { label: 'Coupon', icon: TicketIcon, bg: 'bg-rose-500', type: 'route', route: 'coupon' },
        { label: 'My Team', icon: UserGroupIcon, bg: 'bg-indigo-500', type: 'route', route: 'team' },
        { label: 'Profile', icon: UserSettings01Icon, bg: 'bg-slate-600', type: 'route', route: 'user' },
        { label: 'Compare', icon: Analytics02Icon, bg: 'bg-orange-500', type: 'route', route: 'packages.table' },
    ];

    if (props.link_whatsapp) {
        items.push({ label: 'WhatsApp', icon: WhatsappIcon, bg: 'bg-green-500', type: 'external', href: props.link_whatsapp });
    }
    if (props.link_telegram) {
        items.push({ label: 'Telegram', icon: TelegramIcon, bg: 'bg-sky-500', type: 'external', href: props.link_telegram });
    }
    if (props.link_customer_support) {
        items.push({ label: 'Support', icon: CustomerSupportIcon, bg: 'bg-cyan-600', type: 'external', href: props.link_customer_support });
    }
    if (props.link_download_app) {
        items.push({ label: 'Get App', icon: Download04Icon, bg: 'bg-fuchsia-500', type: 'external', href: props.link_download_app });
    }

    items.push({ label: 'Sign Out', icon: Logout02Icon, bg: 'bg-red-500', type: 'logout' });

    return items;
});

const headerStyle =
    'background: radial-gradient(circle at 18% -10%, #f59e0b 0%, #d97706 42%, #b45309 100%);';
</script>

<template>
    <Head title="Account" />

    <AppLayout show-bottom-nav>
        <!-- Gradient header -->
        <div class="w-full rounded-b-4xl px-5 pt-5 pb-10 relative" :style="headerStyle">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                        <span class="text-white text-base font-bold">{{ (user.email[0] || 'M').toUpperCase() }}</span>
                    </div>
                    <div class="leading-tight pt-0.5">
                        <p class="text-white/70 text-[11px] tracking-wide">Welcome back,</p>
                        <p class="text-white text-sm font-semibold tracking-wide">{{ user.phone }}</p>
                    </div>
                </div>
                <Link
                    :href="route('transaction')"
                    class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center shrink-0 no-underline"
                >
                    <HugeiconsIcon :icon="Notification03Icon" :size="20" color="#ffffff" :stroke-width="1.8" />
                </Link>
            </div>

            <div class="text-center mt-6">
                <p class="text-white/70 text-[11px] tracking-[0.25em] uppercase">Available Balance</p>
                <div class="flex items-center justify-center gap-3 mt-1">
                    <p class="text-white text-4xl font-bold tracking-tight">
                        <span class="text-xl font-medium align-middle mr-1">KES</span>
                        <span v-if="!hideBalance">{{ money(earnings.balance) }}</span>
                        <span v-else>••••••</span>
                    </p>
                    <button type="button" class="text-white/80 hover:text-white" @click="hideBalance = !hideBalance">
                        <HugeiconsIcon :icon="hideBalance ? ViewOffIcon : ViewIcon" :size="20" color="currentColor" :stroke-width="1.8" />
                    </button>
                </div>
                <p class="text-white/70 text-[11px] tracking-wide mt-2">
                    Team: {{ downline }} member{{ downline === 1 ? '' : 's' }} · {{ numActive }} active
                </p>
            </div>
        </div>

        <!-- Wallet sheet -->
        <div class="w-full flex-1 -mt-5 rounded-t-3xl bg-card border-t border-border px-5 pt-5 pb-32">
            <!-- Wallet summary row -->
            <div class="w-full flex items-center justify-between bg-secondary rounded-2xl px-4 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <HugeiconsIcon :icon="MoneySend02Icon" :size="18" color="currentColor" class="text-primary" :stroke-width="1.8" />
                    </div>
                    <span class="text-muted-foreground text-xs font-medium tracking-wide">Total Withdrawn</span>
                </div>
                <span class="text-foreground text-base font-bold">KES {{ money(earnings.withdraw) }}</span>
            </div>

            <div class="border-t border-border mt-5 mb-5"></div>

            <!-- Feature grid -->
            <div class="grid grid-cols-4 gap-x-2 gap-y-5">
                <component
                    :is="tile.type === 'route' ? Link : tile.type === 'external' ? 'a' : 'button'"
                    v-for="tile in tiles"
                    :key="tile.label"
                    :href="tile.type === 'route' ? route(tile.route) : tile.type === 'external' ? tile.href : undefined"
                    :target="tile.type === 'external' ? '_blank' : undefined"
                    :rel="tile.type === 'external' ? 'noopener noreferrer' : undefined"
                    :type="tile.type === 'logout' ? 'button' : undefined"
                    class="flex flex-col items-center gap-2 no-underline group"
                    @click="tile.type === 'logout' ? logout() : undefined"
                >
                    <div
                        class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm transition-transform group-active:scale-95"
                        :class="tile.bg"
                    >
                        <HugeiconsIcon :icon="tile.icon" :size="26" color="#ffffff" :stroke-width="1.8" />
                    </div>
                    <span class="text-[11px] text-zinc-700 text-center leading-tight">{{ tile.label }}</span>
                </component>
            </div>
        </div>
    </AppLayout>
</template>
