<script setup lang="ts">
import type { CalendarDate } from '@internationalized/date';
import { getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { CalendarIcon } from '@lucide/vue';
import { computed } from 'vue';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';

const props = defineProps<{
    modelValue: string | null;
    placeholder?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string | null];
}>();

const maxDate = today(getLocalTimeZone());

const calendarValue = computed<CalendarDate | undefined>({
    get: () => (props.modelValue ? parseDate(props.modelValue) : undefined),
    set: (value) => emit('update:modelValue', value ? value.toString() : null),
});

const displayLabel = computed(() => {
    if (!props.modelValue) {
        return null;
    }

    const date = new Date(`${props.modelValue}T00:00:00`);

    return new Intl.DateTimeFormat('es-MX', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
});
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <button
                type="button"
                :class="
                    cn(
                        'flex h-9 w-full items-center gap-2 rounded-md border border-input bg-transparent px-3 py-1 text-left text-sm shadow-xs transition-colors hover:border-gold/60 hover:bg-accent/40 focus-visible:border-gold focus-visible:ring-2 focus-visible:ring-gold/30 dark:bg-input/30',
                        !displayLabel && 'text-muted-foreground',
                    )
                "
            >
                <CalendarIcon class="size-4 shrink-0 opacity-60" />
                <span class="truncate">{{
                    displayLabel ?? placeholder ?? 'Selecciona una fecha'
                }}</span>
            </button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0" align="start">
            <Calendar
                v-model="calendarValue"
                :max-value="maxDate"
                initial-focus
            />
        </PopoverContent>
    </Popover>
</template>
