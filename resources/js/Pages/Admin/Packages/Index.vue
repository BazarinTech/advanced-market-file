<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Package } from '@/types/models';

defineProps<{
    packages: Package[];
}>();

function money(value: string) {
    return Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Package::imageUrl() is a plain model method (not an accessor/append), so it isn't
// serialized in the Inertia JSON payload — replicate its `asset('images/packages/...')`
// logic on the client instead.
function imageUrl(pkg: Package) {
    return `/images/packages/${pkg.image ?? ''}`;
}

// ── Add package form ──
const addForm = useForm<{
    name: string;
    amount: string;
    daily: string;
    days: string;
    image: File | null;
    active: boolean;
    tasks_per_day: string;
    task_category: string;
}>({
    name: '',
    amount: '',
    daily: '',
    days: '',
    image: null,
    active: true,
    tasks_per_day: '1',
    task_category: '',
});

function onAddImageChange(event: Event) {
    const target = event.target as HTMLInputElement;
    addForm.image = target.files?.[0] ?? null;
}

function submitAdd() {
    addForm.post(route('admin.packages.store'), {
        forceFormData: true,
        onSuccess: () => addForm.reset(),
    });
}

// ── Per-row inline edit ──
const editingId = ref<number | null>(null);

function toggleEdit(id: number) {
    editingId.value = editingId.value === id ? null : id;
}

function makeEditForm(pkg: Package) {
    return useForm<{
        name: string;
        amount: string;
        daily: string;
        days: number;
        image: File | null;
        active: boolean;
        tasks_per_day: number;
        task_category: string;
    }>({
        name: pkg.name,
        amount: pkg.amount,
        daily: pkg.daily,
        days: pkg.days,
        image: null,
        active: pkg.active,
        tasks_per_day: pkg.tasks_per_day,
        task_category: pkg.task_category ?? '',
    });
}

const editForms = ref<Record<number, ReturnType<typeof makeEditForm>>>({});

function editForm(pkg: Package) {
    if (!editForms.value[pkg.id]) {
        editForms.value[pkg.id] = makeEditForm(pkg);
    }
    return editForms.value[pkg.id];
}

function onEditImageChange(pkg: Package, event: Event) {
    const target = event.target as HTMLInputElement;
    editForm(pkg).image = target.files?.[0] ?? null;
}

function submitEdit(pkg: Package) {
    editForm(pkg).post(route('admin.packages.update', pkg.id), {
        forceFormData: true,
        onSuccess: () => {
            editingId.value = null;
        },
    });
}

function destroyPackage(pkg: Package) {
    if (!confirm('Delete this package?')) return;
    router.post(route('admin.packages.destroy', pkg.id));
}
</script>

<template>
    <Head title="Packages - Admin" />

    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Investment Packages</h1>

        <div class="bg-white rounded-xl shadow p-5 mb-6">
            <h3 class="text-blue-600 font-bold text-lg mb-4">Add New Package</h3>
            <form class="grid grid-cols-1 sm:grid-cols-2 gap-4" @submit.prevent="submitAdd">
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Package Name</label>
                    <input
                        v-model="addForm.name"
                        type="text"
                        placeholder="e.g. SFL 11"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                        required
                    >
                    <p v-if="addForm.errors.name" class="text-xs text-red-500 mt-1">{{ addForm.errors.name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Price (Kes)</label>
                    <input
                        v-model="addForm.amount"
                        type="number"
                        step="0.01"
                        placeholder="e.g. 500"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                        required
                    >
                    <p v-if="addForm.errors.amount" class="text-xs text-red-500 mt-1">{{ addForm.errors.amount }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Daily Income (Kes)</label>
                    <input
                        v-model="addForm.daily"
                        type="number"
                        step="0.01"
                        placeholder="e.g. 40"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                        required
                    >
                    <p v-if="addForm.errors.daily" class="text-xs text-red-500 mt-1">{{ addForm.errors.daily }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Cycle (days)</label>
                    <input
                        v-model="addForm.days"
                        type="number"
                        placeholder="e.g. 20"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                        required
                    >
                    <p v-if="addForm.errors.days" class="text-xs text-red-500 mt-1">{{ addForm.errors.days }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Tasks Per Day</label>
                    <input
                        v-model="addForm.tasks_per_day"
                        type="number"
                        min="1"
                        max="20"
                        placeholder="e.g. 3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                        required
                    >
                    <p class="text-xs text-gray-400 mt-1">Daily income is split evenly across this many tasks.</p>
                    <p v-if="addForm.errors.tasks_per_day" class="text-xs text-red-500 mt-1">{{ addForm.errors.tasks_per_day }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Task Category</label>
                    <input
                        v-model="addForm.task_category"
                        type="text"
                        list="task-category-suggestions"
                        placeholder="e.g. Financial services"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500"
                    >
                    <p class="text-xs text-gray-400 mt-1">The AI asks broad questions within this topic. Leave blank for general knowledge.</p>
                    <p v-if="addForm.errors.task_category" class="text-xs text-red-500 mt-1">{{ addForm.errors.task_category }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Thumbnail Image</label>
                    <input
                        type="file"
                        accept="image/*"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                        @change="onAddImageChange"
                    >
                    <p v-if="addForm.errors.image" class="text-xs text-red-500 mt-1">{{ addForm.errors.image }}</p>
                </div>
                <div class="flex items-end gap-4">
                    <label class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                        <input v-model="addForm.active" type="checkbox" class="w-4 h-4 accent-green-600">
                        Active
                    </label>
                    <button
                        type="submit"
                        :disabled="addForm.processing"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg disabled:opacity-60"
                    >
                        Add Package
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-green-600 text-white">
                    <tr>
                        <th class="px-3 py-3">Image</th>
                        <th class="px-3 py-3">Name</th>
                        <th class="px-3 py-3">Price</th>
                        <th class="px-3 py-3">Daily</th>
                        <th class="px-3 py-3">Days</th>
                        <th class="px-3 py-3">Total</th>
                        <th class="px-3 py-3">Tasks/Day</th>
                        <th class="px-3 py-3">Category</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template v-for="pkg in packages" :key="pkg.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <img :src="imageUrl(pkg)" class="w-14 h-14 rounded-lg object-cover" alt="">
                            </td>
                            <td class="px-3 py-2 font-semibold text-green-700">{{ pkg.name }}</td>
                            <td class="px-3 py-2">Kes {{ money(pkg.amount) }}</td>
                            <td class="px-3 py-2">Kes {{ money(pkg.daily) }}</td>
                            <td class="px-3 py-2">{{ pkg.days }} days</td>
                            <td class="px-3 py-2">Kes {{ money(String(Number(pkg.daily) * pkg.days)) }}</td>
                            <td class="px-3 py-2">{{ pkg.tasks_per_day }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ pkg.task_category || 'General' }}</td>
                            <td class="px-3 py-2">
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                    :class="pkg.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                                >
                                    {{ pkg.active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 flex gap-2 flex-wrap">
                                <button
                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg"
                                    @click="toggleEdit(pkg.id)"
                                >
                                    Edit
                                </button>
                                <button
                                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg"
                                    @click="destroyPackage(pkg)"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="editingId === pkg.id" class="bg-blue-50">
                            <td colspan="10" class="px-4 py-3">
                                <form class="grid grid-cols-2 sm:grid-cols-3 gap-3" @submit.prevent="submitEdit(pkg)">
                                    <div>
                                        <label class="text-xs text-gray-500">Name</label>
                                        <input
                                            v-model="editForm(pkg).name"
                                            type="text"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Price (Kes)</label>
                                        <input
                                            v-model="editForm(pkg).amount"
                                            type="number"
                                            step="0.01"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Daily (Kes)</label>
                                        <input
                                            v-model="editForm(pkg).daily"
                                            type="number"
                                            step="0.01"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Cycle (days)</label>
                                        <input
                                            v-model.number="editForm(pkg).days"
                                            type="number"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Tasks/Day</label>
                                        <input
                                            v-model.number="editForm(pkg).tasks_per_day"
                                            type="number"
                                            min="1"
                                            max="20"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Task Category</label>
                                        <input
                                            v-model="editForm(pkg).task_category"
                                            type="text"
                                            list="task-category-suggestions"
                                            placeholder="General knowledge"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm outline-none"
                                        >
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">New Image (optional)</label>
                                        <input
                                            type="file"
                                            accept="image/*"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"
                                            @change="onEditImageChange(pkg, $event)"
                                        >
                                    </div>
                                    <div class="flex items-end gap-3">
                                        <label class="flex items-center gap-1 text-sm text-gray-600">
                                            <input v-model="editForm(pkg).active" type="checkbox" class="accent-green-600">
                                            Active
                                        </label>
                                        <button
                                            type="submit"
                                            :disabled="editForm(pkg).processing"
                                            class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-1.5 rounded-lg disabled:opacity-60"
                                        >
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </template>
                    <tr v-if="packages.length === 0">
                        <td colspan="10" class="px-3 py-4 text-center text-gray-400">No packages found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <datalist id="task-category-suggestions">
            <option value="General knowledge" />
            <option value="Financial services" />
            <option value="Science and nature" />
            <option value="Technology" />
            <option value="Sports" />
            <option value="History" />
            <option value="Geography" />
            <option value="Health and wellness" />
            <option value="Arts and culture" />
        </datalist>
    </AdminLayout>
</template>
