<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import Autoplay from 'embla-carousel-autoplay';
import AppLayout from '@/layouts/AppLayout.vue';
import Icon from '@/components/Icon.vue';
import AppLogo from '@/components/AppLogo.vue';
import { Carousel, CarouselContent, CarouselItem } from '@/components/ui/carousel';
import type { CarouselApi } from '@/components/ui/carousel';
import { useCurrency } from '@/composables/useCurrency';

defineProps<{
    downline: number;
    numActive: number;
}>();

const page = usePage();
const user = page.props.auth.user!;
const earnings = user.earnings!;

const { money, moneyRound } = useCurrency();

const slideCount = 3;
const currentSlide = ref(0);

function onInitApi(api: CarouselApi) {
    if (!api) return;
    api.on('select', () => {
        currentSlide.value = api.selectedScrollSnap();
    });
}

const autoplay = Autoplay({ delay: 4500, stopOnInteraction: false });
</script>

<template>
    <Head title="Home" />

    <AppLayout show-bottom-nav>
        <!-- Top Bar -->
        <div class="w-full flex items-center justify-between h-14 px-4 bg-card border-b border-border">
            <div class="flex items-center gap-2">
                <AppLogo size="sm" />
                <span class="text-foreground font-semibold tracking-widest text-sm uppercase">Mythos Task</span>
            </div>
            <div class="text-right">
                <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Welcome back</p>
                <p class="text-primary text-xs tracking-wide">{{ user.phone }}</p>
            </div>
        </div>

        <!-- Wallet / Earnings / Team carousel -->
        <div class="w-full px-4 mt-4">
            <Carousel :opts="{ loop: true }" :plugins="[autoplay]" class="w-full" @init-api="onInitApi">
                <CarouselContent>
                    <!-- Slide 1: Wallet balance -->
                    <CarouselItem>
                        <div class="w-full h-40 rounded-2xl p-5 relative overflow-hidden bg-primary">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-10 bg-white"
                                style="transform: translate(30%, -30%)"
                            ></div>
                            <p class="text-primary-foreground/70 text-[10px] tracking-[0.3em] uppercase">Wallet Balance</p>
                            <p class="text-primary-foreground text-3xl font-light mt-1 tracking-wide">
                                <span class="font-semibold">{{ money(earnings.balance) }}</span>
                            </p>
                            <div class="w-12 h-px bg-primary-foreground/40 mt-3 mb-3"></div>
                            <div class="flex gap-6">
                                <div>
                                    <p class="text-primary-foreground/70 text-[10px] tracking-widest uppercase">Earned</p>
                                    <p class="text-primary-foreground text-sm font-medium">
                                        {{ money(Number(earnings.referral) + Number(earnings.totals)) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-primary-foreground/70 text-[10px] tracking-widest uppercase">Deposited</p>
                                    <p class="text-primary-foreground text-sm">{{ money(earnings.deposit) }}</p>
                                </div>
                                <div>
                                    <p class="text-primary-foreground/70 text-[10px] tracking-widest uppercase">Team</p>
                                    <p class="text-primary-foreground text-sm">{{ downline }}</p>
                                </div>
                            </div>
                        </div>
                    </CarouselItem>

                    <!-- Slide 2: Lifetime earnings -->
                    <CarouselItem>
                        <div class="w-full h-40 rounded-2xl p-5 relative overflow-hidden bg-zinc-900">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-10 bg-primary"
                                style="transform: translate(30%, -30%)"
                            ></div>
                            <p class="text-white/60 text-[10px] tracking-[0.3em] uppercase">Total Rewards</p>
                            <p class="text-white text-3xl font-light mt-1 tracking-wide">
                                <span class="font-semibold">{{ money(earnings.totals) }}</span>
                            </p>
                            <div class="w-12 h-px bg-white/30 mt-3 mb-3"></div>
                            <div class="flex gap-6">
                                <div>
                                    <p class="text-white/60 text-[10px] tracking-widest uppercase">Withdrawn</p>
                                    <p class="text-white text-sm font-medium">{{ money(earnings.withdraw) }}</p>
                                </div>
                                <div>
                                    <p class="text-white/60 text-[10px] tracking-widest uppercase">Referral</p>
                                    <p class="text-white text-sm">{{ money(earnings.referral) }}</p>
                                </div>
                                <div>
                                    <p class="text-white/60 text-[10px] tracking-widest uppercase">Bonus</p>
                                    <p class="text-white text-sm">{{ money(earnings.bonus) }}</p>
                                </div>
                            </div>
                        </div>
                    </CarouselItem>

                    <!-- Slide 3: Team -->
                    <CarouselItem>
                        <div class="w-full h-40 rounded-2xl p-5 relative overflow-hidden bg-card border border-border">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 rounded-full bg-accent"
                                style="transform: translate(30%, -30%)"
                            ></div>
                            <p class="text-muted-foreground text-[10px] tracking-[0.3em] uppercase">My Team</p>
                            <p class="text-foreground text-3xl font-light mt-1 tracking-wide">
                                <span class="font-semibold">{{ downline }}</span> member{{ downline === 1 ? '' : 's' }}
                            </p>
                            <div class="w-12 h-px bg-border mt-3 mb-3"></div>
                            <div class="flex items-end justify-between">
                                <div class="flex gap-6">
                                    <div>
                                        <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Active</p>
                                        <p class="text-success text-sm font-medium">{{ numActive }}</p>
                                    </div>
                                    <div>
                                        <p class="text-muted-foreground text-[10px] tracking-widest uppercase">Referral Bonus</p>
                                        <p class="text-foreground text-sm">{{ money(earnings.referral) }}</p>
                                    </div>
                                </div>
                                <Link
                                    :href="route('team')"
                                    class="text-primary text-[11px] font-semibold tracking-widest uppercase no-underline"
                                >
                                    Invite &rarr;
                                </Link>
                            </div>
                        </div>
                    </CarouselItem>
                </CarouselContent>
            </Carousel>

            <!-- Dots -->
            <div class="flex justify-center gap-1.5 mt-3">
                <span
                    v-for="i in slideCount"
                    :key="i"
                    class="h-1.5 rounded-full transition-all duration-300"
                    :class="currentSlide === i - 1 ? 'w-5 bg-primary' : 'w-1.5 bg-border'"
                ></span>
            </div>
        </div>

        <!-- Task mission banner -->
        <div class="w-full px-4 mt-4">
            <div class="w-full rounded-2xl p-5 bg-card border border-border flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-accent flex items-center justify-center shrink-0">
                    <Icon name="box" class="text-accent-foreground text-xl" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-foreground text-sm font-semibold">Ready for your next task?</p>
                    <p class="text-muted-foreground text-xs mt-0.5">Start a task and earn daily rewards.</p>
                </div>
                <Link
                    :href="route('packages')"
                    class="shrink-0 bg-primary hover:bg-amber-700 text-primary-foreground text-xs font-bold px-4 py-2.5 rounded-xl tracking-wide uppercase no-underline"
                >
                    Browse
                </Link>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="w-full px-4 mt-4 grid grid-cols-3 gap-3">
            <div class="rounded-2xl p-3 text-center bg-card border border-border">
                <Icon name="arrow-trend-up" class="text-primary mb-1" />
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Withdrawn</p>
                <p class="text-foreground text-xs font-medium mt-0.5">{{ moneyRound(earnings.withdraw) }}</p>
            </div>
            <div class="rounded-2xl p-3 text-center bg-card border border-border">
                <Icon name="users" class="text-primary mb-1" />
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Active Team</p>
                <p class="text-foreground text-xs font-medium mt-0.5">{{ numActive }}</p>
            </div>
            <div class="rounded-2xl p-3 text-center bg-card border border-border">
                <Icon name="coins" class="text-primary mb-1" />
                <p class="text-muted-foreground text-[9px] tracking-widest uppercase">Referral</p>
                <p class="text-foreground text-xs font-medium mt-0.5">{{ moneyRound(earnings.referral) }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="w-full px-4 mt-4 grid grid-cols-2 gap-3 pb-32">
            <Link :href="route('deposit')" class="flex items-center gap-3 rounded-2xl p-4 no-underline bg-card border border-border">
                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center">
                    <Icon name="plus" class="text-primary" />
                </div>
                <div>
                    <p class="text-foreground text-xs font-medium">Deposit</p>
                    <p class="text-muted-foreground text-[10px]">Add funds</p>
                </div>
            </Link>
            <Link :href="route('withdraw')" class="flex items-center gap-3 rounded-2xl p-4 no-underline bg-card border border-border">
                <div class="w-9 h-9 rounded-xl bg-success/10 flex items-center justify-center">
                    <Icon name="arrow-up-from-bracket" class="text-success" />
                </div>
                <div>
                    <p class="text-foreground text-xs font-medium">Withdraw</p>
                    <p class="text-muted-foreground text-[10px]">Cash out</p>
                </div>
            </Link>
            <Link :href="route('packages')" class="flex items-center gap-3 rounded-2xl p-4 no-underline bg-card border border-border">
                <div class="w-9 h-9 rounded-xl bg-info/10 flex items-center justify-center">
                    <Icon name="layer-group" class="text-info" />
                </div>
                <div>
                    <p class="text-foreground text-xs font-medium">Tasks</p>
                    <p class="text-muted-foreground text-[10px]">Browse &amp; start</p>
                </div>
            </Link>
            <Link :href="route('task')" class="flex items-center gap-3 rounded-2xl p-4 no-underline bg-card border border-border">
                <div class="w-9 h-9 rounded-xl bg-warning/10 flex items-center justify-center">
                    <Icon name="briefcase" class="text-warning" />
                </div>
                <div>
                    <p class="text-foreground text-xs font-medium">My Tasks</p>
                    <p class="text-muted-foreground text-[10px]">Claim rewards</p>
                </div>
            </Link>
        </div>
    </AppLayout>
</template>
