import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

export function useChartTheme() {
    const { resolvedAppearance } = useAppearance();

    const isDark = computed(() => resolvedAppearance.value === 'dark');
    const foreColor = computed(() => (isDark.value ? '#FAF9F5' : '#1E1E1E'));
    const mutedColor = computed(() => (isDark.value ? '#B8B2A3' : '#6B6457'));
    const gridColor = computed(() => (isDark.value ? '#2A2A2A' : '#E7E2D8'));

    return { isDark, foreColor, mutedColor, gridColor };
}
