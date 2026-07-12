import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import type { Flash } from '@/types/inertia';

const SUCCESS_KEYS: (keyof Flash)[] = [
    'success',
    'success_withdrawal',
    'success_banner',
    'success_links',
    'success_claim_image',
];

export function useFlashToasts() {
    const page = usePage();

    watch(
        () => page.props.flash,
        (flash) => {
            if (!flash) return;

            for (const key of SUCCESS_KEYS) {
                if (flash[key]) toast.success(flash[key] as string);
            }
            if (flash.error) toast.error(flash.error);
        },
        { immediate: true, deep: true },
    );

    watch(
        () => page.props.errors,
        (errors) => {
            if (errors && Object.keys(errors).length) {
                toast.error(Object.values(errors)[0] as string);
            }
        },
        { immediate: true, deep: true },
    );
}
