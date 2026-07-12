<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import Icon from '@/components/Icon.vue';

const page = usePage();
const earnings = page.props.auth.user!.earnings!;

function money(v: string | number) {
    return Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

const form = useForm({
    amount: '',
    phone: '',
});

function submit() {
    form.post(route('deposit.store'));
}
</script>

<template>
    <Head title="Deposit" />

    <AppLayout>
        <PageHeader title="Deposit" back-route="account" />

        <div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">
            <!-- Balance card -->
            <div class="rounded-xl p-5 text-center bg-card border border-border">
                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase mb-1">Available Balance</p>
                <p class="text-white text-2xl font-light tracking-wide">Kes <span class="font-semibold">{{ money(earnings.balance) }}</span></p>
                <div class="w-10 h-px bg-primary mx-auto mt-3"></div>
            </div>

            <!-- Info box -->
            <div class="rounded-xl px-4 py-3 flex items-start gap-3 bg-card border border-border">
                <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                <div class="space-y-1">
                    <p class="text-muted-foreground text-xs">Enter amount and phone number</p>
                    <p class="text-muted-foreground text-xs">Click submit — you will receive an M-Pesa STK pop-up</p>
                    <p class="text-muted-foreground text-xs">Enter your PIN and balance updates automatically</p>
                </div>
            </div>

            <div
                v-if="Object.keys(form.errors).length"
                class="px-4 py-2 rounded-lg text-sm text-destructive bg-destructive/10 border border-destructive/20"
            >
                {{ Object.values(form.errors)[0] }}
            </div>

            <!-- Form -->
            <div class="rounded-xl p-5 bg-card border border-border">
                <form class="flex flex-col gap-4" @submit.prevent="submit">
                    <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Amount (KES)</label>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="1"
                            min="400"
                            placeholder="e.g. 1000"
                            class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                            required
                        />
                    </div>
                    <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Phone Number</label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            placeholder="07xxxxxxxx"
                            class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                            required
                        />
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-primary hover:bg-[#00a88a] text-primary-foreground font-bold py-3.5 rounded-lg tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                    >
                        {{ form.processing ? 'Processing…' : 'Submit Deposit' }}
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
