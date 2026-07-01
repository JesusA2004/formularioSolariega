<script setup lang="ts">
import { Check } from '@lucide/vue';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { cn } from '@/lib/utils';
import type { OptionItem } from '@/types/buzon';

withDefaults(
    defineProps<{
        modelValue: string;
        options: OptionItem[];
        columns?: 1 | 2 | 3 | 4;
    }>(),
    {
        columns: 2,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const colsClass: Record<number, string> = {
    1: 'sm:grid-cols-1',
    2: 'sm:grid-cols-2',
    3: 'sm:grid-cols-3',
    4: 'sm:grid-cols-4',
};
</script>

<template>
    <RadioGroup
        :model-value="modelValue"
        :class="cn('grid grid-cols-1 gap-2.5', colsClass[columns])"
        @update:model-value="
            (value) => emit('update:modelValue', String(value))
        "
    >
        <label
            v-for="option in options"
            :key="option.value"
            :for="`opt-${option.value}`"
            :class="
                cn(
                    'relative flex cursor-pointer items-center gap-2 rounded-lg border px-3.5 py-2.5 text-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-gold/60 hover:bg-accent/40 hover:shadow-sm',
                    modelValue === option.value
                        ? 'border-primary bg-primary/5 font-medium text-primary ring-1 ring-gold/40'
                        : 'border-input bg-card',
                )
            "
        >
            <RadioGroupItem
                :id="`opt-${option.value}`"
                :value="option.value"
                class="sr-only"
            />
            <span class="flex-1">{{ option.label }}</span>
            <Check v-if="modelValue === option.value" class="size-4 shrink-0" />
        </label>
    </RadioGroup>
</template>
