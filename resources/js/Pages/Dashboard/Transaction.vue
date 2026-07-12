<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import Icon from '@/components/Icon.vue';
import type { Transaction } from '@/types/models';

defineProps<{
    transactions: Transaction[];
}>();

const CREDIT_TYPES: Transaction['type'][] = ['Deposit', 'Deposits', 'Referral'];

function isCredit(tx: Transaction): boolean {
    return CREDIT_TYPES.includes(tx.type);
}

function statusClass(status: Transaction['status']): string {
    if (status === 'Success' || status === 'Approved') return 'text-primary';
    if (status === 'Pending') return 'text-warning';
    return 'text-destructive';
}

function money(v: string | number) {
    return Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>

<template>
    <Head title="Transactions" />

    <AppLayout>
        <PageHeader title="Transactions" back-route="account" />

        <div class="w-full flex flex-col px-4 mt-4 pb-10 gap-3">
            <div v-if="transactions.length === 0" class="text-center mt-16">
                <Icon name="receipt" class="text-border text-4xl mb-3" />
                <p class="text-muted-foreground tracking-widest uppercase text-xs">No transactions yet</p>
            </div>
            <div
                v-for="tx in transactions"
                :key="tx.ID"
                class="rounded-xl px-4 py-3 flex items-center justify-between bg-card border border-border"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                        :class="isCredit(tx) ? 'bg-success/10' : 'bg-destructive/10'"
                    >
                        <Icon :name="isCredit(tx) ? 'arrow-down' : 'arrow-up'" :class="isCredit(tx) ? 'text-success' : 'text-destructive'" class="text-sm" />
                    </div>
                    <div>
                        <p class="text-white text-sm font-medium tracking-wide">{{ tx.type }}</p>
                        <p class="text-xs tracking-widest mt-0.5" :class="statusClass(tx.status)">{{ tx.status }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold" :class="isCredit(tx) ? 'text-success' : 'text-destructive'">
                        {{ isCredit(tx) ? '+' : '-' }}Kes {{ money(tx.amount) }}
                    </p>
                    <p class="text-muted-foreground text-[10px] mt-0.5">{{ tx.date }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
