<script setup lang="ts">
import BrandMark from '@/components/BrandMark.vue';

withDefaults(
    defineProps<{
        contentClass?: string;
    }>(),
    {
        contentClass: 'max-w-3xl',
    },
);
</script>

<template>
    <div class="relative min-h-svh overflow-hidden bg-background">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-64 bg-gradient-to-b from-[#171717] via-[#232323] to-transparent"
        />
        <div
            class="pointer-events-none absolute -top-20 right-[15%] size-72 rounded-full bg-gold/20 blur-3xl"
        />
        <div
            class="pointer-events-none absolute top-56 right-0 left-0 h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent"
        />

        <div
            :class="[
                'relative mx-auto flex min-h-svh w-full flex-col px-4 py-8 sm:py-10',
                contentClass,
            ]"
        >
            <header
                class="mb-6 flex animate-in flex-col items-center gap-3 text-center text-marfil duration-700 fade-in slide-in-from-top-4"
            >
                <BrandMark class="size-14 text-xl shadow-lg" />
                <div class="space-y-1.5">
                    <h1 class="text-2xl font-semibold sm:text-3xl">
                        <slot name="title">Buzón Solariega</slot>
                    </h1>
                    <p
                        v-if="$slots.subtitle"
                        class="mx-auto max-w-xl text-sm text-marfil/80"
                    >
                        <slot name="subtitle" />
                    </p>
                </div>
            </header>

            <main
                class="flex-1 animate-in duration-700 fade-in slide-in-from-bottom-4"
            >
                <slot />
            </main>

            <footer class="mt-10 text-center text-xs text-muted-foreground">
                © {{ new Date().getFullYear() }} Solariega Cenit. Toda la
                información se maneja de forma confidencial.
            </footer>
        </div>
    </div>
</template>
