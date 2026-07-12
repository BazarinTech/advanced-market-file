import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { Flash } from '@/types/inertia';

export function useFlash() {
    const page = usePage();

    return computed<Flash>(() => page.props.flash);
}
