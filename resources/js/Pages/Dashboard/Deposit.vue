<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import Icon from '@/components/Icon.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useCurrency } from '@/composables/useCurrency';

const props = defineProps<{
    cryptoAddress: string | null;
}>();

const page = usePage();
const earnings = page.props.auth.user!.earnings!;

const { money, toKes } = useCurrency();

const activeTab = ref<'mpesa' | 'crypto'>('mpesa');

const form = useForm({
    method: 'mpesa' as 'mpesa' | 'crypto',
    amount: '',
    phone: '',
});

const kesPreview = computed(() => {
    const amount = Number(form.amount);
    if (!amount || amount <= 0) return null;
    return toKes(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});

function submit() {
    form.method = activeTab.value;
    form.post(route('deposit.store'));
}

const copied = ref(false);
const addressInput = ref<HTMLInputElement | null>(null);

async function copyAddress() {
    if (!props.cryptoAddress) return;
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(props.cryptoAddress);
        } else {
            throw new Error('clipboard API unavailable');
        }
    } catch {
        const input = addressInput.value;
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
    <Head title="Deposit" />

    <AppLayout>
        <PageHeader title="Deposit" back-route="account" />

        <div class="w-full flex flex-col px-4 pb-10 mt-4 gap-4">
            <!-- Balance card -->
            <div class="rounded-2xl p-5 text-center bg-card border border-border">
                <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase mb-1">Available Balance</p>
                <p class="text-foreground text-2xl font-light tracking-wide">{{ money(earnings.balance) }}</p>
                <div class="w-10 h-px bg-primary mx-auto mt-3"></div>
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

                <!-- M-Pesa -->
                <TabsContent value="mpesa" class="w-full mt-4 flex flex-col gap-4">
                    <div class="rounded-2xl px-4 py-3 flex items-start gap-3 bg-card border border-border">
                        <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-xs">Enter amount (USD) and phone number</p>
                            <p class="text-muted-foreground text-xs">Click submit — you will receive an M-Pesa STK pop-up</p>
                            <p class="text-muted-foreground text-xs">Enter your PIN and balance updates automatically</p>
                        </div>
                    </div>

                    <div class="rounded-2xl p-5 bg-card border border-border">
                        <form class="flex flex-col gap-4" @submit.prevent="submit">
                            <div class="rounded-2xl px-4 py-3 bg-secondary border border-border">
                                <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Amount (USD)</label>
                                <input
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="1"
                                    placeholder="e.g. 10"
                                    class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                                    required
                                />
                                <p v-if="kesPreview" class="text-muted-foreground text-[10px] mt-1">You will be charged KES {{ kesPreview }}</p>
                            </div>
                            <div class="rounded-2xl px-4 py-3 bg-secondary border border-border">
                                <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Phone Number</label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="07xxxxxxxx"
                                    class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                                    required
                                />
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-bold py-3.5 rounded-2xl tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                            >
                                {{ form.processing ? 'Processing…' : 'Submit Deposit' }}
                            </button>
                        </form>
                    </div>
                </TabsContent>

                <!-- Crypto -->
                <TabsContent value="crypto" class="w-full mt-4 flex flex-col gap-4">
                    <div class="rounded-2xl px-4 py-3 flex items-start gap-3 bg-card border border-border">
                        <Icon name="circle-info" class="text-primary mt-0.5 text-sm shrink-0" />
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-xs">Send USDT (TRC20 network only) to the address below</p>
                            <p class="text-muted-foreground text-xs">Then submit the amount you sent</p>
                            <p class="text-muted-foreground text-xs">Your deposit will be reviewed and approved within ~30 minutes</p>
                        </div>
                    </div>

                    <div v-if="cryptoAddress" class="rounded-2xl p-5 bg-card border border-border">
                        <p class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase mb-2">USDT-TRC20 Address</p>
                        <div class="flex items-center gap-2">
                            <input
                                ref="addressInput"
                                type="text"
                                readonly
                                :value="cryptoAddress"
                                class="flex-1 rounded-2xl px-3 py-2.5 text-xs text-muted-foreground outline-none truncate tracking-wide font-mono bg-secondary border border-border"
                            />
                            <button
                                type="button"
                                class="bg-primary hover:bg-amber-700 text-primary-foreground text-xs font-bold px-4 py-2.5 rounded-2xl uppercase tracking-widest whitespace-nowrap transition-colors"
                                @click="copyAddress"
                            >
                                <template v-if="copied">✓ Copied!</template>
                                <template v-else><Icon name="copy" class="mr-1" /> Copy</template>
                            </button>
                        </div>
                    </div>
                    <div v-else class="rounded-2xl px-4 py-3 bg-card border border-border">
                        <p class="text-muted-foreground text-xs">Crypto deposits are not available right now. Please use M-Pesa or check back later.</p>
                    </div>

                    <div v-if="cryptoAddress" class="rounded-2xl p-5 bg-card border border-border">
                        <form class="flex flex-col gap-4" @submit.prevent="submit">
                            <div class="rounded-2xl px-4 py-3 bg-secondary border border-border">
                                <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Amount Sent (USD)</label>
                                <input
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="1"
                                    placeholder="e.g. 10"
                                    class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                                    required
                                />
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-bold py-3.5 rounded-2xl tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                            >
                                {{ form.processing ? 'Submitting…' : "I've Sent The Funds" }}
                            </button>
                        </form>
                    </div>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
