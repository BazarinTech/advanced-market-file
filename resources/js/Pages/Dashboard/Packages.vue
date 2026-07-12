<script setup lang="ts">
import { Head, usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Icon from '@/components/Icon.vue';
import type { Package } from '@/types/models';

const props = defineProps<{
    packages: Package[];
}>();

const page = usePage();
const earnings = page.props.auth.user!.earnings!;
const balance = Number(earnings.balance);

function roi(pkg: Package): number {
    const amount = Number(pkg.amount);
    if (amount <= 0) return 0;
    return Math.round((Number(pkg.daily) * pkg.days / amount) * 100);
}

function money(v: string | number) {
    return Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function moneyRound(v: string | number) {
    return Math.round(Number(v)).toLocaleString('en-US');
}

function canAfford(pkg: Package): boolean {
    return balance >= Number(pkg.amount);
}

// One independent form per package card.
const forms = Object.fromEntries(props.packages.map((pkg) => [pkg.id, useForm({ package: pkg.id })]));

function buy(pkg: Package) {
    if (!canAfford(pkg)) return;
    forms[pkg.id].post(route('home.buy'));
}
</script>

<template>
    <Head title="Investment Plans" />

    <AppLayout show-bottom-nav>
        <!-- Top Bar -->
        <div class="w-full flex items-center h-14 px-4 bg-secondary border-b border-border">
            <div class="w-7 h-7 rounded bg-primary flex items-center justify-center mr-3">
                <Icon name="layer-group" class="text-primary-foreground text-xs" />
            </div>
            <p class="text-white text-xs font-light tracking-[0.3em] uppercase">Investment Plans</p>
            <div class="ml-auto text-right">
                <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Balance</p>
                <p class="text-primary text-xs font-medium">Kes {{ money(earnings.balance) }}</p>
            </div>
        </div>

        <!-- Info strip -->
        <div class="mx-4 mt-4 rounded-lg px-4 py-3 flex items-start gap-3 bg-card border border-border">
            <Icon name="circle-info" class="text-primary mt-0.5 text-sm" />
            <p class="text-muted-foreground text-xs leading-relaxed">
                Select a plan to invest. Earnings are claimable daily from <span class="text-primary">9:00 AM</span> each day.
                Multiple plans can be held simultaneously.
            </p>
        </div>

        <!-- Package cards -->
        <div class="w-full px-4 mt-4 flex flex-col gap-4 pb-28">
            <form v-for="pkg in packages" :key="pkg.id" @submit.prevent="buy(pkg)">
                <div class="rounded-xl overflow-hidden bg-card border border-border">
                    <!-- Package header -->
                    <div class="relative">
                        <img :src="`/images/packages/${pkg.image}`" class="w-full object-cover" style="height: 120px" :alt="pkg.name" />
                        <div
                            class="absolute inset-0"
                            style="background: linear-gradient(to bottom, rgba(5, 15, 26, 0.2), rgba(5, 15, 26, 0.85))"
                        ></div>
                        <div class="absolute bottom-0 left-0 right-0 px-4 pb-3 flex items-end justify-between">
                            <p class="text-white font-semibold tracking-widest uppercase text-sm">{{ pkg.name }}</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-medium text-primary-foreground bg-primary">
                                {{ roi(pkg) }}% ROI
                            </span>
                        </div>
                    </div>
                    <!-- Package details -->
                    <div class="px-4 py-4">
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center">
                                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Price</p>
                                <p class="text-primary text-sm font-semibold mt-0.5">Kes {{ moneyRound(pkg.amount) }}</p>
                            </div>
                            <div class="text-center border-l border-r border-border">
                                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Daily</p>
                                <p class="text-white text-sm font-semibold mt-0.5">Kes {{ moneyRound(pkg.daily) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">{{ pkg.days }}d Total</p>
                                <p class="text-success text-sm font-semibold mt-0.5">Kes {{ moneyRound(Number(pkg.daily) * pkg.days) }}</p>
                            </div>
                        </div>
                        <button
                            type="submit"
                            :disabled="!canAfford(pkg) || forms[pkg.id].processing"
                            class="w-full py-3 rounded-lg text-xs font-semibold tracking-widest uppercase transition-opacity disabled:cursor-not-allowed"
                            :class="
                                canAfford(pkg)
                                    ? 'text-primary-foreground bg-primary hover:bg-[#00a88a] disabled:opacity-70'
                                    : 'text-muted-foreground cursor-not-allowed'
                            "
                        >
                            {{ forms[pkg.id].processing ? 'Processing…' : canAfford(pkg) ? 'Activate Plan' : 'Insufficient Balance' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
