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
    emerald:
        'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
    amber: 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
    red: 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300',
    blue: 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
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
