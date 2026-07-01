<script setup lang="ts">
import type { LucideIcon } from '@lucide/vue';
import { cn } from '@/lib/utils';

withDefaults(
    defineProps<{
        label: string;
        value: number | string;
        icon: LucideIcon;
        tone?: 'default' | 'emerald' | 'amber' | 'red' | 'blue';
    }>(),
    {
        tone: 'default',
    },
);

const toneClasses: Record<string, string> = {
    default: 'bg-muted text-foreground',
    emerald: 'bg-primary/10 text-primary',
    amber: 'bg-gold/15 text-gold-foreground',
    red: 'bg-destructive/10 text-destructive',
    blue: 'bg-cafe/10 text-cafe',
};
</script>

<template>
    <div
        class="group flex items-center gap-4 rounded-xl border bg-card p-4 shadow-xs transition-all hover:-translate-y-0.5 hover:shadow-md"
    >
        <div
            :class="
                cn(
                    'flex size-11 shrink-0 items-center justify-center rounded-lg transition-transform group-hover:scale-105',
                    toneClasses[tone],
                )
            "
        >
            <component :is="icon" class="size-5" />
        </div>
        <div class="min-w-0">
            <p class="text-2xl font-semibold tabular-nums">{{ value }}</p>
            <p class="truncate text-xs text-muted-foreground">{{ label }}</p>
        </div>
    </div>
</template>
