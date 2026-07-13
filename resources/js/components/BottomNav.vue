<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { HugeiconsIcon } from '@hugeicons/vue';
import { Home04Icon, Task01Icon, BriefcaseDollarIcon, UserCircleIcon } from '@hugeicons/core-free-icons';

const tabs = [
    { route: 'home', icon: Home04Icon, label: 'Home' },
    { route: 'packages', icon: Task01Icon, label: 'Tasks' },
    { route: 'task', icon: BriefcaseDollarIcon, label: 'My Tasks' },
    { route: 'account', icon: UserCircleIcon, label: 'Account' },
] as const;
</script>

<template>
    <nav
        class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-150 bg-card border-t border-border z-50 pb-[env(safe-area-inset-bottom)]"
    >
        <div class="flex">
            <Link
                v-for="tab in tabs"
                :key="tab.route"
                :href="route(tab.route)"
                class="relative flex flex-col items-center justify-center gap-1 flex-1 pt-2.5 pb-2 no-underline transition-colors"
                :class="route().current(tab.route) ? 'text-primary' : 'text-muted-foreground hover:text-primary'"
            >
                <span
                    class="absolute top-0 left-1/2 -translate-x-1/2 h-0.5 w-10 rounded-full transition-colors"
                    :class="route().current(tab.route) ? 'bg-primary' : 'bg-transparent'"
                ></span>
                <HugeiconsIcon
                    :icon="tab.icon"
                    :size="22"
                    :stroke-width="route().current(tab.route) ? 2 : 1.5"
                />
                <span
                    class="text-[10px] tracking-wide"
                    :class="route().current(tab.route) ? 'font-semibold' : 'font-medium'"
                >
                    {{ tab.label }}
                </span>
            </Link>
        </div>
    </nav>
</template>
