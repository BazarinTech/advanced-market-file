<script setup lang="ts">
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import Icon from '@/components/Icon.vue';
import type { User } from '@/types/models';

const props = defineProps<{
    downline: User[];
    numActive: number;
    numDeposited: number;
    depositTotals: Record<string, number>;
}>();

const page = usePage();
const user = page.props.auth.user!;
const earnings = user.earnings!;

function money(v: string | number) {
    return Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function moneyRound(v: string | number) {
    return Math.round(Number(v)).toLocaleString('en-US');
}

function maskPhone(phone: string): string {
    return phone.slice(0, 2) + '***' + phone.slice(-2);
}

const inviteLink = `${window.location.origin}/register?invite=${user.ID}`;
const copied = ref(false);
const inviteInput = ref<HTMLInputElement | null>(null);

async function copyInviteLink() {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(inviteLink);
        } else {
            throw new Error('clipboard API unavailable');
        }
    } catch {
        const input = inviteInput.value;
        if (input) {
            input.select();
            input.setSelectionRange(0, 99999);
            try {
                document.execCommand('copy');
            } catch {
                // ignore
            }
            input.blur();
        }
    }
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2500);
}
</script>

<template>
    <Head title="Team" />

    <AppLayout>
        <PageHeader title="Team" back-route="account" />

        <!-- Stats Grid -->
        <div class="w-full grid grid-cols-2 border-b border-border">
            <div class="flex flex-col gap-1 items-center py-4 border-r border-b border-border bg-card">
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Referral Bonus</p>
                <p class="text-primary text-sm font-semibold">Kes {{ money(earnings.referral) }}</p>
            </div>
            <div class="flex flex-col gap-1 items-center py-4 border-b border-border bg-card">
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Total Members</p>
                <p class="text-white text-sm font-semibold">{{ downline.length }}</p>
            </div>
            <div class="flex flex-col gap-1 items-center py-4 border-r border-border bg-card">
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Deposited</p>
                <p class="text-white text-sm font-semibold">{{ numDeposited }}</p>
            </div>
            <div class="flex flex-col gap-1 items-center py-4 bg-card">
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Active</p>
                <p class="text-success text-sm font-semibold">{{ numActive }}</p>
            </div>
        </div>

        <div class="w-full px-4 mt-4 flex flex-col gap-3 pb-10">
            <!-- Referral Link Card -->
            <div class="rounded-xl p-4 bg-card border border-border">
                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase mb-1">Your Referral Code</p>
                <p class="text-primary text-lg font-semibold tracking-widest mb-4">{{ user.ID }}</p>

                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase mb-2">Invite Link</p>
                <div class="flex items-center gap-2">
                    <input
                        ref="inviteInput"
                        type="text"
                        readonly
                        :value="inviteLink"
                        class="flex-1 rounded-lg px-3 py-2.5 text-xs text-muted-foreground outline-none truncate tracking-wide bg-secondary border border-border"
                    />
                    <button
                        type="button"
                        class="bg-primary hover:bg-[#00a88a] text-primary-foreground text-xs font-bold px-4 py-2.5 rounded-lg uppercase tracking-widest whitespace-nowrap transition-colors"
                        @click="copyInviteLink"
                    >
                        <template v-if="copied">✓ Copied!</template>
                        <template v-else><Icon name="copy" class="mr-1" /> Copy</template>
                    </button>
                </div>
            </div>

            <!-- Downline List -->
            <div v-if="downline.length === 0" class="text-center mt-10">
                <Icon name="users" class="text-border text-4xl mb-3" />
                <p class="text-muted-foreground tracking-widest uppercase text-xs">No members yet</p>
            </div>
            <div
                v-for="member in downline"
                :key="member.ID"
                class="rounded-xl px-4 py-3 flex items-center justify-between bg-card border border-border"
            >
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                        <Icon name="user" class="text-primary text-sm" />
                    </div>
                    <div>
                        <p class="text-white text-sm font-light tracking-wide">{{ maskPhone(member.phone) }}</p>
                        <p
                            class="text-xs tracking-widest uppercase mt-0.5"
                            :class="member.status === 'Active' ? 'text-success' : 'text-destructive'"
                        >
                            {{ member.status }}
                        </p>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-muted-foreground text-[9px] tracking-widest uppercase mb-0.5">Deposited</p>
                    <p class="text-white text-sm font-semibold">Kes {{ moneyRound(depositTotals[member.email] ?? 0) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-muted-foreground text-[10px]">{{ member.date }}</p>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div
            class="fixed bottom-6 left-1/2 rounded-xl text-white text-xs px-5 py-2.5 shadow-xl z-50 flex items-center gap-2 uppercase tracking-widest bg-card border border-border transition-all"
            :style="{
                opacity: copied ? 1 : 0,
                transform: `translateX(-50%) translateY(${copied ? 0 : 16}px)`,
                pointerEvents: 'none',
            }"
        >
            <Icon name="circle-check" class="text-primary" /> Link copied!
        </div>
    </AppLayout>
</template>
