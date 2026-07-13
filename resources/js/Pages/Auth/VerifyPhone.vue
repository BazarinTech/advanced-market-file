<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import AppLogo from '@/components/AppLogo.vue';
import Icon from '@/components/Icon.vue';

const props = defineProps<{ phone: string }>();

const form = useForm({ code: '' });
const resendForm = useForm({});

function submit() {
    form.post(route('verify-phone.verify'));
}

function resend() {
    resendForm.post(route('verify-phone.resend'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Verify Phone" />

    <AppLayout>
        <div class="flex flex-col items-center justify-center w-full min-h-screen px-6">
            <div class="text-center mb-10">
                <div class="flex items-center justify-center mx-auto mb-4">
                    <AppLogo />
                </div>
                <p class="text-foreground text-2xl font-bold tracking-widest uppercase">Mythos Task</p>
                <p class="text-muted-foreground text-xs tracking-[0.3em] uppercase mt-1">Verify Your Phone</p>
            </div>

            <div class="w-full rounded-2xl px-4 py-4 mb-4 flex items-start gap-3 bg-card border border-border">
                <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                <p class="text-muted-foreground text-xs tracking-wide leading-relaxed">
                    We sent a 6-digit code by SMS to <span class="text-foreground font-medium">{{ props.phone }}</span>.
                    Enter it below to activate your account.
                </p>
            </div>

            <form class="w-full flex flex-col gap-4" @submit.prevent="submit">
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
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-bold py-3.5 rounded-2xl tracking-[0.2em] uppercase text-sm mt-2 disabled:opacity-70"
                >
                    {{ form.processing ? 'Verifying...' : 'Verify Phone' }}
                </button>
                <button
                    type="button"
                    :disabled="resendForm.processing"
                    class="text-muted-foreground text-xs tracking-widest uppercase hover:text-primary disabled:opacity-50"
                    @click="resend"
                >
                    {{ resendForm.processing ? 'Sending...' : "Didn't get a code? Resend" }}
                </button>
            </form>
        </div>
    </AppLayout>
</template>
