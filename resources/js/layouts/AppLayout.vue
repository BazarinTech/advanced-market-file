<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import BottomNav from '@/components/BottomNav.vue';
import FlashBanner from '@/components/FlashBanner.vue';

withDefaults(defineProps<{ showBottomNav?: boolean }>(), {
    showBottomNav: false,
});

const SKIP_CLASSES = ['object-contain', 'w-4', 'w-5', 'w-6'];

function applyImageSkeletons() {
    document.querySelectorAll<HTMLImageElement>('img').forEach((img) => {
        if (img.dataset.skeletonApplied) return;
        const cls = img.className || '';
        if (SKIP_CLASSES.some((s) => cls.includes(s))) return;
        if (img.dataset.noSkeleton !== undefined) return;

        img.dataset.skeletonApplied = 'true';
        const parent = img.parentElement;
        if (!parent) return;
        if (window.getComputedStyle(parent).position === 'static') {
            parent.style.position = 'relative';
        }

        const skeleton = document.createElement('div');
        skeleton.className = 'img-skeleton';
        parent.insertBefore(skeleton, img);
        img.classList.add('img-lazy');

        const reveal = () => {
            img.classList.add('loaded');
            skeleton.classList.add('fade-out');
            setTimeout(() => skeleton.remove(), 380);
        };

        if (img.complete && img.naturalWidth > 0) {
            reveal();
        } else {
            img.addEventListener('load', reveal, { once: true });
            img.addEventListener('error', reveal, { once: true });
        }
    });
}

const removeFinishListener = router.on('finish', () => {
    requestAnimationFrame(applyImageSkeletons);
});

onMounted(applyImageSkeletons);
onUnmounted(removeFinishListener);
</script>

<template>
    <div class="dark bg-background text-foreground flex flex-col items-center min-h-screen max-w-150 mx-auto">
        <FlashBanner />
        <slot />
        <BottomNav v-if="showBottomNav" />
    </div>
</template>

<style>
@keyframes skeletonShimmer {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
.img-skeleton {
    position: absolute;
    inset: 0;
    z-index: 2;
    background: linear-gradient(90deg, #0d1f35 25%, #162f47 50%, #0d1f35 75%);
    background-size: 200% 100%;
    animation: skeletonShimmer 1.4s ease-in-out infinite;
    transition: opacity 0.35s ease;
}
.img-skeleton.fade-out {
    opacity: 0;
    pointer-events: none;
}
.img-lazy {
    opacity: 0;
    transition: opacity 0.35s ease;
}
.img-lazy.loaded {
    opacity: 1;
}
</style>
