<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        name: string;
        class?: string;
    }>(),
    { class: '' },
);

const icons = import.meta.glob<string>('/public/icons/hugeicons/*.svg', {
    query: '?raw',
    import: 'default',
    eager: true,
});

const markup = computed(() => {
    const raw = icons[`/public/icons/hugeicons/${props.name}.svg`];
    if (!raw) {
        return `<!-- icon not found: ${props.name} -->`;
    }

    const cls = ['inline-block', props.class].filter(Boolean).join(' ');

    return raw
        .replace(/\s*width="[^"]*"/, '')
        .replace(/\s*height="[^"]*"/, '')
        .replace(/stroke="#[0-9A-Fa-f]{3,8}"/g, 'stroke="currentColor"')
        .replace(/fill="#[0-9A-Fa-f]{3,8}"/g, 'fill="none"')
        .replace('<svg', `<svg width="1em" height="1em" class="${cls}"`);
});
</script>

<template>
    <span v-html="markup" />
</template>
