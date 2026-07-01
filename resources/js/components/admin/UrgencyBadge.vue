<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

const props = defineProps<{
    level: string;
    label: string;
}>();

const classes: Record<string, string> = {
    bajo: 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-900',
    medio: 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-900',
    alto: 'bg-orange-100 text-orange-700 border-orange-200 dark:bg-orange-950 dark:text-orange-300 dark:border-orange-900',
    critico:
        'bg-red-100 text-red-700 border-red-200 dark:bg-red-950 dark:text-red-300 dark:border-red-900',
};
</script>

<template>
    <Badge
        variant="outline"
        :class="cn('font-medium', classes[props.level] ?? '')"
    >
        {{ label }}
    </Badge>
</template>
