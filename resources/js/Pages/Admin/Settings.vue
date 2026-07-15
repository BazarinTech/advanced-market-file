<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Icon from '@/components/Icon.vue';

const props = defineProps<{
    withdrawal_min: number;
    withdrawal_fee: number;
    home_banner: string | null;
    claim_image: string | null;
    link_whatsapp: string | null;
    link_telegram: string | null;
    link_customer_support: string | null;
    link_download_app: string | null;
    logo: string | null;
    usd_kes_rate: number;
    crypto_deposit_address: string | null;
    referral_level1_pct: number;
    referral_level2_pct: number;
    referral_level3_pct: number;
}>();

// ── Platform logo ──
const logoForm = useForm<{ logo: File | null }>({
    logo: null,
});

function onLogoChange(event: Event) {
    const target = event.target as HTMLInputElement;
    logoForm.logo = target.files?.[0] ?? null;
}

function submitLogo() {
    logoForm.post(route('admin.settings.logo'), {
        forceFormData: true,
        onSuccess: () => logoForm.reset(),
    });
}

// ── Withdrawal settings ──
const withdrawalForm = useForm({
    withdrawal_min: props.withdrawal_min,
    withdrawal_fee: props.withdrawal_fee,
    usd_kes_rate: props.usd_kes_rate,
});

function submitWithdrawal() {
    withdrawalForm.post(route('admin.settings.update'));
}

// ── Crypto deposit address ──
const cryptoForm = useForm({
    crypto_deposit_address: props.crypto_deposit_address ?? '',
});

function submitCrypto() {
    cryptoForm.post(route('admin.settings.crypto'));
}

// ── Referral commission rates ──
const referralForm = useForm({
    referral_level1_pct: props.referral_level1_pct,
    referral_level2_pct: props.referral_level2_pct,
    referral_level3_pct: props.referral_level3_pct,
});

function submitReferral() {
    referralForm.post(route('admin.settings.referral'));
}

// ── Home page banner ──
const bannerForm = useForm<{ banner: File | null }>({
    banner: null,
});

function onBannerChange(event: Event) {
    const target = event.target as HTMLInputElement;
    bannerForm.banner = target.files?.[0] ?? null;
}

function submitBanner() {
    bannerForm.post(route('admin.settings.banner'), {
        forceFormData: true,
        onSuccess: () => bannerForm.reset(),
    });
}

// ── Claim image ──
const claimImageForm = useForm<{ claim_image: File | null }>({
    claim_image: null,
});

function onClaimImageChange(event: Event) {
    const target = event.target as HTMLInputElement;
    claimImageForm.claim_image = target.files?.[0] ?? null;
}

function submitClaimImage() {
    claimImageForm.post(route('admin.settings.claim-image'), {
        forceFormData: true,
        onSuccess: () => claimImageForm.reset(),
    });
}

// ── Links & social ──
const linksForm = useForm({
    link_whatsapp: props.link_whatsapp ?? '',
    link_telegram: props.link_telegram ?? '',
    link_customer_support: props.link_customer_support ?? '',
    link_download_app: props.link_download_app ?? '',
});

function submitLinks() {
    linksForm.post(route('admin.settings.links'));
}
</script>

<template>
    <Head title="Settings - Admin" />

    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Platform Settings</h1>

        <div class="flex flex-col gap-6 max-w-2xl">
            <!-- Section 0: Platform Logo -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                    <div class="w-1 h-5 bg-amber-500 rounded"></div>
                    <h2 class="text-base font-semibold text-gray-700">Platform Logo</h2>
                </div>

                <p class="text-xs text-gray-400 mb-4">
                    Shown in the top bar and on the login, register, and forgot-password pages. Falls back to the default icon mark if none is uploaded.
                </p>

                <div v-if="logo" class="mb-4 flex items-center gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-2 uppercase tracking-wide">Current Logo</p>
                        <img :src="logo" class="w-16 h-16 object-contain rounded-lg border border-gray-200 p-2" alt="Current Logo">
                    </div>
                </div>

                <form class="flex flex-col gap-4" @submit.prevent="submitLogo">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Upload New Logo</label>
                        <input
                            type="file"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-amber-500"
                            required
                            @change="onLogoChange"
                        >
                        <p class="text-xs text-gray-400 mt-1">Accepted: JPG, PNG, WEBP. Max size: 1 MB. Recommended: square image with transparent background.</p>
                        <p v-if="logoForm.errors.logo" class="text-xs text-red-500 mt-1">{{ logoForm.errors.logo }}</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="logoForm.processing"
                            class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-2 rounded-lg text-sm disabled:opacity-60"
                        >
                            Upload Logo
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 1: Withdrawal Settings -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                    <div class="w-1 h-5 bg-blue-600 rounded"></div>
                    <h2 class="text-base font-semibold text-gray-700">Withdrawal Settings</h2>
                </div>

                <form class="flex flex-col gap-5" @submit.prevent="submitWithdrawal">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Minimum Withdrawal Amount (Kes)</label>
                        <input
                            v-model.number="withdrawalForm.withdrawal_min"
                            type="number"
                            min="1"
                            step="1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                            required
                        >
                        <p class="text-xs text-gray-400 mt-1">Users cannot withdraw less than this amount.</p>
                        <p v-if="withdrawalForm.errors.withdrawal_min" class="text-xs text-red-500 mt-1">{{ withdrawalForm.errors.withdrawal_min }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Withdrawal Fee (%)</label>
                        <input
                            v-model.number="withdrawalForm.withdrawal_fee"
                            type="number"
                            min="0"
                            max="100"
                            step="0.1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                            required
                        >
                        <p class="text-xs text-gray-400 mt-1">Percentage deducted from every withdrawal. E.g. <strong>5</strong> = 5%.</p>
                        <p v-if="withdrawalForm.errors.withdrawal_fee" class="text-xs text-red-500 mt-1">{{ withdrawalForm.errors.withdrawal_fee }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">USD Exchange Rate (1 USD = ? KES)</label>
                        <input
                            v-model.number="withdrawalForm.usd_kes_rate"
                            type="number"
                            min="1"
                            step="0.01"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                            required
                        >
                        <p class="text-xs text-gray-400 mt-1">All user-facing balances/prices are shown in USD using this rate. The admin panel always shows raw KES.</p>
                        <p v-if="withdrawalForm.errors.usd_kes_rate" class="text-xs text-red-500 mt-1">{{ withdrawalForm.errors.usd_kes_rate }}</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="withdrawalForm.processing"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg text-sm disabled:opacity-60"
                        >
                            Save Withdrawal Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 1b: Crypto Deposit Address -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                    <div class="w-1 h-5 bg-teal-500 rounded"></div>
                    <h2 class="text-base font-semibold text-gray-700">Crypto Deposit Address (USDT-TRC20)</h2>
                </div>

                <form class="flex flex-col gap-4" @submit.prevent="submitCrypto">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Receiving Address</label>
                        <input
                            v-model="cryptoForm.crypto_deposit_address"
                            type="text"
                            placeholder="T..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:border-teal-500"
                            required
                        >
                        <p class="text-xs text-gray-400 mt-1">Shown to users on the Deposit page for USDT-TRC20 transfers. Must be a TRC20 network address.</p>
                        <p v-if="cryptoForm.errors.crypto_deposit_address" class="text-xs text-red-500 mt-1">{{ cryptoForm.errors.crypto_deposit_address }}</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="cryptoForm.processing"
                            class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-2 rounded-lg text-sm disabled:opacity-60"
                        >
                            Save Crypto Address
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 1c: Referral Commissions -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                    <div class="w-1 h-5 bg-rose-500 rounded"></div>
                    <h2 class="text-base font-semibold text-gray-700">Referral Commissions</h2>
                </div>

                <p class="text-xs text-gray-400 mb-4">
                    Percentage of each successful deposit paid to the depositor's sponsor (Level 1), their sponsor (Level 2), and their sponsor's sponsor (Level 3).
                </p>

                <form class="flex flex-col gap-5" @submit.prevent="submitReferral">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Level 1 (%)</label>
                        <input
                            v-model.number="referralForm.referral_level1_pct"
                            type="number"
                            min="0"
                            max="100"
                            step="0.1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-rose-500"
                            required
                        >
                        <p v-if="referralForm.errors.referral_level1_pct" class="text-xs text-red-500 mt-1">{{ referralForm.errors.referral_level1_pct }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Level 2 (%)</label>
                        <input
                            v-model.number="referralForm.referral_level2_pct"
                            type="number"
                            min="0"
                            max="100"
                            step="0.1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-rose-500"
                            required
                        >
                        <p v-if="referralForm.errors.referral_level2_pct" class="text-xs text-red-500 mt-1">{{ referralForm.errors.referral_level2_pct }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Level 3 (%)</label>
                        <input
                            v-model.number="referralForm.referral_level3_pct"
                            type="number"
                            min="0"
                            max="100"
                            step="0.1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-rose-500"
                            required
                        >
                        <p v-if="referralForm.errors.referral_level3_pct" class="text-xs text-red-500 mt-1">{{ referralForm.errors.referral_level3_pct }}</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="referralForm.processing"
                            class="bg-rose-600 hover:bg-rose-700 text-white font-semibold px-6 py-2 rounded-lg text-sm disabled:opacity-60"
                        >
                            Save Referral Rates
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 2: Home Page Banner -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                    <div class="w-1 h-5 bg-emerald-500 rounded"></div>
                    <h2 class="text-base font-semibold text-gray-700">Home Page Banner</h2>
                </div>

                <div v-if="home_banner" class="mb-4">
                    <p class="text-xs text-gray-400 mb-2 uppercase tracking-wide">Current Banner</p>
                    <img :src="home_banner" class="w-full h-36 object-cover rounded-lg border border-gray-200" alt="Current Banner">
                </div>

                <form class="flex flex-col gap-4" @submit.prevent="submitBanner">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Upload New Banner Image</label>
                        <input
                            type="file"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-emerald-500"
                            required
                            @change="onBannerChange"
                        >
                        <p class="text-xs text-gray-400 mt-1">Accepted: JPG, PNG, WEBP. Max size: 3 MB. Recommended width: 600px+.</p>
                        <p v-if="bannerForm.errors.banner" class="text-xs text-red-500 mt-1">{{ bannerForm.errors.banner }}</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="bannerForm.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-lg text-sm disabled:opacity-60"
                        >
                            Upload Banner
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 3: Claim Image -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                    <div class="w-1 h-5 bg-purple-500 rounded"></div>
                    <h2 class="text-base font-semibold text-gray-700">Orders Page — Claim Image</h2>
                </div>

                <div v-if="claim_image" class="mb-4 flex items-center gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-2 uppercase tracking-wide">Current Image</p>
                        <img :src="claim_image" class="w-20 h-20 object-cover rounded-lg border border-gray-200" alt="Claim Image">
                    </div>
                </div>

                <form class="flex flex-col gap-4" @submit.prevent="submitClaimImage">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Upload New Claim Image</label>
                        <input
                            type="file"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-purple-500"
                            required
                            @change="onClaimImageChange"
                        >
                        <p class="text-xs text-gray-400 mt-1">Accepted: JPG, PNG, WEBP. Max size: 2 MB. Recommended: square image (e.g. 200×200).</p>
                        <p v-if="claimImageForm.errors.claim_image" class="text-xs text-red-500 mt-1">{{ claimImageForm.errors.claim_image }}</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="claimImageForm.processing"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded-lg text-sm disabled:opacity-60"
                        >
                            Upload Image
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 4: Links & Social -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                    <div class="w-1 h-5 bg-indigo-500 rounded"></div>
                    <h2 class="text-base font-semibold text-gray-700">Links &amp; Social</h2>
                </div>

                <form class="flex flex-col gap-5" @submit.prevent="submitLinks">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            <Icon name="whatsapp" class="text-green-500 mr-1" /> WhatsApp Group URL
                        </label>
                        <input
                            v-model="linksForm.link_whatsapp"
                            type="url"
                            placeholder="https://chat.whatsapp.com/..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                        >
                        <p class="text-xs text-gray-400 mt-1">Leave blank to hide from users.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            <Icon name="telegram" class="text-blue-500 mr-1" /> Telegram Group URL
                        </label>
                        <input
                            v-model="linksForm.link_telegram"
                            type="url"
                            placeholder="https://t.me/..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                        >
                        <p class="text-xs text-gray-400 mt-1">Leave blank to hide from users.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            <Icon name="headset" class="text-cyan-500 mr-1" /> Customer Support URL
                        </label>
                        <input
                            v-model="linksForm.link_customer_support"
                            type="url"
                            placeholder="https://wa.me/254..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                        >
                        <p class="text-xs text-gray-400 mt-1">Link to WhatsApp chat, Telegram DM, or a support page.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            <Icon name="download" class="text-amber-500 mr-1" /> Download App URL
                        </label>
                        <input
                            v-model="linksForm.link_download_app"
                            type="url"
                            placeholder="https://..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                        >
                        <p class="text-xs text-gray-400 mt-1">APK download link or Play Store / App Store URL.</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="linksForm.processing"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg text-sm disabled:opacity-60"
                        >
                            Save Links
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
