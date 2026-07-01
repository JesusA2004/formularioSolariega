<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { RequestStatusValue } from '@/types/buzon';

const props = defineProps<{
    status: RequestStatusValue;
    label: string;
}>();

const classes: Record<RequestStatusValue, string> = {
    recibido: 'bg-muted text-foreground border-border',
    en_revision: 'bg-gold/15 text-gold-foreground border-gold/40',
    atendido: 'bg-primary/10 text-primary border-primary/30',
    cerrado: 'bg-muted text-muted-foreground border-border',
    descartado: 'bg-destructive/10 text-destructive border-destructive/30',
};
</script>

<template>
    <Badge variant="outline" :class="cn('font-medium', classes[props.status])">
        {{ label }}
    </Badge>
</template>
