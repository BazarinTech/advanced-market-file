<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import type { PaginatedResponse, User } from '@/types/models';

const props = defineProps<{
    users: PaginatedResponse<User>;
    uplineEmails: Record<number, string>;
    search: string | null;
}>();

const searchValue = ref(props.search ?? '');

function submitSearch() {
    router.get(route('admin.users'), { search: searchValue.value }, { preserveState: true });
}

function clearSearch() {
    searchValue.value = '';
    router.get(route('admin.users'));
}

function toggleStatus(u: User) {
    router.post(route('admin.users.status', u.ID), {
        status: u.status === 'Active' ? 'Inactive' : 'Active',
    });
}

function makeAdmin(u: User) {
    router.post(route('admin.users.make-admin', u.ID));
}

const resetOpen = ref(false);
const resetUserId = ref<number | null>(null);
const resetUserEmail = ref('');
const resetForm = useForm({
    password: '',
    password_confirmation: '',
});

function openReset(u: User) {
    resetUserId.value = u.ID;
    resetUserEmail.value = u.email;
    resetForm.reset();
    resetOpen.value = true;
}

function submitReset() {
    if (resetUserId.value === null) return;
    resetForm.post(route('admin.users.reset-password', resetUserId.value), {
        onSuccess: () => {
            resetOpen.value = false;
        },
    });
}
</script>

<template>
    <Head title="Users - Admin" />

    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Users ({{ users.total }})</h1>

        <form class="mb-4 flex gap-2" @submit.prevent="submitSearch">
            <input
                v-model="searchValue"
                type="text"
                placeholder="Search by email, phone or ID..."
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-green-500"
            >
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">Search</button>
            <button v-if="search" type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm" @click="clearSearch">
                Clear
            </button>
        </form>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-green-600 text-white">
                    <tr>
                        <th class="px-3 py-3">#</th>
                        <th class="px-3 py-3">ID</th>
                        <th class="px-3 py-3">Email</th>
                        <th class="px-3 py-3">Phone</th>
                        <th class="px-3 py-3">Country</th>
                        <th class="px-3 py-3">Upline</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Role</th>
                        <th class="px-3 py-3">Date</th>
                        <th class="px-3 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="(u, i) in users.data" :key="u.ID" class="hover:bg-gray-50">
                        <td class="px-3 py-2">{{ (users.from ?? 1) + i }}</td>
                        <td class="px-3 py-2">{{ u.ID }}</td>
                        <td class="px-3 py-2">{{ u.email }}</td>
                        <td class="px-3 py-2">{{ u.phone }}</td>
                        <td class="px-3 py-2">{{ u.country }}</td>
                        <td class="px-3 py-2 text-xs text-gray-500">
                            {{ uplineEmails[u.refer] ?? (u.refer ? '#' + u.refer : '—') }}
                        </td>
                        <td class="px-3 py-2 font-semibold" :class="u.status === 'Active' ? 'text-green-600' : 'text-red-500'">{{ u.status }}</td>
                        <td class="px-3 py-2">{{ u.role }}</td>
                        <td class="px-3 py-2 text-xs text-gray-500">{{ u.date }}</td>
                        <td class="px-3 py-2 flex flex-wrap gap-1">
                            <button
                                class="px-2 py-1 rounded text-xs text-white"
                                :class="u.status === 'Active' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-600 hover:bg-green-700'"
                                @click="toggleStatus(u)"
                            >
                                {{ u.status === 'Active' ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button
                                v-if="u.role !== 'admin'"
                                class="px-2 py-1 rounded text-xs text-white bg-blue-600 hover:bg-blue-700"
                                @click="makeAdmin(u)"
                            >
                                Make Admin
                            </button>
                            <button
                                class="px-2 py-1 rounded text-xs text-white bg-orange-500 hover:bg-orange-600"
                                @click="openReset(u)"
                            >
                                Reset Password
                            </button>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="10" class="px-3 py-4 text-center text-gray-400">No users found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :paginator="users" />

        <Dialog v-model:open="resetOpen">
            <DialogContent class="sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle>Reset Password</DialogTitle>
                </DialogHeader>
                <p class="text-xs text-gray-400 -mt-2">{{ resetUserEmail }}</p>
                <form class="flex flex-col gap-4" @submit.prevent="submitReset">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1 uppercase tracking-wide">New Password</label>
                        <input
                            v-model="resetForm.password"
                            type="password"
                            minlength="6"
                            placeholder="Min. 6 characters"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-orange-500"
                            required
                        >
                        <p v-if="resetForm.errors.password" class="text-xs text-red-500 mt-1">{{ resetForm.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1 uppercase tracking-wide">Confirm Password</label>
                        <input
                            v-model="resetForm.password_confirmation"
                            type="password"
                            placeholder="Repeat new password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-orange-500"
                            required
                        >
                    </div>
                    <button
                        type="submit"
                        :disabled="resetForm.processing"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg text-sm mt-1 disabled:opacity-60"
                    >
                        Reset Password
                    </button>
                </form>
            </DialogContent>
        </Dialog>
    </AdminLayout>
</template>
