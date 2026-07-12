<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Icon from '@/components/Icon.vue';

const tabs = [
    { route: 'home', icon: 'table-list', label: 'Home' },
    { route: 'packages', icon: 'box', label: 'Tasks' },
    { route: 'task', icon: 'briefcase', label: 'My Tasks' },
    { route: 'account', icon: 'user', label: 'Account' },
] as const;
</script>

<template>
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-150 px-4 pb-4 z-50 pointer-events-none">
        <div class="pointer-events-auto flex bg-card rounded-2xl border border-border shadow-lg shadow-stone-200/60 overflow-hidden">
            <Link
                v-for="tab in tabs"
                :key="tab.route"
                :href="route(tab.route)"
                class="flex flex-col items-center justify-center gap-1 flex-1 py-3 no-underline transition-colors"
                :class="route().current(tab.route) ? 'text-primary' : 'text-muted-foreground hover:text-primary'"
            >
                <span
                    class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
                    :class="route().current(tab.route) ? 'bg-accent' : ''"
                >
                    <Icon :name="tab.icon" class="text-base" />
                </span>
                <span class="text-[10px] tracking-widest uppercase font-medium">{{ tab.label }}</span>
            </Link>
        </div>
    </nav>
</template>
