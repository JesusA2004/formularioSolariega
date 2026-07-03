<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import BrandMark from '@/components/BrandMark.vue';
import { Card, CardContent } from '@/components/ui/card';
import { create } from '@/routes/reportar';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-hidden bg-gradient-to-br from-[#171717] via-[#1E1E1E] to-[#171717] p-6 md:p-10"
    >
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(45,45,45,0.5),transparent_45%),radial-gradient(circle_at_80%_0%,rgba(212,175,55,0.18),transparent_40%)]"
        />

        <div
            class="relative w-full max-w-sm animate-in duration-700 fade-in slide-in-from-bottom-4"
        >
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="create()"
                        class="flex flex-col items-center gap-3 font-medium"
                    >
                        <img
                            src="/images/logo.png"
                            alt="Solariega Cenit"
                            class="h-20 w-auto object-contain transition-transform duration-300 hover:scale-105 sm:h-28 lg:h-32 xl:h-40"
                        />
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-semibold text-marfil">
                            {{ title }}
                        </h1>
                        <p class="text-center text-sm text-marfil/70">
                            {{ description }}
                        </p>
                    </div>
                </div>

                <Card
                    class="border-[#2a2a2a]/60 bg-card/95 shadow-2xl backdrop-blur"
                >
                    <CardContent class="pt-6">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
