<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import Icon from '@/components/Icon.vue';
import type { Package } from '@/types/models';

defineProps<{
    packages: Package[];
}>();

function moneyRound(v: string | number) {
    return Math.round(Number(v)).toLocaleString('en-US');
}

function roi(pkg: Package): string {
    const amount = Number(pkg.amount);
    if (amount <= 0) return '∞';
    return Math.round(((Number(pkg.daily) * pkg.days) / amount) * 100) + '%';
}
</script>

<template>
    <Head title="Tasks" />

    <AppLayout>
        <PageHeader title="Tasks" back-route="account" />

        <div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">
            <!-- Section heading -->
            <div class="flex items-center gap-3">
                <div class="w-4 h-px bg-primary"></div>
                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase">Tasks at a Glance</p>
                <div class="flex-1 h-px bg-border"></div>
            </div>

            <!-- Comparison table -->
            <div class="rounded-2xl overflow-hidden overflow-x-auto bg-card border border-border">
                <table class="w-full text-xs border-collapse" style="min-width: 340px">
                    <thead>
                        <tr class="bg-secondary border-b border-border">
                            <td class="px-3 py-3 text-left font-light tracking-[0.2em] uppercase text-primary border-r border-border">Task</td>
                            <td
                                v-for="pkg in packages"
                                :key="pkg.id"
                                class="px-3 py-3 text-center font-light tracking-widest uppercase text-foreground border-r border-border"
                            >
                                {{ pkg.name }}
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Price -->
                        <tr class="border-b border-border">
                            <td class="px-3 py-3 text-primary tracking-widest uppercase font-light whitespace-nowrap border-r border-border bg-card">
                                Price
                            </td>
                            <td
                                v-for="pkg in packages"
                                :key="pkg.id"
                                class="px-3 py-3 text-center text-foreground font-semibold tracking-wide border-r border-border bg-card"
                            >
                                Kes {{ moneyRound(pkg.amount) }}
                            </td>
                        </tr>
                        <!-- Cycle -->
                        <tr class="border-b border-border">
                            <td
                                class="px-3 py-3 text-primary tracking-widest uppercase font-light whitespace-nowrap border-r border-border bg-secondary"
                            >
                                Cycle
                            </td>
                            <td
                                v-for="pkg in packages"
                                :key="pkg.id"
                                class="px-3 py-3 text-center text-muted-foreground tracking-wide border-r border-border bg-secondary"
                            >
                                {{ pkg.days }} days
                            </td>
                        </tr>
                        <!-- Daily -->
                        <tr class="border-b border-border">
                            <td class="px-3 py-3 text-primary tracking-widest uppercase font-light whitespace-nowrap border-r border-border bg-card">
                                Daily
                            </td>
                            <td
                                v-for="pkg in packages"
                                :key="pkg.id"
                                class="px-3 py-3 text-center text-foreground font-semibold tracking-wide border-r border-border bg-card"
                            >
                                Kes {{ moneyRound(pkg.daily) }}
                            </td>
                        </tr>
                        <!-- Total -->
                        <tr class="border-b border-border">
                            <td
                                class="px-3 py-3 text-primary tracking-widest uppercase font-light whitespace-nowrap border-r border-border bg-secondary"
                            >
                                Total
                            </td>
                            <td
                                v-for="pkg in packages"
                                :key="pkg.id"
                                class="px-3 py-3 text-center text-foreground font-semibold tracking-wide border-r border-border bg-secondary"
                            >
                                Kes {{ moneyRound(Number(pkg.daily) * pkg.days) }}
                            </td>
                        </tr>
                        <!-- Reward -->
                        <tr>
                            <td
                                class="px-3 py-3 text-primary tracking-widest uppercase font-light whitespace-nowrap border-r border-border bg-secondary"
                            >
                                Reward
                            </td>
                            <td
                                v-for="pkg in packages"
                                :key="pkg.id"
                                class="px-3 py-3 text-center text-primary font-semibold tracking-widest border-r border-border bg-secondary"
                            >
                                {{ roi(pkg) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase text-right px-3 py-2">
                    Total reward over full cycle
                </p>
            </div>

            <!-- Withdrawal terms heading -->
            <div class="flex items-center gap-3">
                <div class="w-4 h-px bg-primary"></div>
                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase">Withdrawal Terms</p>
                <div class="flex-1 h-px bg-border"></div>
            </div>

            <!-- Withdrawal terms -->
            <div class="rounded-2xl overflow-hidden grid grid-cols-3 bg-card border border-border">
                <div class="flex flex-col items-center py-5 px-2 gap-1.5 border-r border-border">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center mb-1">
                        <Icon name="money-bill-wave" class="text-primary text-sm" />
                    </div>
                    <p class="text-muted-foreground text-[9px] tracking-widest uppercase text-center">Min. Withdrawal</p>
                    <p class="text-foreground text-sm font-semibold">Kes 200</p>
                </div>
                <div class="flex flex-col items-center py-5 px-2 gap-1.5 border-r border-border">
                    <div class="w-9 h-9 rounded-xl bg-warning/10 flex items-center justify-center mb-1">
                        <Icon name="percent" class="text-warning text-sm" />
                    </div>
                    <p class="text-muted-foreground text-[9px] tracking-widest uppercase text-center">Fee Charged</p>
                    <p class="text-foreground text-sm font-semibold">6%</p>
                </div>
                <div class="flex flex-col items-center py-5 px-2 gap-1.5">
                    <div class="w-9 h-9 rounded-xl bg-success/10 flex items-center justify-center mb-1">
                        <Icon name="bolt" class="text-success text-sm" />
                    </div>
                    <p class="text-muted-foreground text-[9px] tracking-widest uppercase text-center">Processing</p>
                    <p class="text-foreground text-sm font-semibold">Instant</p>
                </div>
            </div>

            <!-- CTA -->
            <Link
                :href="route('packages')"
                class="w-full rounded-2xl bg-primary hover:bg-amber-700 text-primary-foreground font-bold text-xs py-3.5 tracking-[0.2em] uppercase text-center no-underline transition-colors"
            >
                View Available Tasks
            </Link>
        </div>
    </AppLayout>
</template>
