<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import AppLogo from '@/components/AppLogo.vue';
import Icon from '@/components/Icon.vue';

defineProps<{
    link_customer_support: string | null;
}>();

const form = useForm({ phone: '' });

function submit() {
    form.post(route('forgot.send'));
}
</script>

<template>
    <Head title="Forgot Password" />

    <AppLayout>
        <div class="flex flex-col items-center justify-center w-full min-h-screen px-6 py-10">
            <div class="text-center mb-8">
                <div class="flex items-center justify-center mx-auto mb-4">
                    <AppLogo />
                </div>
                <p class="text-foreground text-2xl font-bold tracking-widest uppercase">Mythos Task</p>
                <p class="text-muted-foreground text-xs tracking-[0.3em] uppercase mt-1">Account Recovery</p>
            </div>

            <div class="w-full rounded-2xl px-4 py-4 mb-4 flex items-start gap-3 bg-card border border-border">
                <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                <p class="text-muted-foreground text-xs tracking-wide leading-relaxed">
                    Enter the phone number on your account. We'll text you a code to reset your password.
                </p>
            </div>

            <form class="w-full rounded-2xl p-5 flex flex-col gap-3 bg-card border border-border" @submit.prevent="submit">
                <div class="rounded-xl px-4 py-3 bg-secondary border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Phone Number</label>
                    <input
                        v-model="form.phone"
                        type="tel"
                        placeholder="07XXXXXXXX"
                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                        required
                        autofocus
                    >
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-bold py-3.5 rounded-xl tracking-[0.2em] uppercase text-xs mt-1 disabled:opacity-70"
                >
                    {{ form.processing ? 'Sending...' : 'Send Code' }}
                </button>
            </form>

            <a
                v-if="link_customer_support"
                :href="link_customer_support"
                target="_blank"
                class="w-full mt-4 flex items-center justify-between rounded-2xl px-4 py-3.5 no-underline transition-colors bg-card border border-border hover:border-primary/40"
            >
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-success/10 flex items-center justify-center shrink-0">
                        <Icon name="whatsapp" class="text-success text-base" />
                    </div>
                    <span class="text-foreground text-sm font-light tracking-wide">Contact Support Instead</span>
                </div>
                <Icon name="angle-right" class="text-muted-foreground text-sm" />
            </a>

            <Link :href="route('login')" class="text-muted-foreground text-xs tracking-widest uppercase hover:text-primary mt-4">
                &larr; Back to Login
            </Link>
        </div>
    </AppLayout>
</template>
