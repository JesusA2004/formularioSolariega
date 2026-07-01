<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { cn } from '@/lib/utils';

type PageLink = {
    url: string | null;
    label: string;
    active: boolean;
};

defineProps<{
    links: PageLink[];
    from: number | null;
    to: number | null;
    total: number;
}>();
</script>

<template>
    <div
        class="flex flex-col items-center justify-between gap-3 border-t px-4 py-3 sm:flex-row"
    >
        <p class="text-xs text-muted-foreground">
            <template v-if="total > 0"
                >Mostrando {{ from }}–{{ to }} de
                {{ total }} resultados</template
            >
            <template v-else>Sin resultados</template>
        </p>
        <div class="flex flex-wrap items-center gap-1">
            <template v-for="(link, index) in links" :key="index">
                <span
                    v-if="!link.url"
                    class="rounded-md px-2.5 py-1.5 text-xs text-muted-foreground/50"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    preserve-scroll
                    :class="
                        cn(
                            'rounded-md px-2.5 py-1.5 text-xs transition-colors',
                            link.active
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:bg-accent',
                        )
                    "
                >
                    <span v-html="link.label" />
                </Link>
            </template>
        </div>
    </div>
</template>
