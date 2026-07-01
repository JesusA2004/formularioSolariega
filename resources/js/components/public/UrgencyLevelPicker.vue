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
        selected:
            'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500/30 dark:bg-emerald-950/40',
        iconWrap:
            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
    },
    medio: {
        icon: AlertTriangle,
        selected:
            'border-amber-500 bg-amber-50 ring-2 ring-amber-500/30 dark:bg-amber-950/40',
        iconWrap:
            'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
    },
    alto: {
        icon: ShieldAlert,
        selected:
            'border-orange-500 bg-orange-50 ring-2 ring-orange-500/30 dark:bg-orange-950/40',
        iconWrap:
            'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
    },
    critico: {
        icon: Flame,
        selected:
            'border-red-500 bg-red-50 ring-2 ring-red-500/30 dark:bg-red-950/40',
        iconWrap: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
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
                    'flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition-all hover:shadow-sm',
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
