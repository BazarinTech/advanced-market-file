<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Icon from '@/components/Icon.vue';

const props = withDefaults(defineProps<{ size?: 'sm' | 'lg' }>(), { size: 'lg' });

const page = usePage();
const logo = computed(() => page.props.platformLogo);

const boxClass = computed(() => (props.size === 'sm' ? 'w-7 h-7 rounded-lg' : 'w-14 h-14 rounded-2xl'));
const iconClass = computed(() => (props.size === 'sm' ? 'text-primary-foreground text-xs' : 'text-primary-foreground text-2xl'));
</script>

<template>
    <img
        v-if="logo"
        :src="logo"
        alt="Logo"
        class="object-contain shrink-0"
        :class="boxClass"
    >
    <div v-else class="bg-primary flex items-center justify-center shrink-0" :class="boxClass">
        <Icon name="box" :class="iconClass" />
    </div>
</template>
