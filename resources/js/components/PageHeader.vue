<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Icon from '@/components/Icon.vue';

withDefaults(
    defineProps<{
        title: string;
        backRoute?: string;
    }>(),
    { backRoute: undefined },
);
</script>

<template>
    <header class="w-full h-14 bg-card border-b border-border flex items-center px-4 shrink-0 sticky top-0 z-40">
        <Link
            v-if="backRoute"
            :href="route(backRoute)"
            class="w-8 h-8 -ml-1 rounded-full flex items-center justify-center text-foreground hover:bg-accent hover:text-accent-foreground transition-colors"
        >
            <Icon name="arrow-left" class="text-base" />
        </Link>
        <h1
            class="flex-1 text-center text-sm font-semibold tracking-wide uppercase text-foreground"
            :class="backRoute ? 'pr-8' : ''"
        >
            {{ title }}
        </h1>
    </header>
</template>
