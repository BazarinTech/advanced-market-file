<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import AppLogo from '@/components/AppLogo.vue';

const props = defineProps<{ refCode: string | null; refValid: boolean }>();

const form = useForm({
    email: '',
    phone: '',
    country: '254',
    ref: props.refCode ?? '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('register'));
}
</script>

<template>
    <Head title="Create Account" />

    <AppLayout>
        <div class="flex flex-col items-center justify-center w-full min-h-screen px-6 py-10">
            <div class="text-center mb-8">
                <div class="flex items-center justify-center mx-auto mb-4">
                    <AppLogo />
                </div>
                <p class="text-foreground text-2xl font-bold tracking-widest uppercase">Mythos Task</p>
                <p class="text-muted-foreground text-xs tracking-[0.3em] uppercase mt-1">Create your account</p>
            </div>

            <form class="w-full flex flex-col gap-3" @submit.prevent="submit">
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Email Address</label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="your@email.com"
                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                        required
                    >
                </div>
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Phone Number (Safaricom only)</label>
                    <input
                        v-model="form.phone"
                        type="text"
                        placeholder="07XXXXXXXX"
                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                        required
                    >
                    <p class="text-muted-foreground/70 text-[10px] mt-1">
                        Only Safaricom numbers are supported — you'll receive an SMS code to verify it.
                    </p>
                </div>
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Country</label>
                    <select v-model="form.country" class="w-full bg-card outline-none text-sm text-foreground" required>
                        <option value="254">Kenya</option>
                        <option value="256">Uganda</option>
                        <option value="255">Tanzania</option>
                        <option value="250">Other</option>
                    </select>
                </div>
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Referral Code</label>
                    <input
                        v-model="form.ref"
                        type="text"
                        inputmode="numeric"
                        maxlength="6"
                        placeholder="6-digit code"
                        class="w-full bg-transparent outline-none text-sm"
                        :class="props.refValid ? 'text-muted-foreground' : 'text-foreground placeholder-muted-foreground/60'"
                        :readonly="props.refValid"
                        required
                    >
                    <p v-if="!props.refValid" class="text-muted-foreground/70 text-[10px] mt-1">
                        Ask your inviter for their 6-digit referral code. An account cannot be created without one.
                    </p>
                    <p v-if="form.errors.ref" class="text-destructive text-[10px] mt-1">{{ form.errors.ref }}</p>
                </div>
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Min. 8 characters"
                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                        required
                    >
                </div>
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Confirm Password</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="Repeat password"
                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                        required
                    >
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-bold py-3.5 rounded-2xl tracking-[0.2em] uppercase text-sm mt-2 disabled:opacity-70"
                >
                    {{ form.processing ? 'Creating account...' : 'Create Account' }}
                </button>
                <div class="text-center mt-1">
                    <Link :href="route('login')" class="text-muted-foreground text-xs tracking-widest uppercase hover:text-primary">
                        Already have an account? Sign In
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
