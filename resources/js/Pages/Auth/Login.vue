<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Icon from '@/components/Icon.vue';

const form = useForm({
    email: '',
    password: '',
});

function submit() {
    form.post(route('login'));
}
</script>

<template>
    <Head title="Sign In" />

    <AppLayout>
        <div class="flex flex-col items-center justify-center w-full min-h-screen px-6">
            <div class="text-center mb-10">
                <div class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-4">
                    <Icon name="box" class="text-primary-foreground text-2xl" />
                </div>
                <p class="text-foreground text-2xl font-bold tracking-widest uppercase">Mythos Task</p>
                <p class="text-muted-foreground text-xs tracking-[0.3em] uppercase mt-1">Welcome back</p>
            </div>

            <form class="w-full flex flex-col gap-4" @submit.prevent="submit">
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
                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="••••••••"
                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                        required
                    >
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-bold py-3.5 rounded-2xl tracking-[0.2em] uppercase text-sm mt-2 flex items-center justify-center gap-2 disabled:opacity-70"
                >
                    {{ form.processing ? 'Signing in...' : 'Sign In' }}
                </button>
                <div class="flex items-center justify-between mt-1">
                    <Link :href="route('forgot')" class="text-muted-foreground text-xs tracking-widest uppercase hover:text-primary">
                        Forgot Password?
                    </Link>
                    <Link :href="route('register')" class="text-primary text-xs tracking-widest uppercase hover:text-amber-700">
                        Create Account
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
