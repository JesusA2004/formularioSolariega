<script setup lang="ts">
import { AlertTriangle, Flame, Info, ShieldAlert } from '@lucide/vue';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { cn } from '@/lib/utils';
import type { OptionItem } from '@/types/buzon';

defineProps<{
    modelValue: string;
    options: OptionItem[];
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const styles: Record<
    string,
    { icon: typeof Info; selected: string; iconWrap: string }
> = {
    bajo: {
        icon: Info,
        selected: 'border-primary bg-primary/5 ring-2 ring-primary/25',
        iconWrap: 'bg-primary/10 text-primary',
    },
    medio: {
        icon: AlertTriangle,
        selected: 'border-gold bg-gold/10 ring-2 ring-gold/30',
        iconWrap: 'bg-gold/15 text-gold-foreground',
    },
    alto: {
        icon: ShieldAlert,
        selected: 'border-terracota bg-terracota/10 ring-2 ring-terracota/30',
        iconWrap: 'bg-terracota/15 text-terracota',
    },
    critico: {
        icon: Flame,
        selected:
            'border-destructive bg-destructive/10 ring-2 ring-destructive/30',
        iconWrap: 'bg-destructive/15 text-destructive',
    },
};
</script>

<template>
    <RadioGroup
        :model-value="modelValue"
        class="grid gap-3 sm:grid-cols-2"
        @update:model-value="
            (value) => emit('update:modelValue', String(value))
        "
    >
        <label
            v-for="option in options"
            :key="option.value"
            :for="`urgency-${option.value}`"
            :class="
                cn(
                    'flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm',
                    modelValue === option.value
                        ? styles[option.value]?.selected
                        : 'border-input bg-card',
                )
            "
        >
            <div
                :class="
                    cn(
                        'flex size-9 shrink-0 items-center justify-center rounded-full',
                        styles[option.value]?.iconWrap ??
                            'bg-muted text-muted-foreground',
                    )
                "
            >
                <component
                    :is="styles[option.value]?.icon ?? Info"
                    class="size-5"
                />
            </div>
            <div class="flex-1 space-y-0.5">
                <p class="text-sm font-semibold">{{ option.label }}</p>
                <p class="text-xs text-muted-foreground">
                    {{ option.description }}
                </p>
            </div>
            <RadioGroupItem
                :id="`urgency-${option.value}`"
                :value="option.value"
                class="mt-1"
            />
        </label>
    </RadioGroup>
</template>
