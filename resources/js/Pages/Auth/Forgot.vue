<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Icon from '@/components/Icon.vue';

defineProps<{
    support_email: string | null;
    support_phone: string | null;
    support_network: string | null;
    support_url: string | null;
}>();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    network: '',
});

function submit() {
    form.post(route('forgot.submit'));
}
</script>

<template>
    <Head title="Forgot Password" />

    <AppLayout>
        <div class="flex flex-col items-center justify-center w-full min-h-screen px-6 py-10">
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-4">
                    <Icon name="lock" class="text-primary-foreground text-2xl" />
                </div>
                <p class="text-white text-2xl font-bold tracking-widest uppercase">Trade-Swing</p>
                <p class="text-muted-foreground text-xs tracking-[0.3em] uppercase mt-1">Account Recovery</p>
            </div>

            <div
                v-if="Object.keys(form.errors).length"
                class="w-full px-4 py-2 rounded-lg mb-4 text-sm text-destructive bg-destructive/10 border border-destructive/20"
            >
                <p v-for="(e, k) in form.errors" :key="k">{{ e }}</p>
            </div>

            <div class="w-full rounded-xl px-4 py-4 mb-4 flex items-start gap-3 bg-card border border-border">
                <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                <div class="space-y-1.5">
                    <p class="text-muted-foreground text-xs tracking-wide leading-relaxed">Fill in the form below with the details you used to create your account.</p>
                    <p class="text-muted-foreground text-xs tracking-wide leading-relaxed">Our support team will verify your identity and assist you within 24 hours.</p>
                    <p class="text-muted-foreground text-xs tracking-wide leading-relaxed">Alternatively, reach us directly via the contact link below.</p>
                </div>
            </div>

            <div v-if="support_url" class="w-full mb-4">
                <a
                    :href="support_url"
                    target="_blank"
                    class="w-full flex items-center justify-between rounded-xl px-4 py-3.5 no-underline transition-colors bg-card border border-border"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-success/10 flex items-center justify-center shrink-0">
                            <Icon name="whatsapp" class="text-success text-base" />
                        </div>
                        <span class="text-white text-sm font-light tracking-wide">Contact Support Now</span>
                    </div>
                    <Icon name="angle-right" class="text-border text-sm" />
                </a>
            </div>

            <div class="w-full rounded-xl p-5 mb-6 bg-card border border-border">
                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase mb-4">Submit Recovery Request</p>

                <form class="flex flex-col gap-3" @submit.prevent="submit">
                    <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Full Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="As used on account"
                            class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                            required
                        >
                    </div>

                    <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Email Address</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="your@email.com"
                            class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                            required
                        >
                    </div>

                    <div class="rounded-lg px-4 py-3 flex gap-3 items-start bg-secondary border border-border">
                        <div class="flex-1">
                            <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Phone Number</label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                placeholder="07xxxxxxxx"
                                class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                                required
                            >
                        </div>
                        <div class="shrink-0 pt-5">
                            <select
                                v-model="form.network"
                                class="bg-secondary outline-none text-xs tracking-wide text-muted-foreground rounded-lg px-2 py-1 border border-border"
                                required
                            >
                                <option value="" disabled>Network</option>
                                <option value="Safaricom">Safaricom</option>
                                <option value="Airtel">Airtel</option>
                                <option value="Telkom">Telkom</option>
                                <option value="Faiba">Faiba</option>
                            </select>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-primary hover:bg-[#00a88a] text-primary-foreground font-bold py-3.5 rounded-lg tracking-[0.2em] uppercase text-xs mt-1 disabled:opacity-70"
                    >
                        {{ form.processing ? 'Submitting...' : 'Submit Request' }}
                    </button>
                </form>
            </div>

            <Link :href="route('login')" class="text-muted-foreground text-xs tracking-widest uppercase hover:text-primary">
                &larr; Back to Login
            </Link>
        </div>
    </AppLayout>
</template>
