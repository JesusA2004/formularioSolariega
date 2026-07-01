<script setup lang="ts">
import { cn } from '@/lib/utils';

withDefaults(
    defineProps<{
        class?: string;
    }>(),
    {},
);
</script>

<template>
    <div
        :class="
            cn(
                'flex aspect-square items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-emerald-800 font-serif font-semibold text-white shadow-sm dark:from-emerald-500 dark:to-emerald-700',
                $props.class,
            )
        "
    >
        SC
    </div>
</template>
