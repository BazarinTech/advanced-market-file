<script setup lang="ts">
import BottomNav from '@/components/BottomNav.vue';
import { useFlashToasts } from '@/composables/useFlashToasts';
import { useImageSkeletons } from '@/composables/useImageSkeletons';

withDefaults(defineProps<{ showBottomNav?: boolean }>(), {
    showBottomNav: false,
});

useFlashToasts();
useImageSkeletons();
</script>

<template>
    <div class="theme-app bg-background text-foreground flex flex-col items-center min-h-screen max-w-150 mx-auto">
        <slot />
        <BottomNav v-if="showBottomNav" />
    </div>
</template>
