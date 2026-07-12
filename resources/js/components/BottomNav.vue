<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Icon from '@/components/Icon.vue';

const tabs = [
    { route: 'home', icon: 'chart-line', label: 'Home' },
    { route: 'packages', icon: 'layer-group', label: 'Plans' },
    { route: 'task', icon: 'briefcase', label: 'Orders' },
    { route: 'account', icon: 'user', label: 'Account' },
] as const;
</script>

<template>
    <nav
        class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-150 bg-secondary border-t border-border flex z-50"
    >
        <Link
            v-for="(tab, i) in tabs"
            :key="tab.route"
            :href="route(tab.route)"
            class="flex flex-col items-center justify-center flex-1 py-3 no-underline"
            :class="[
                i < tabs.length - 1 ? 'border-r border-border' : '',
                route().current(tab.route) ? 'text-primary' : 'text-muted-foreground hover:text-primary',
            ]"
        >
            <Icon :name="tab.icon" class="text-base" />
            <span class="text-[10px] mt-1 tracking-widest uppercase">{{ tab.label }}</span>
        </Link>
    </nav>
</template>
