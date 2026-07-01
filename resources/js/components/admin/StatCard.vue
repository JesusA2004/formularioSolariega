<script setup lang="ts">
import type { LucideIcon } from '@lucide/vue';
import { cn } from '@/lib/utils';

withDefaults(
    defineProps<{
        label: string;
        value: number | string;
        icon: LucideIcon;
        tone?: 'default' | 'blue' | 'gold' | 'green' | 'gray' | 'purple';
        description?: string;
    }>(),
    {
        tone: 'default',
    },
);

const toneClasses: Record<string, string> = {
    default: 'bg-primary/10 text-primary',
    blue: 'bg-[#3B82F6]/10 text-[#3B82F6]',
    gold: 'bg-gold/15 text-gold-foreground',
    green: 'bg-[#22C55E]/10 text-[#22C55E]',
    gray: 'bg-[#64748B]/10 text-[#64748B]',
    purple: 'bg-[#8B5CF6]/10 text-[#8B5CF6]',
};
</script>

<template>
    <div
        class="group rounded-2xl border bg-card p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-gold/50 hover:shadow-lg"
    >
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-sm text-muted-foreground">
                    {{ label }}
                </p>
                <p class="mt-1 text-3xl font-semibold tabular-nums">
                    {{ value }}
                </p>
                <p
                    v-if="description"
                    class="mt-1 truncate text-xs text-muted-foreground"
                >
                    {{ description }}
                </p>
            </div>
            <div
                :class="
                    cn(
                        'flex size-12 shrink-0 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-110',
                        toneClasses[tone],
                    )
                "
            >
                <component :is="icon" class="size-6" />
            </div>
        </div>
    </div>
</template>
