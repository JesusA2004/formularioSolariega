<script setup lang="ts">
import BrandMark from '@/components/BrandMark.vue';
</script>

<template>
    <div class="relative min-h-svh bg-muted/40">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-emerald-900 via-emerald-800 to-transparent"
        />

        <div
            class="relative mx-auto flex min-h-svh w-full max-w-3xl flex-col px-4 py-10 sm:py-14"
        >
            <header
                class="mb-8 flex flex-col items-center gap-4 text-center text-white"
            >
                <BrandMark class="size-16 text-2xl shadow-lg" />
                <div class="space-y-1">
                    <p
                        class="text-sm font-medium tracking-wide text-emerald-100/90 uppercase"
                    >
                        Solariega Cenit
                    </p>
                    <h1 class="text-2xl font-semibold sm:text-3xl">
                        <slot name="title"
                            >Buzón de Quejas, Sugerencias y Reportes</slot
                        >
                    </h1>
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
