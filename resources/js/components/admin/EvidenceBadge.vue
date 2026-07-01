<script setup lang="ts">
import { Paperclip } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

defineProps<{
    hasEvidence: boolean;
}>();
</script>

<template>
    <Badge
        variant="outline"
        :class="
            cn(
                'gap-1 font-medium',
                hasEvidence
                    ? 'border-[#8B5CF6]/30 bg-[#8B5CF6]/10 text-[#8B5CF6]'
                    : 'border-border bg-muted text-muted-foreground',
            )
        "
    >
        <Paperclip v-if="hasEvidence" class="size-3" />
        {{ hasEvidence ? 'Con evidencia' : 'Sin evidencia' }}
    </Badge>
</template>
