<script setup lang="ts">
import { Check } from '@lucide/vue';
import { computed } from 'vue';
import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';

const props = defineProps<{
    steps: string[];
    currentStep: number;
}>();

const percent = computed(
    () => ((props.currentStep + 1) / props.steps.length) * 100,
);
</script>

<template>
    <div class="space-y-3">
        <div
            class="flex items-center justify-between text-xs font-medium text-muted-foreground"
        >
            <span>Paso {{ currentStep + 1 }} de {{ steps.length }}</span>
            <span>{{ steps[currentStep] }}</span>
        </div>
        <Progress :model-value="percent" class="h-1.5" />
        <div class="hidden items-center justify-between sm:flex">
            <div
                v-for="(step, index) in steps"
                :key="step"
                class="flex flex-1 items-center"
            >
                <div class="flex flex-col items-center gap-1.5">
                    <div
                        :class="
                            cn(
                                'flex size-7 items-center justify-center rounded-full border-2 text-xs font-semibold transition-colors',
                                index < currentStep &&
                                    'border-primary bg-primary text-primary-foreground',
                                index === currentStep &&
                                    'border-primary text-primary',
                                index > currentStep &&
                                    'border-muted text-muted-foreground',
                            )
                        "
                    >
                        <Check v-if="index < currentStep" class="size-3.5" />
                        <span v-else>{{ index + 1 }}</span>
                    </div>
                    <span
                        :class="
                            cn(
                                'max-w-20 text-center text-[11px] leading-tight',
                                index === currentStep
                                    ? 'font-semibold text-foreground'
                                    : 'text-muted-foreground',
                            )
                        "
                    >
                        {{ step }}
                    </span>
                </div>
                <div
                    v-if="index < steps.length - 1"
                    :class="
                        cn(
                            'mx-2 h-0.5 flex-1',
                            index < currentStep ? 'bg-primary' : 'bg-muted',
                        )
                    "
                />
            </div>
        </div>
    </div>
</template>
