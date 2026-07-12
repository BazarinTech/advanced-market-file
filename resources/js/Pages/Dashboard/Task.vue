<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Icon from '@/components/Icon.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import type { Order } from '@/types/models';

const props = defineProps<{
    orders: Order[];
    canClaim: boolean;
    packageImages: Record<string, string | null>;
    claimImgSrc: string;
}>();

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

const nextClaimLabel = computed(() => {
    const candidates = activeOrders.value.filter((o) => o.last_claimed_at);
    if (candidates.length === 0) return '9:00 AM tomorrow';
    const latest = candidates.reduce((a, b) => (new Date(a.last_claimed_at!) > new Date(b.last_claimed_at!) ? a : b));
    const next = nextClaimAt(latest);
    return next ? `9:00 AM · ${formatDate(next, true)}` : '9:00 AM tomorrow';
});

function packageImage(order: Order): string {
    const file = props.packageImages[order.package];
    return file ? `/images/packages/${file}` : '/images/8.jpeg';
}

function progress(order: Order): number {
    const totals = Number(order.totals);
    if (totals <= 0) return 0;
    return Math.min(100, Math.round((Number(order.earnings) / totals) * 100));
}

const claimForm = useForm({});

function claim() {
    claimForm.post(route('task.claim'), { preserveScroll: true });
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
            <!-- Claim card -->
            <form class="w-full rounded-2xl p-5 flex flex-col items-center bg-card border border-border" @submit.prevent="claim">
                <img :src="claimImgSrc" class="w-16 h-16 object-cover mx-auto mb-3 rounded-xl" alt="" />
                <p class="text-muted-foreground text-center text-xs tracking-wide">Collect your daily rewards from active tasks</p>
                <button
                    v-if="canClaim"
                    type="submit"
                    :disabled="claimForm.processing"
                    class="mt-4 w-[75%] bg-primary hover:bg-amber-700 text-primary-foreground font-semibold py-2.5 rounded-2xl tracking-widest uppercase text-xs flex items-center justify-center gap-2 disabled:opacity-70"
                >
                    <Icon name="bolt" /> {{ claimForm.processing ? 'Claiming…' : 'Claim Reward' }}
                </button>
                <div v-else class="mt-4 text-center">
                    <p class="text-muted-foreground text-[10px] tracking-widest uppercase mb-1">Next claim available</p>
                    <p class="text-primary text-sm font-medium">{{ nextClaimLabel }}</p>
                </div>
            </form>

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
                                <p v-if="orderCanClaim(order)" class="text-primary text-[10px] uppercase tracking-widest font-medium">
                                    ● Ready to claim
                                </p>
                                <p v-else class="text-muted-foreground text-[10px]">
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
    </AppLayout>
</template>
