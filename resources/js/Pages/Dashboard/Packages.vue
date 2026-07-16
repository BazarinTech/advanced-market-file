<script setup lang="ts">
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AppLayout from '@/layouts/AppLayout.vue';
import Icon from '@/components/Icon.vue';
import { useCurrency } from '@/composables/useCurrency';
import type { Package } from '@/types/models';

const props = defineProps<{
    packages: Package[];
    claimedOneTimePackages: string[];
}>();

const page = usePage();
const earnings = page.props.auth.user!.earnings!;
const balance = Number(earnings.balance);

const { money, moneyRound } = useCurrency();

function roi(pkg: Package): number {
    const amount = Number(pkg.amount);
    if (amount <= 0) return 0;
    return Math.round((Number(pkg.daily) * pkg.days / amount) * 100);
}

function canAfford(pkg: Package): boolean {
    return balance >= Number(pkg.amount);
}

function alreadyClaimed(pkg: Package): boolean {
    return pkg.one_time_only && props.claimedOneTimePackages.includes(pkg.name);
}

// One independent form per package card.
const forms = Object.fromEntries(props.packages.map((pkg) => [pkg.id, useForm({ package: pkg.id })]));

function buy(pkg: Package) {
    if (alreadyClaimed(pkg)) return;
    if (!canAfford(pkg)) {
        toast.error('Insufficient balance. Please recharge your account to start this task.');
        return;
    }
    forms[pkg.id].post(route('home.buy'));
}
</script>

<template>
    <Head title="Tasks" />

    <AppLayout show-bottom-nav>
        <!-- Top Bar -->
        <div class="w-full flex items-center h-14 px-4 bg-card border-b border-border">
            <div class="w-7 h-7 rounded-lg bg-primary flex items-center justify-center mr-3">
                <Icon name="layer-group" class="text-primary-foreground text-xs" />
            </div>
            <p class="text-foreground text-xs font-light tracking-[0.3em] uppercase">Task Plans</p>
            <div class="ml-auto text-right">
                <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Balance</p>
                <p class="text-primary text-xs font-medium">{{ money(earnings.balance) }}</p>
            </div>
        </div>

        <!-- Package cards -->
        <div class="w-full px-4 mt-4 flex flex-col gap-4 pb-32">
            <form v-for="pkg in packages" :key="pkg.id" @submit.prevent="buy(pkg)">
                <div class="rounded-2xl overflow-hidden bg-card border border-border">
                    <!-- Package header -->
                    <div class="relative">
                        <img :src="pkg.image_url ?? '/images/8.jpeg'" class="w-full object-cover" style="height: 120px" :alt="pkg.name" />
                        <div
                            class="absolute inset-0"
                            style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.85))"
                        ></div>
                        <div class="absolute bottom-0 left-0 right-0 px-4 pb-3 flex items-end justify-between">
                            <p class="text-white font-semibold tracking-widest uppercase text-sm">{{ pkg.name }}</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-medium text-primary-foreground bg-primary">
                                {{ roi(pkg) }}% Reward
                            </span>
                        </div>
                    </div>
                    <!-- Package details -->
                    <div class="px-4 py-4">
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center">
                                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Price</p>
                                <p class="text-primary text-sm font-semibold mt-0.5">{{ moneyRound(pkg.amount) }}</p>
                            </div>
                            <div class="text-center border-l border-r border-border">
                                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Daily</p>
                                <p class="text-foreground text-sm font-semibold mt-0.5">{{ moneyRound(pkg.daily) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Per Month</p>
                                <p class="text-success text-sm font-semibold mt-0.5">{{ moneyRound(Number(pkg.daily) * pkg.days) }}/mo</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mb-4 text-[10px] text-muted-foreground">
                            <span class="flex items-center gap-1">
                                <Icon name="bolt" class="text-primary" />
                                {{ pkg.tasks_per_day }} task{{ pkg.tasks_per_day === 1 ? '' : 's' }}/day
                            </span>
                            <span v-if="pkg.task_category">· {{ pkg.task_category }} questions</span>
                        </div>
                        <button
                            type="submit"
                            :disabled="forms[pkg.id].processing || alreadyClaimed(pkg)"
                            class="w-full py-3 rounded-2xl text-xs font-semibold tracking-widest uppercase transition-opacity disabled:cursor-not-allowed"
                            :class="
                                alreadyClaimed(pkg)
                                    ? 'text-muted-foreground bg-secondary'
                                    : 'text-primary-foreground bg-primary hover:bg-amber-700 disabled:opacity-70'
                            "
                        >
                            {{ alreadyClaimed(pkg) ? 'Already Claimed' : forms[pkg.id].processing ? 'Processing…' : 'Start Task' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
