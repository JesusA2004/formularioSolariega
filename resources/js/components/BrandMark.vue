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
                'flex aspect-square items-center justify-center rounded-xl bg-gradient-to-br from-[#232323] to-[#171717] font-serif font-semibold text-[#FAF9F5] shadow-sm ring-1 ring-[#D4AF37]/50',
                $props.class,
            )
        "
    >
        SC
    </div>
</template>
