<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import Icon from '@/components/Icon.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useCurrency } from '@/composables/useCurrency';
import type { WithdrawalAccount } from '@/types/models';

const props = defineProps<{
    accounts: { mpesa: WithdrawalAccount | null; crypto: WithdrawalAccount | null };
    withdrawal_min: number;
    withdrawal_fee: number;
}>();

const page = usePage();
const earnings = page.props.auth.user!.earnings!;

const { money, toKes, toUsd } = useCurrency();

const activeTab = ref<'mpesa' | 'crypto'>('mpesa');
const editing = ref(false);
const codeSent = ref(false);

const account = computed(() => props.accounts[activeTab.value]);

watch(activeTab, () => {
    editing.value = false;
    codeSent.value = false;
});

// ── Setup / edit form ──
const setupForm = useForm({
    method: 'mpesa' as 'mpesa' | 'crypto',
    name: '',
    phone: '',
    crypto_address: '',
    code: '',
});

function startEdit() {
    editing.value = true;
    codeSent.value = false;
    setupForm.reset();
    setupForm.method = activeTab.value;
}

const sendCodeForm = useForm({ method: 'mpesa' as 'mpesa' | 'crypto' });

function sendCode() {
    sendCodeForm.method = activeTab.value;
    sendCodeForm.post(route('withdraw.account.send-code'), {
        preserveScroll: true,
        onSuccess: () => {
            codeSent.value = true;
        },
    });
}

function submitSetup() {
    setupForm.method = activeTab.value;
    setupForm.post(route('withdraw.account.update'), {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = false;
            codeSent.value = false;
            setupForm.reset();
        },
    });
}

// ── Withdrawal request form ──
const withdrawForm = useForm({
    method: 'mpesa' as 'mpesa' | 'crypto',
    amount: '',
});

const kesPreview = computed(() => {
    const amount = Number(withdrawForm.amount);
    if (!amount || amount <= 0) return null;
    return toKes(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});

function submitWithdraw() {
    withdrawForm.method = activeTab.value;
    withdrawForm.post(route('withdraw.store'), { preserveScroll: true });
}

const minUsd = computed(() => toUsd(props.withdrawal_min).toFixed(2));
</script>

<template>
    <Head title="Withdraw" />

    <AppLayout>
        <PageHeader title="Withdraw" back-route="account" />

        <div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">
            <!-- Balance card -->
            <div class="rounded-2xl p-5 text-center bg-card border border-border">
                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase mb-1">Available Balance</p>
                <p class="text-foreground text-2xl font-light tracking-wide">{{ money(earnings.balance) }}</p>
                <div class="w-10 h-px bg-primary mx-auto mt-3"></div>
            </div>

            <div
                v-if="Object.keys(setupForm.errors).length || Object.keys(withdrawForm.errors).length || Object.keys(sendCodeForm.errors).length"
                class="px-4 py-2 rounded-lg text-sm text-destructive bg-destructive/10 border border-destructive/20"
            >
                {{ Object.values(setupForm.errors)[0] ?? Object.values(withdrawForm.errors)[0] ?? Object.values(sendCodeForm.errors)[0] }}
            </div>

            <Tabs v-model="activeTab" class="w-full">
                <TabsList class="w-full h-auto rounded-2xl overflow-hidden bg-card border border-border p-0">
                    <TabsTrigger
                        value="mpesa"
                        class="flex-1 py-2.5 text-xs tracking-widest uppercase font-medium rounded-none data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-none text-muted-foreground"
                    >
                        M-Pesa
                    </TabsTrigger>
                    <TabsTrigger
                        value="crypto"
                        class="flex-1 py-2.5 text-xs tracking-widest uppercase font-medium rounded-none data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-none text-muted-foreground"
                    >
                        Crypto (USDT)
                    </TabsTrigger>
                </TabsList>

                <TabsContent :value="activeTab" class="w-full mt-4 flex flex-col gap-4">
                    <!-- No account yet — first-time setup, no OTP required -->
                    <template v-if="!account && !editing">
                        <div class="rounded-2xl px-4 py-3 flex items-start gap-3 bg-card border border-border">
                            <Icon name="triangle-exclamation" class="text-warning mt-0.5 text-sm shrink-0" />
                            <div class="space-y-1">
                                <p class="text-foreground text-xs tracking-widest uppercase font-light">Setup Required</p>
                                <p class="text-muted-foreground text-xs tracking-wide">
                                    Set up your {{ activeTab === 'mpesa' ? 'M-Pesa' : 'crypto' }} withdrawal account before making a withdrawal.
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="w-full rounded-2xl bg-primary hover:bg-amber-700 text-primary-foreground font-bold text-xs py-3 tracking-widest uppercase"
                            @click="startEdit"
                        >
                            Set Up {{ activeTab === 'mpesa' ? 'M-Pesa' : 'Crypto' }} Account
                        </button>
                    </template>

                    <!-- Editing (first-time setup, or OTP-gated edit of an existing account) -->
                    <template v-else-if="editing">
                        <div class="rounded-2xl p-5 bg-card border border-border">
                            <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase mb-4">
                                {{ account ? 'Verify & Edit' : 'Set Up' }} {{ activeTab === 'mpesa' ? 'M-Pesa' : 'Crypto' }} Account
                            </p>

                            <div v-if="account && !codeSent" class="flex flex-col gap-3">
                                <p class="text-muted-foreground text-xs">
                                    For your security, changing an existing withdrawal account requires verifying a code sent to your registered phone.
                                </p>
                                <button
                                    type="button"
                                    :disabled="sendCodeForm.processing"
                                    class="w-full bg-secondary hover:bg-border text-foreground font-semibold py-3 rounded-2xl tracking-widest uppercase text-xs disabled:opacity-60"
                                    @click="sendCode"
                                >
                                    {{ sendCodeForm.processing ? 'Sending…' : 'Send Verification Code' }}
                                </button>
                            </div>

                            <form v-else class="flex flex-col gap-4" @submit.prevent="submitSetup">
                                <template v-if="activeTab === 'mpesa'">
                                    <div class="rounded-2xl px-4 py-3 bg-secondary border border-border">
                                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Full Name (as on M-Pesa)</label>
                                        <input
                                            v-model="setupForm.name"
                                            type="text"
                                            placeholder="Your full name"
                                            class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                                            required
                                        />
                                    </div>
                                    <div class="rounded-2xl px-4 py-3 bg-secondary border border-border">
                                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">M-Pesa Phone Number</label>
                                        <input
                                            v-model="setupForm.phone"
                                            type="tel"
                                            placeholder="07xxxxxxxx"
                                            class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                                            required
                                        />
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="rounded-2xl px-4 py-3 bg-secondary border border-border">
                                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">USDT-TRC20 Address</label>
                                        <input
                                            v-model="setupForm.crypto_address"
                                            type="text"
                                            placeholder="T..."
                                            class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60 font-mono"
                                            required
                                        />
                                    </div>
                                </template>
                                <div v-if="account" class="rounded-2xl px-4 py-3 bg-secondary border border-border">
                                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Verification Code</label>
                                    <input
                                        v-model="setupForm.code"
                                        type="text"
                                        inputmode="numeric"
                                        autocomplete="one-time-code"
                                        placeholder="123456"
                                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60 tracking-widest"
                                        required
                                    />
                                </div>
                                <button
                                    type="submit"
                                    :disabled="setupForm.processing"
                                    class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-bold py-3 rounded-2xl tracking-widest uppercase text-xs disabled:opacity-50"
                                >
                                    {{ setupForm.processing ? 'Saving…' : 'Save Account' }}
                                </button>
                            </form>
                        </div>
                    </template>

                    <!-- Account exists — summary + withdrawal form -->
                    <template v-else>
                        <div class="rounded-2xl px-4 py-4 flex items-center justify-between bg-card border border-border">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                                    <Icon :name="activeTab === 'mpesa' ? 'mobile-screen' : 'coins'" class="text-primary text-sm" />
                                </div>
                                <div v-if="activeTab === 'mpesa'">
                                    <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Withdrawal Account</p>
                                    <p class="text-foreground text-sm font-light mt-0.5">{{ account!.name }}</p>
                                    <p class="text-muted-foreground text-xs">{{ account!.phone }}</p>
                                </div>
                                <div v-else>
                                    <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Crypto Address (TRC20)</p>
                                    <p class="text-foreground text-xs font-mono mt-0.5 break-all">{{ account!.crypto_address }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="bg-primary/10 text-primary text-[9px] px-2 py-1 rounded uppercase tracking-widest">
                                    {{ activeTab === 'mpesa' ? 'M-Pesa' : 'Crypto' }}
                                </span>
                                <p class="text-xs mt-2">
                                    <button type="button" class="text-primary text-[9px] tracking-widest uppercase" @click="startEdit">Change</button>
                                </p>
                            </div>
                        </div>

                        <!-- Info box -->
                        <div class="rounded-2xl px-4 py-3 flex items-start gap-3 bg-card border border-border">
                            <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                            <div class="space-y-1">
                                <p class="text-muted-foreground text-xs">Fee charged: {{ withdrawal_fee }}%</p>
                                <p class="text-muted-foreground text-xs">Minimum withdrawal: ${{ minUsd }}</p>
                                <p class="text-muted-foreground text-xs">
                                    {{ activeTab === 'mpesa' ? 'Withdrawals are processed automatically' : 'Crypto withdrawals are reviewed and processed within ~30 minutes' }}
                                </p>
                            </div>
                        </div>

                        <!-- Withdrawal form -->
                        <div class="rounded-2xl p-5 bg-card border border-border">
                            <form class="flex flex-col gap-4" @submit.prevent="submitWithdraw">
                                <div class="rounded-2xl px-4 py-3 bg-secondary border border-border">
                                    <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Amount (USD)</label>
                                    <input
                                        v-model="withdrawForm.amount"
                                        type="number"
                                        step="0.01"
                                        :min="minUsd"
                                        placeholder="e.g. 5"
                                        class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                                        required
                                    />
                                    <p v-if="kesPreview && activeTab === 'mpesa'" class="text-muted-foreground text-[10px] mt-1">
                                        ≈ KES {{ kesPreview }} after fee
                                    </p>
                                </div>
                                <p class="text-muted-foreground text-xs px-1">
                                    <template v-if="activeTab === 'mpesa'">
                                        Sending to: <span class="text-foreground">{{ account!.phone }}</span> · {{ account!.name }}
                                    </template>
                                    <template v-else>
                                        Sending to: <span class="text-foreground font-mono break-all">{{ account!.crypto_address }}</span>
                                    </template>
                                </p>
                                <button
                                    type="submit"
                                    :disabled="withdrawForm.processing"
                                    class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-bold py-3.5 rounded-2xl tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                                >
                                    {{ withdrawForm.processing ? 'Processing…' : 'Withdraw' }}
                                </button>
                            </form>
                        </div>
                    </template>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
