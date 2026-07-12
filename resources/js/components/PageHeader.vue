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
    <header class="w-full h-14 bg-secondary border-b border-border flex items-center px-4 relative shrink-0">
        <Link v-if="backRoute" :href="route(backRoute)" class="text-foreground">
            <Icon name="arrow-left" class="text-lg" />
        </Link>
        <h1
            class="flex-1 text-center text-sm font-semibold tracking-wide uppercase"
            :class="backRoute ? 'pr-8' : ''"
        >
            {{ title }}
        </h1>
    </header>
</template>
