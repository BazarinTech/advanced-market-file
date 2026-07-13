<script setup lang="ts">
import { computed, ref } from 'vue';
import axios from 'axios';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Icon from '@/components/Icon.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import type { Order } from '@/types/models';

const props = defineProps<{
    orders: Order[];
    packageImages: Record<string, string | null>;
    claimImgSrc: string;
}>();

const page = usePage();

function moneyRound(v: string | number) {
    return Math.round(Number(v)).toLocaleString('en-US');
}

// Mirrors Order::canClaim() / Order::nextClaimAt() on the backend: earnings unlock
// daily at 9:00 AM, one day after the last claim (or immediately if never claimed).
function nextClaimAt(order: Order): Date | null {
    if (!order.last_claimed_at) return null;
    const d = new Date(order.last_claimed_at);
    d.setDate(d.getDate() + 1);
    d.setHours(9, 0, 0, 0);
    return d;
}

function orderCanClaim(order: Order): boolean {
    if (!order.last_claimed_at) return true;
    const next = nextClaimAt(order);
    return next !== null && new Date() >= next;
}

function formatDate(d: Date, withYear = false): string {
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: withYear ? 'numeric' : undefined });
}

const activeOrders = computed(() => props.orders.filter((o) => o.status === 'Active'));
const completedOrders = computed(() => props.orders.filter((o) => o.status !== 'Active'));

function packageImage(order: Order): string {
    const file = props.packageImages[order.package];
    return file ? `/images/packages/${file}` : '/images/8.jpeg';
}

function progress(order: Order): number {
    const totals = Number(order.totals);
    if (totals <= 0) return 0;
    return Math.min(100, Math.round((Number(order.earnings) / totals) * 100));
}

// ── Claim modal (per task, gated behind an AI-generated quiz question) ──
const modalOpen = ref(false);
const claimingOrder = ref<Order | null>(null);
const questionState = ref<'loading' | 'ready' | 'error'>('loading');
const question = ref('');
const questionError = ref('');

const answerForm = useForm({ answer: '' });

function fetchQuestion() {
    if (!claimingOrder.value) return;

    questionState.value = 'loading';
    questionError.value = '';
    question.value = '';
    answerForm.reset('answer');
    answerForm.clearErrors();

    axios
        .post(route('task.question', claimingOrder.value.ID))
        .then((res) => {
            question.value = res.data.question;
            questionState.value = 'ready';
        })
        .catch((err) => {
            questionError.value = err.response?.data?.message ?? 'Could not generate a question right now.';
            questionState.value = 'error';
        });
}

function openClaim(order: Order) {
    claimingOrder.value = order;
    modalOpen.value = true;
    fetchQuestion();
}

function submitAnswer() {
    if (!claimingOrder.value) return;

    answerForm.post(route('task.claim', claimingOrder.value.ID), {
        preserveScroll: true,
        onSuccess: () => {
            if (page.props.flash.error) {
                // Wrong answer, expired question, etc. — fetch a fresh question to retry.
                fetchQuestion();
            } else {
                modalOpen.value = false;
                claimingOrder.value = null;
            }
        },
    });
}
</script>

<template>
    <Head title="My Tasks" />

    <AppLayout show-bottom-nav>
        <!-- Top Bar -->
        <div class="w-full flex items-center justify-center h-14 bg-card border-b border-border">
            <p class="text-foreground text-xs font-light tracking-[0.3em] uppercase">My Tasks</p>
        </div>

        <div class="flex flex-col items-center w-full px-4 pb-32 mt-4">
            <!-- Info card -->
            <div class="w-full rounded-2xl p-5 flex flex-col items-center bg-card border border-border">
                <img :src="claimImgSrc" class="w-16 h-16 object-cover mx-auto mb-3 rounded-xl" alt="" />
                <p class="text-muted-foreground text-center text-xs tracking-wide">
                    Answer a quick question to claim each task's daily reward
                </p>
            </div>

            <!-- Tabs -->
            <Tabs default-value="active" class="w-full mt-4">
                <TabsList class="w-full h-auto rounded-2xl overflow-hidden bg-card border border-border p-0">
                    <TabsTrigger
                        value="active"
                        class="flex-1 py-2.5 text-xs tracking-widest uppercase font-medium rounded-none data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-none text-muted-foreground"
                    >
                        Active
                    </TabsTrigger>
                    <TabsTrigger
                        value="inactive"
                        class="flex-1 py-2.5 text-xs tracking-widest uppercase font-medium rounded-none data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-none text-muted-foreground"
                    >
                        Completed
                    </TabsTrigger>
                </TabsList>

                <!-- Active Tasks -->
                <TabsContent value="active" class="w-full">
                    <div v-if="activeOrders.length === 0" class="text-center mt-10">
                        <Icon name="layer-group" class="text-border text-4xl mb-3" />
                        <p class="text-muted-foreground tracking-widest uppercase text-xs">No active tasks</p>
                        <Link
                            :href="route('packages')"
                            class="inline-block mt-3 px-5 py-2 rounded-2xl bg-primary text-primary-foreground text-xs font-semibold tracking-widest uppercase no-underline"
                        >
                            Browse Tasks
                        </Link>
                    </div>
                    <div
                        v-for="order in activeOrders"
                        :key="order.ID"
                        class="w-full mt-3 rounded-2xl overflow-hidden bg-card border border-border"
                    >
                        <div class="flex gap-3 p-3">
                            <div class="relative w-24 h-20 shrink-0 rounded-xl overflow-hidden">
                                <img :src="packageImage(order)" class="w-full h-full object-cover" :alt="order.package" />
                                <div class="absolute inset-0" style="background: rgba(0, 0, 0, 0.3)"></div>
                            </div>
                            <div class="flex-1 space-y-1">
                                <p class="text-foreground font-semibold tracking-widest uppercase text-xs">{{ order.package }}</p>
                                <div class="grid grid-cols-2 gap-x-3 gap-y-0.5 text-[10px]">
                                    <span class="text-muted-foreground">Cycle: <span class="text-foreground">{{ order.cycle }}d</span></span>
                                    <span class="text-muted-foreground">Daily: <span class="text-primary">Kes {{ moneyRound(order.daily) }}</span></span>
                                    <span class="text-muted-foreground">Total: <span class="text-foreground">Kes {{ moneyRound(order.totals) }}</span></span>
                                    <span class="text-muted-foreground">Earned: <span class="text-success">Kes {{ moneyRound(order.earnings) }}</span></span>
                                </div>
                                <p v-if="!orderCanClaim(order)" class="text-muted-foreground text-[10px]">
                                    Available at 9:00 AM · {{ formatDate(nextClaimAt(order)!) }}
                                </p>
                            </div>
                        </div>
                        <!-- Progress bar -->
                        <div class="px-3 pb-3">
                            <div class="flex justify-between text-[9px] text-muted-foreground mb-1">
                                <span>Progress</span><span>{{ progress(order) }}%</span>
                            </div>
                            <div class="w-full h-1 rounded-full bg-border">
                                <div class="h-1 rounded-full bg-primary transition-all" :style="{ width: progress(order) + '%' }"></div>
                            </div>
                        </div>
                        <!-- Claim button -->
                        <div v-if="orderCanClaim(order)" class="px-3 pb-3">
                            <button
                                type="button"
                                class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-semibold py-2.5 rounded-xl tracking-widest uppercase text-xs flex items-center justify-center gap-2"
                                @click="openClaim(order)"
                            >
                                <Icon name="bolt" /> Claim Reward
                            </button>
                        </div>
                    </div>
                </TabsContent>

                <!-- Completed Tasks -->
                <TabsContent value="inactive" class="w-full">
                    <p v-if="completedOrders.length === 0" class="text-muted-foreground text-center mt-10 tracking-widest uppercase text-xs">
                        No completed tasks
                    </p>
                    <div
                        v-for="order in completedOrders"
                        :key="order.ID"
                        class="w-full mt-3 rounded-2xl overflow-hidden opacity-60 bg-card border border-border"
                    >
                        <div class="flex gap-3 p-3">
                            <div class="relative w-24 h-20 shrink-0 rounded-xl overflow-hidden">
                                <img :src="packageImage(order)" class="w-full h-full object-cover grayscale" :alt="order.package" />
                            </div>
                            <div class="flex-1 space-y-1">
                                <p class="text-foreground font-semibold tracking-widest uppercase text-xs">{{ order.package }}</p>
                                <div class="grid grid-cols-2 gap-x-3 gap-y-0.5 text-[10px]">
                                    <span class="text-muted-foreground">Cycle: <span class="text-foreground">{{ order.cycle }}d</span></span>
                                    <span class="text-muted-foreground">Daily: <span class="text-foreground">Kes {{ moneyRound(order.daily) }}</span></span>
                                    <span class="text-muted-foreground">Earned: <span class="text-foreground">Kes {{ moneyRound(order.earnings) }}</span></span>
                                </div>
                                <p class="text-success text-[10px] uppercase tracking-widest">● Cycle complete</p>
                            </div>
                        </div>
                    </div>
                </TabsContent>
            </Tabs>
        </div>

        <!-- Claim quiz modal -->
        <Dialog v-model:open="modalOpen">
            <DialogContent class="bg-card border-border">
                <DialogHeader>
                    <DialogTitle class="text-foreground text-xs tracking-[0.3em] uppercase text-center">
                        Claim {{ claimingOrder?.package }} Reward
                    </DialogTitle>
                </DialogHeader>

                <!-- Loading -->
                <div v-if="questionState === 'loading'" class="flex flex-col items-center gap-3 py-6">
                    <Icon name="bolt" class="text-primary text-2xl animate-pulse" />
                    <p class="text-muted-foreground text-xs tracking-wide">Preparing your question…</p>
                </div>

                <!-- Error fetching a question -->
                <div v-else-if="questionState === 'error'" class="flex flex-col items-center gap-3 py-4">
                    <Icon name="triangle-exclamation" class="text-destructive text-2xl" />
                    <p class="text-destructive text-xs text-center tracking-wide">{{ questionError }}</p>
                    <button
                        type="button"
                        class="px-5 py-2 rounded-xl bg-primary text-primary-foreground text-xs font-semibold tracking-widest uppercase"
                        @click="fetchQuestion"
                    >
                        Try Again
                    </button>
                </div>

                <!-- Question ready -->
                <form v-else class="flex flex-col gap-3" @submit.prevent="submitAnswer">
                    <div class="rounded-2xl px-4 py-3 bg-secondary border border-border">
                        <p class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase mb-1">Question</p>
                        <p class="text-foreground text-sm">{{ question }}</p>
                    </div>
                    <div class="rounded-2xl px-4 py-3 bg-card border border-border">
                        <label class="text-muted-foreground text-[9px] tracking-[0.3em] uppercase block mb-1">Your Answer</label>
                        <input
                            v-model="answerForm.answer"
                            type="text"
                            autocomplete="off"
                            placeholder="Type your answer…"
                            class="w-full bg-transparent outline-none text-sm text-foreground placeholder-muted-foreground/60"
                            required
                            autofocus
                        />
                    </div>
                    <button
                        type="submit"
                        :disabled="answerForm.processing"
                        class="w-full bg-primary hover:bg-amber-700 text-primary-foreground font-bold py-3 rounded-2xl tracking-widest uppercase text-xs mt-1 disabled:opacity-70"
                    >
                        {{ answerForm.processing ? 'Checking…' : 'Submit Answer' }}
                    </button>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
