import { onMounted, ref } from 'vue';

/**
 * ApexCharts touches `window`/`document` directly, which breaks Inertia's
 * SSR render. Gate any chart behind this so SSR emits a safe skeleton and
 * the real chart only mounts client-side.
 */
export function useMounted() {
    const isMounted = ref(false);

    onMounted(() => {
        isMounted.value = true;
    });

    return isMounted;
}
