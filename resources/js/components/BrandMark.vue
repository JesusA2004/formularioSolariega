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
    <img
        src="/images/logoLetras.png"
        alt="Solariega Cenit"
        class="h-20 w-auto object-contain transition-transform duration-300 hover:scale-105 sm:h-28 lg:h-32 xl:h-40"
    />
</template>
