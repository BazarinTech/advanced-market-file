<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Icon from '@/components/Icon.vue';

defineProps<{
    downline: number;
    numActive: number;
    chartLabels: string[];
    chartValues: number[];
}>();

const page = usePage();
const user = page.props.auth.user!;
const earnings = user.earnings!;

const ticker = [
    ['BTC/USD', '$67,420', '▲ 2.4%', true],
    ['ETH/USD', '$3,512', '▲ 1.8%', true],
    ['BNB/USD', '$412', '▼ 0.6%', false],
    ['SOL/USD', '$178', '▲ 4.1%', true],
    ['XRP/USD', '$0.612', '▲ 0.9%', true],
    ['ADA/USD', '$0.48', '▼ 1.2%', false],
    ['BTC/USD', '$67,420', '▲ 2.4%', true],
    ['ETH/USD', '$3,512', '▲ 1.8%', true],
    ['BNB/USD', '$412', '▼ 0.6%', false],
    ['SOL/USD', '$178', '▲ 4.1%', true],
] as const;

function money(v: string | number) {
    return Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function moneyRound(v: string | number) {
    return Math.round(Number(v)).toLocaleString('en-US');
}

// TradingView mini symbol overview widget — injected imperatively since it's a third-party <script> tag.
const widgetContainer = ref<HTMLDivElement | null>(null);
let widgetScript: HTMLScriptElement | null = null;

onMounted(() => {
    if (!widgetContainer.value) return;

    widgetScript = document.createElement('script');
    widgetScript.type = 'text/javascript';
    widgetScript.src = 'https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js';
    widgetScript.async = true;
    widgetScript.innerHTML = JSON.stringify({
        symbol: 'BINANCE:BTCUSDT',
        width: '100%',
        height: 220,
        locale: 'en',
        dateRange: '1M',
        colorTheme: 'dark',
        trendLineColor: 'rgba(0,201,167,1)',
        underLineColor: 'rgba(0,201,167,0.25)',
        underLineBottomColor: 'rgba(0,201,167,0)',
        isTransparent: true,
        autosize: true,
        largeChartUrl: '',
    });
    widgetContainer.value.appendChild(widgetScript);
});

onUnmounted(() => {
    widgetScript?.remove();
});
</script>

<template>
    <Head title="Home" />

    <AppLayout show-bottom-nav>
        <!-- Top Bar -->
        <div class="w-full flex items-center justify-between h-14 px-4 bg-secondary border-b border-border">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded bg-primary flex items-center justify-center">
                    <Icon name="chart-line" class="text-primary-foreground text-xs" />
                </div>
                <span class="text-white font-semibold tracking-widest text-sm uppercase">Trade-Swing</span>
            </div>
            <div class="text-right">
                <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Welcome back</p>
                <p class="text-primary text-xs tracking-wide">{{ user.phone }}</p>
            </div>
        </div>

        <!-- Market ticker strip -->
        <div class="w-full bg-secondary border-b border-border overflow-hidden py-2">
            <div class="flex gap-6 ticker-track whitespace-nowrap px-4" style="width: max-content">
                <span v-for="(t, i) in ticker" :key="i" class="inline-flex items-center gap-1.5 text-[11px]">
                    <span class="text-muted-foreground">{{ t[0] }}</span>
                    <span class="text-white font-medium">{{ t[1] }}</span>
                    <span :class="t[3] ? 'text-success' : 'text-destructive'">{{ t[2] }}</span>
                </span>
            </div>
        </div>

        <!-- Portfolio Value Card -->
        <div class="w-full px-4 mt-4">
            <div
                class="w-full rounded-xl p-5 relative overflow-hidden border border-border"
                style="background: linear-gradient(135deg, #0d2a40 0%, #0a3d30 100%)"
            >
                <div
                    class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-10 bg-primary"
                    style="transform: translate(30%, -30%)"
                ></div>
                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase">Portfolio Value</p>
                <p class="text-white text-3xl font-light mt-1 tracking-wide">
                    Kes <span class="font-semibold">{{ money(earnings.balance) }}</span>
                </p>
                <div class="w-12 h-px bg-primary mt-3 mb-3"></div>
                <div class="flex gap-6">
                    <div>
                        <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Earned</p>
                        <p class="text-success text-sm font-medium">
                            Kes {{ money(Number(earnings.referral) + Number(earnings.deposit)) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Deposited</p>
                        <p class="text-white text-sm">Kes {{ money(earnings.deposit) }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Team</p>
                        <p class="text-white text-sm">{{ downline }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TradingView Chart -->
        <div class="w-full px-4 mt-4">
            <div class="w-full rounded-xl overflow-hidden bg-card border border-border">
                <div class="flex items-center justify-between px-4 pt-3 pb-1">
                    <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase">Live Market</p>
                    <span class="text-primary text-[10px] tracking-widest uppercase">TradingView</span>
                </div>
                <div class="tradingview-widget-container" style="height: 220px">
                    <div ref="widgetContainer" class="tradingview-widget-container__widget" style="height: 220px; width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="w-full px-4 mt-4 grid grid-cols-3 gap-3">
            <div class="rounded-lg p-3 text-center bg-card border border-border">
                <Icon name="arrow-trend-up" class="text-primary mb-1" />
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Withdrawn</p>
                <p class="text-white text-xs font-medium mt-0.5">{{ moneyRound(earnings.withdraw) }}</p>
            </div>
            <div class="rounded-lg p-3 text-center bg-card border border-border">
                <Icon name="users" class="text-primary mb-1" />
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Active Team</p>
                <p class="text-white text-xs font-medium mt-0.5">{{ numActive }}</p>
            </div>
            <div class="rounded-lg p-3 text-center bg-card border border-border">
                <Icon name="coins" class="text-primary mb-1" />
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Referral</p>
                <p class="text-white text-xs font-medium mt-0.5">{{ moneyRound(earnings.referral) }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="w-full px-4 mt-4 grid grid-cols-2 gap-3 pb-28">
            <Link :href="route('deposit')" class="flex items-center gap-3 rounded-xl p-4 no-underline bg-card border border-border">
                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                    <Icon name="plus" class="text-primary" />
                </div>
                <div>
                    <p class="text-white text-xs font-medium">Deposit</p>
                    <p class="text-muted-foreground text-[10px]">Add funds</p>
                </div>
            </Link>
            <Link :href="route('withdraw')" class="flex items-center gap-3 rounded-xl p-4 no-underline bg-card border border-border">
                <div class="w-9 h-9 rounded-lg bg-success/10 flex items-center justify-center">
                    <Icon name="arrow-up-from-bracket" class="text-success" />
                </div>
                <div>
                    <p class="text-white text-xs font-medium">Withdraw</p>
                    <p class="text-muted-foreground text-[10px]">Cash out</p>
                </div>
            </Link>
            <Link :href="route('packages')" class="flex items-center gap-3 rounded-xl p-4 no-underline bg-card border border-border">
                <div class="w-9 h-9 rounded-lg bg-info/10 flex items-center justify-center">
                    <Icon name="layer-group" class="text-info" />
                </div>
                <div>
                    <p class="text-white text-xs font-medium">Plans</p>
                    <p class="text-muted-foreground text-[10px]">View &amp; invest</p>
                </div>
            </Link>
            <Link :href="route('task')" class="flex items-center gap-3 rounded-xl p-4 no-underline bg-card border border-border">
                <div class="w-9 h-9 rounded-lg bg-warning/10 flex items-center justify-center">
                    <Icon name="briefcase" class="text-warning" />
                </div>
                <div>
                    <p class="text-white text-xs font-medium">Orders</p>
                    <p class="text-muted-foreground text-[10px]">Claim earnings</p>
                </div>
            </Link>
        </div>
    </AppLayout>
</template>
