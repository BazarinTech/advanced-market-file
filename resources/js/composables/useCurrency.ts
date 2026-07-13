import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useCurrency() {
    const page = usePage();
    const rate = computed(() => Number(page.props.usdRate ?? 1) || 1);

    function toUsd(kes: string | number): number {
        return Number(kes) / rate.value;
    }

    function toKes(usdAmount: string | number): number {
        return Number(usdAmount) * rate.value;
    }

    function money(v: string | number): string {
        return '$' + toUsd(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function moneyRound(v: string | number): string {
        return '$' + Math.round(toUsd(v)).toLocaleString('en-US');
    }

    return { rate, toUsd, toKes, money, moneyRound };
}
