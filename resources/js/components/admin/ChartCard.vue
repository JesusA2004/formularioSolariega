<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

withDefaults(
    defineProps<{
        title: string;
        hint?: string;
        height?: string;
    }>(),
    {
        hint: 'Haz clic en un dato para filtrar el listado',
        height: 'h-72',
    },
);
</script>

<template>
    <Card
        class="animate-in gap-4 rounded-2xl duration-500 fade-in slide-in-from-bottom-2 hover:border-gold/40 hover:shadow-lg"
    >
        <CardHeader class="gap-1">
            <CardTitle class="text-base font-semibold">{{ title }}</CardTitle>
            <p class="text-xs text-muted-foreground">{{ hint }}</p>
        </CardHeader>
        <CardContent :class="height">
            <slot />
        </CardContent>
    </Card>
</template>
