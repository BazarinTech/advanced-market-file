<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { PaginatedResponse } from '@/types/models';

defineProps<{ paginator: Pick<PaginatedResponse<unknown>, 'links'> }>();
</script>

<template>
    <nav v-if="paginator.links.length > 3" class="flex flex-wrap items-center gap-1 mt-4">
        <template v-for="(link, i) in paginator.links" :key="i">
            <span
                v-if="!link.url"
                class="px-3 py-1.5 text-sm rounded text-gray-400 border border-gray-200"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                class="px-3 py-1.5 text-sm rounded border"
                :class="
                    link.active
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'text-gray-700 border-gray-200 hover:bg-gray-50'
                "
                v-html="link.label"
            />
        </template>
    </nav>
</template>
