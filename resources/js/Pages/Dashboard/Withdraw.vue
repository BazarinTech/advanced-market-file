<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import Icon from '@/components/Icon.vue';
import type { WithdrawalAccount } from '@/types/models';

const props = defineProps<{
    account: WithdrawalAccount | null;
    withdrawal_min: number;
    withdrawal_fee: number;
}>();

const page = usePage();
const earnings = page.props.auth.user!.earnings!;

function money(v: string | number) {
    return Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

const setupForm = useForm({
    name: '',
    phone: '',
});

function submitSetup() {
    setupForm.post(route('withdraw.setup'));
}

const withdrawForm = useForm({
    amount: '',
});

function submitWithdraw() {
    withdrawForm.post(route('withdraw.store'));
}
</script>

<template>
    <Head title="Withdraw" />

    <AppLayout>
        <PageHeader title="Withdraw" back-route="account" />

        <div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">
            <!-- Balance card -->
            <div class="rounded-xl p-5 text-center bg-card border border-border">
                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase mb-1">Available Balance</p>
                <p class="text-white text-2xl font-light tracking-wide">Kes <span class="font-semibold">{{ money(earnings.balance) }}</span></p>
                <div class="w-10 h-px bg-primary mx-auto mt-3"></div>
            </div>

            <div
                v-if="Object.keys(setupForm.errors).length || Object.keys(withdrawForm.errors).length"
                class="px-4 py-2 rounded-lg text-sm text-destructive bg-destructive/10 border border-destructive/20"
            >
                {{ Object.values(setupForm.errors)[0] ?? Object.values(withdrawForm.errors)[0] }}
            </div>

            <template v-if="!account">
                <!-- No account — setup form -->
                <div class="rounded-xl px-4 py-3 flex items-start gap-3 bg-card border border-border">
                    <Icon name="triangle-exclamation" class="text-warning mt-0.5 text-sm shrink-0" />
                    <div class="space-y-1">
                        <p class="text-white text-xs tracking-widest uppercase font-light">Setup Required</p>
                        <p class="text-muted-foreground text-xs tracking-wide">
                            Set up your withdrawal account before making a withdrawal. This can only be set once — contact support to change it
                            later.
                        </p>
                    </div>
                </div>

                <div class="rounded-xl p-5 bg-card border border-border">
                    <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase mb-4">Set Up Withdrawal Account</p>
                    <form class="flex flex-col gap-4" @submit.prevent="submitSetup">
                        <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                            <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Full Name (as on M-Pesa)</label>
                            <input
                                v-model="setupForm.name"
                                type="text"
                                placeholder="Your full name"
                                class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                                required
                            />
                        </div>
                        <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                            <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">M-Pesa Phone Number</label>
                            <input
                                v-model="setupForm.phone"
                                type="tel"
                                placeholder="07xxxxxxxx"
                                class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                                required
                            />
                        </div>
                        <button
                            type="submit"
                            :disabled="setupForm.processing"
                            class="w-full bg-primary hover:bg-[#00a88a] text-primary-foreground font-bold py-3 rounded-lg tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                        >
                            {{ setupForm.processing ? 'Saving…' : 'Save Account' }}
                        </button>
                    </form>
                </div>
            </template>

            <template v-else>
                <!-- Withdrawal account card -->
                <div class="rounded-xl px-4 py-4 flex items-center justify-between bg-card border border-border">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <Icon name="mobile-screen" class="text-primary text-sm" />
                        </div>
                        <div>
                            <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Withdrawal Account</p>
                            <p class="text-white text-sm font-light mt-0.5">{{ account.name }}</p>
                            <p class="text-muted-foreground text-xs">{{ account.phone }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="bg-primary/10 text-primary text-[9px] px-2 py-1 rounded uppercase tracking-widest">M-Pesa</span>
                        <p class="text-xs text-muted-foreground mt-2">
                            <a :href="route('account')" class="text-primary no-underline hover:text-white text-[9px] tracking-widest">Change? Support</a>
                        </p>
                    </div>
                </div>

                <!-- Info box -->
                <div class="rounded-xl px-4 py-3 flex items-start gap-3 bg-card border border-border">
                    <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                    <div class="space-y-1">
                        <p class="text-muted-foreground text-xs">Fee charged: {{ withdrawal_fee }}%</p>
                        <p class="text-muted-foreground text-xs">Minimum withdrawal: Kes {{ Math.round(withdrawal_min).toLocaleString('en-US') }}</p>
                        <p class="text-muted-foreground text-xs">Withdrawals are processed automatically</p>
                    </div>
                </div>

                <!-- Withdrawal form -->
                <div class="rounded-xl p-5 bg-card border border-border">
                    <form class="flex flex-col gap-4" @submit.prevent="submitWithdraw">
                        <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                            <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Amount (KES)</label>
                            <input
                                v-model="withdrawForm.amount"
                                type="number"
                                step="1"
                                :min="withdrawal_min"
                                placeholder="e.g. 500"
                                class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                                required
                            />
                        </div>
                        <p class="text-muted-foreground text-xs px-1">
                            Sending to: <span class="text-white">{{ account.phone }}</span> · {{ account.name }}
                        </p>
                        <button
                            type="submit"
                            :disabled="withdrawForm.processing"
                            class="w-full bg-primary hover:bg-[#00a88a] text-primary-foreground font-bold py-3.5 rounded-lg tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                        >
                            {{ withdrawForm.processing ? 'Processing…' : 'Withdraw' }}
                        </button>
                    </form>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
