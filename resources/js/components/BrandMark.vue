<script setup lang="ts">
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        class?: string;
    }>(),
    {},
);
</script>

<template>
    <img
        src="/images/logo.png"
        alt="Solariega Cenit"
        :class="
            cn(
                'h-10 w-auto object-contain transition-transform duration-300 hover:scale-105',
                props.class,
            )
        "
    />
</template>
