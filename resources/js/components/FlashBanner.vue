<script setup lang="ts">
import { useFlash } from '@/composables/useFlash';

withDefaults(defineProps<{ keys?: string[] }>(), {
    keys: () => ['success', 'error'],
});

const flash = useFlash();
</script>

<template>
    <template v-for="key in keys" :key="key">
        <div
            v-if="flash[key as keyof typeof flash]"
            class="w-full px-4 py-3 mb-2 rounded-lg border text-sm"
            :class="
                key === 'error'
                    ? 'text-destructive bg-destructive/10 border-destructive/20'
                    : 'text-success bg-success/10 border-success/20'
            "
        >
            {{ flash[key as keyof typeof flash] }}
        </div>
    </template>
</template>
