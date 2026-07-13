<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import AppLogo from '@/components/AppLogo.vue';
import Icon from '@/components/Icon.vue';

const props = defineProps<{ phone: string }>();

const form = useForm({
    phone: props.phone,
    code: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('password.reset.submit'));
}
</script>

<template>
    <Head title="Reset Password" />

    <AppLayout>
        <div class="flex flex-col items-center justify-center w-full min-h-screen px-6 py-10">
            <div class="text-center mb-8">
                <div class="flex items-center justify-center mx-auto mb-4">
                    <AppLogo />
                </div>
                <p class="text-foreground text-2xl font-bold tracking-widest uppercase">Mythos Task</p>
                <p class="text-muted-foreground text-xs tracking-[0.3em] uppercase mt-1">Reset Password</p>
            </div>

            <div class="w-full rounded-2xl px-4 py-4 mb-4 flex items-start gap-3 bg-card border border-border">
                <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                <p class="text-muted-foreground text-xs tracking-wide leading-relaxed">
                    Enter the code sent to <span class="text-foreground font-medium">{{ props.phone }}</span> along with your new password.
                </p>
            </div>

            <form class="w-full flex flex-col gap-3" @submit.prevent="submit">
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Phone Number</label>
                    <input
                        v-model="form.phone"
                        type="tel"
                        placeholder="07XXXXXXXX"
                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                        required
                    >
                </div>
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Verification Code</label>
                    <input
                        v-model="form.code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="123456"
                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60 tracking-widest"
                        required
                        autofocus
                    >
                </div>
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">New Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Min. 8 characters"
                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                        required
                    >
                </div>
                <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Confirm New Password</label>
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
                    {{ form.processing ? 'Resetting...' : 'Reset Password' }}
                </button>
                <div class="text-center mt-1">
                    <Link :href="route('login')" class="text-muted-foreground text-xs tracking-widest uppercase hover:text-primary">
                        &larr; Back to Login
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
