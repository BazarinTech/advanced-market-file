<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { PasswordRecoveryRequest } from '@/types/models';

const props = defineProps<{
    requests: PasswordRecoveryRequest[];
}>();

type Filter = 'all' | 'Pending' | 'Resolved';

const activeFilter = ref<Filter>('all');

const tabs: { key: Filter; label: string }[] = [
    { key: 'all', label: 'All' },
    { key: 'Pending', label: 'Pending' },
    { key: 'Resolved', label: 'Resolved' },
];

const filteredRequests = computed(() =>
    activeFilter.value === 'all'
        ? props.requests
        : props.requests.filter((r) => r.status === activeFilter.value),
);

function formatDate(value: string) {
    return new Date(value).toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).replace(',', ',');
}

function resolve(id: number) {
    router.post(route('admin.recovery-requests.resolve', id));
}
</script>

<template>
    <Head title="Password Recovery Requests - Admin" />

    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Password Recovery Requests ({{ requests.length }})</h1>

        <div class="flex gap-2 mb-4">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                class="px-4 py-1.5 rounded text-sm font-medium"
                :class="activeFilter === tab.key ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                @click="activeFilter = tab.key"
            >
                {{ tab.label }}
            </button>
        </div>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-orange-600 text-white">
                    <tr>
                        <th class="px-3 py-3">#</th>
                        <th class="px-3 py-3">Name</th>
                        <th class="px-3 py-3">Email</th>
                        <th class="px-3 py-3">Phone</th>
                        <th class="px-3 py-3">Network</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Submitted</th>
                        <th class="px-3 py-3">Resolved At</th>
                        <th class="px-3 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="(r, i) in filteredRequests" :key="r.id" class="hover:bg-gray-50">
                        <td class="px-3 py-2">{{ i + 1 }}</td>
                        <td class="px-3 py-2 font-medium">{{ r.name }}</td>
                        <td class="px-3 py-2">{{ r.email }}</td>
                        <td class="px-3 py-2">{{ r.phone }}</td>
                        <td class="px-3 py-2">{{ r.network }}</td>
                        <td class="px-3 py-2">
                            <span v-if="r.status === 'Pending'" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                            <span v-else class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Resolved</span>
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-500">{{ formatDate(r.created_at) }}</td>
                        <td class="px-3 py-2 text-xs text-gray-500">{{ r.resolved_at ? formatDate(r.resolved_at) : '—' }}</td>
                        <td class="px-3 py-2">
                            <button
                                v-if="r.status === 'Pending'"
                                class="px-2 py-1 rounded text-xs text-white bg-green-600 hover:bg-green-700"
                                @click="resolve(r.id)"
                            >
                                Mark Resolved
                            </button>
                            <span v-else class="text-xs text-gray-400">Done</span>
                        </td>
                    </tr>
                    <tr v-if="filteredRequests.length === 0">
                        <td colspan="9" class="px-4 py-6 text-center text-gray-400">No recovery requests yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
