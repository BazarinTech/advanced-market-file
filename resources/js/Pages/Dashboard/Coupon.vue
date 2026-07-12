<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import Icon from '@/components/Icon.vue';

const form = useForm({
    code: '',
});

function submit() {
    form.post(route('coupon.redeem'));
}

function uppercase() {
    form.code = form.code.toUpperCase();
}
</script>

<template>
    <Head title="Redeem Coupon" />

    <AppLayout>
        <PageHeader title="Redeem Coupon" back-route="account" />

        <div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">
            <!-- Hero card -->
            <div
                class="rounded-xl p-5 text-center relative overflow-hidden border border-border"
                style="background: linear-gradient(135deg, #0d2a40 0%, #0a3d30 100%)"
            >
                <div
                    class="absolute top-0 right-0 w-28 h-28 rounded-full opacity-10 bg-primary"
                    style="transform: translate(30%, -30%)"
                ></div>
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mx-auto mb-3">
                    <Icon name="ticket" class="text-primary text-xl" />
                </div>
                <p class="text-white text-sm font-medium tracking-wide mb-1">Got a coupon code?</p>
                <p class="text-muted-foreground text-xs tracking-wide">
                    Enter it below to receive your reward instantly credited to your balance.
                </p>
            </div>

            <!-- Redemption form -->
            <div class="rounded-xl p-5 bg-card border border-border">
                <form class="flex flex-col gap-4" @submit.prevent="submit">
                    <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Coupon Code</label>
                        <input
                            v-model="form.code"
                            type="text"
                            placeholder="e.g. TRADE2025"
                            autocomplete="off"
                            class="w-full bg-transparent outline-none text-sm text-white placeholder-border uppercase tracking-widest"
                            required
                            @input="uppercase"
                        />
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-primary hover:bg-[#00a88a] text-primary-foreground font-bold py-3.5 rounded-lg tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                    >
                        {{ form.processing ? 'Verifying…' : 'Redeem Coupon' }}
                    </button>
                </form>
            </div>

            <!-- Info box -->
            <div class="rounded-xl px-4 py-3 flex items-start gap-3 bg-card border border-border">
                <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                <div class="space-y-1">
                    <p class="text-muted-foreground text-xs">Each coupon can only be redeemed once per account</p>
                    <p class="text-muted-foreground text-xs">Rewards are credited instantly to your balance</p>
                    <p class="text-muted-foreground text-xs">Coupons are case-insensitive and may have expiry limits</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
