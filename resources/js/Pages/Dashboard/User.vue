<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import Icon from '@/components/Icon.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const page = usePage();
const user = page.props.auth.user!;

const passModalOpen = ref(false);

const passwordForm = useForm({
    prevPass: '',
    newPass: '',
    conPass: '',
});

function submitPassword() {
    passwordForm.post(route('user.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passModalOpen.value = false;
            passwordForm.reset();
        },
    });
}

const logoutForm = useForm({});

function logout() {
    logoutForm.post(route('logout'));
}
</script>

<template>
    <Head title="Profile Settings" />

    <AppLayout>
        <PageHeader title="Profile Settings" back-route="account" />

        <div class="w-full px-4 mt-4 flex flex-col gap-2">
            <!-- Info rows -->
            <div class="rounded-xl overflow-hidden bg-card border border-border">
                <div class="flex items-center justify-between px-4 py-4 border-b border-border">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <Icon name="envelope" class="text-primary text-sm" />
                        </div>
                        <div>
                            <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Email</p>
                            <p class="text-white text-sm font-light mt-0.5">{{ user.email }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between px-4 py-4 border-b border-border">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <Icon name="phone" class="text-primary text-sm" />
                        </div>
                        <div>
                            <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Phone</p>
                            <p class="text-white text-sm font-light mt-0.5">{{ user.phone }}</p>
                        </div>
                    </div>
                </div>
                <button
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-4 text-left hover:bg-secondary transition-colors"
                    @click="passModalOpen = true"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-warning/10 flex items-center justify-center shrink-0">
                            <Icon name="lock" class="text-warning text-sm" />
                        </div>
                        <div>
                            <p class="text-white text-sm font-light">Change Password</p>
                            <p class="text-muted-foreground text-[10px] mt-0.5">Update your login password</p>
                        </div>
                    </div>
                    <Icon name="angle-right" class="text-border text-sm" />
                </button>
            </div>

            <!-- Sign out -->
            <form class="mt-2" @submit.prevent="logout">
                <button
                    type="submit"
                    :disabled="logoutForm.processing"
                    class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-colors disabled:opacity-70"
                    style="background: #1a0a0a; border: 1px solid #3a1a1a"
                >
                    <div class="w-9 h-9 rounded-lg bg-destructive/10 flex items-center justify-center shrink-0">
                        <Icon name="right-from-bracket" class="text-destructive text-sm" />
                    </div>
                    <span class="text-destructive text-sm tracking-wide font-light">Sign Out</span>
                </button>
            </form>
        </div>

        <!-- Password Dialog -->
        <Dialog v-model:open="passModalOpen">
            <DialogContent class="bg-card border-border">
                <DialogHeader>
                    <DialogTitle class="text-white text-xs tracking-[0.3em] uppercase text-center">Update Password</DialogTitle>
                </DialogHeader>

                <div
                    v-if="Object.keys(passwordForm.errors).length"
                    class="w-full px-4 py-2 rounded-lg text-sm text-destructive bg-destructive/10 border border-destructive/20"
                >
                    {{ Object.values(passwordForm.errors)[0] }}
                </div>

                <form class="flex flex-col gap-3" @submit.prevent="submitPassword">
                    <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Current Password</label>
                        <input
                            v-model="passwordForm.prevPass"
                            type="password"
                            placeholder="••••••••"
                            class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                        />
                    </div>
                    <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">New Password (min 8)</label>
                        <input
                            v-model="passwordForm.newPass"
                            type="password"
                            placeholder="••••••••"
                            class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                        />
                    </div>
                    <div class="rounded-lg px-4 py-3 bg-secondary border border-border">
                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Confirm New Password</label>
                        <input
                            v-model="passwordForm.conPass"
                            type="password"
                            placeholder="••••••••"
                            class="w-full bg-transparent outline-none text-sm text-white placeholder-border"
                        />
                    </div>
                    <button
                        type="submit"
                        :disabled="passwordForm.processing"
                        class="w-full bg-primary hover:bg-[#00a88a] text-primary-foreground font-bold py-3 rounded-lg tracking-widest uppercase text-xs mt-1 disabled:opacity-70"
                    >
                        {{ passwordForm.processing ? 'Updating…' : 'Update Password' }}
                    </button>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
