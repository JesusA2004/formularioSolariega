<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import BrandMark from '@/components/BrandMark.vue';
import { Card, CardContent } from '@/components/ui/card';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-hidden bg-gradient-to-br from-[#10251B] via-[#17281f] to-[#10251B] p-6 md:p-10"
    >
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(40,59,42,0.35),transparent_45%),radial-gradient(circle_at_80%_0%,rgba(198,164,90,0.18),transparent_40%)]"
        />

        <div
            class="relative w-full max-w-sm animate-in duration-700 fade-in slide-in-from-bottom-4"
        >
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-3 font-medium"
                    >
                        <BrandMark class="size-14 text-xl" />
                        <span
                            class="text-sm font-medium tracking-widest text-gold uppercase"
                            >Solariega Cenit</span
                        >
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
                    class="border-[#1b3226]/60 bg-card/95 shadow-2xl backdrop-blur"
                >
                    <CardContent class="pt-6">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
