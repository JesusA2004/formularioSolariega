<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { RequestStatusValue } from '@/types/buzon';

const props = defineProps<{
    status: RequestStatusValue;
    label: string;
}>();

const classes: Record<RequestStatusValue, string> = {
    recibido: 'bg-[#3B82F6]/10 text-[#3B82F6] border-[#3B82F6]/30',
    en_revision: 'bg-gold/15 text-gold-foreground border-gold/40',
    atendido: 'bg-[#22C55E]/10 text-[#22C55E] border-[#22C55E]/30',
    cerrado: 'bg-[#64748B]/10 text-[#64748B] border-[#64748B]/30',
    descartado: 'bg-[#EF4444]/10 text-[#EF4444] border-[#EF4444]/30',
};
</script>

<template>
    <Badge variant="outline" :class="cn('font-medium', classes[props.status])">
        {{ label }}
    </Badge>
</template>
