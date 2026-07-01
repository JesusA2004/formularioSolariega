<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { RequestStatusValue } from '@/types/buzon';

const props = defineProps<{
    status: RequestStatusValue;
    label: string;
}>();

const classes: Record<RequestStatusValue, string> = {
    recibido:
        'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-900',
    en_revision:
        'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-900',
    atendido:
        'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-900',
    cerrado:
        'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700',
    descartado:
        'bg-red-100 text-red-700 border-red-200 dark:bg-red-950 dark:text-red-300 dark:border-red-900',
};
</script>

<template>
    <Badge variant="outline" :class="cn('font-medium', classes[props.status])">
        {{ label }}
    </Badge>
</template>
