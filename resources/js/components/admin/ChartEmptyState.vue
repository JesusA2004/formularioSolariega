<script setup lang="ts">
import { BarChart3 } from '@lucide/vue';

withDefaults(
    defineProps<{
        message?: string;
    }>(),
    {
        message: 'Aún no hay datos suficientes para mostrar esta gráfica.',
    },
);
</script>

<template>
    <div
        class="flex h-full flex-col items-center justify-center gap-2 text-center text-muted-foreground"
    >
        <BarChart3 class="size-8 opacity-40" />
        <p class="max-w-[220px] text-sm">{{ message }}</p>
    </div>
</template>
