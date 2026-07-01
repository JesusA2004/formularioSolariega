<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

const props = defineProps<{
    level: string;
    label: string;
}>();

const classes: Record<string, string> = {
    bajo: 'bg-primary/10 text-primary border-primary/30',
    medio: 'bg-gold/15 text-gold-foreground border-gold/40',
    alto: 'bg-terracota/15 text-terracota border-terracota/30',
    critico: 'bg-destructive/10 text-destructive border-destructive/30',
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
