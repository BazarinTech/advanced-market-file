import { onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const SKIP_CLASSES = ['w-4', 'w-5', 'w-6'];

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

/**
 * Applies a shimmering skeleton placeholder to every <img> on the page while
 * it loads (opt out per-image with a `data-no-skeleton` attribute). Runs on
 * initial mount, after every Inertia navigation, and via a MutationObserver
 * so images inserted by client-side reactivity alone (no server round trip)
 * still get covered.
 */
export function useImageSkeletons() {
    const removeFinishListener = router.on('finish', () => {
        requestAnimationFrame(applyImageSkeletons);
    });

    let observer: MutationObserver | null = null;
    if (typeof MutationObserver !== 'undefined') {
        observer = new MutationObserver(() => {
            requestAnimationFrame(applyImageSkeletons);
        });
    }

    onMounted(() => {
        applyImageSkeletons();
        observer?.observe(document.body, { childList: true, subtree: true });
    });

    onUnmounted(() => {
        removeFinishListener();
        observer?.disconnect();
    });
}
