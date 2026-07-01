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
                'flex aspect-square items-center justify-center rounded-xl bg-gradient-to-br from-[#283B2A] to-[#10251B] font-serif font-semibold text-[#F7F1E6] shadow-sm ring-1 ring-[#C6A45A]/40',
                $props.class,
            )
        "
    >
        SC
    </div>
</template>
