<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2, MessageCircleHeart } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { create } from '@/routes/reportar';

const heroImageMissing = ref(false);
</script>

<template>
    <Head title="Mensaje enviado" />

    <div class="min-h-svh bg-background lg:grid lg:grid-cols-[58%_42%]">
        <!-- Left column: success message -->
        <div
            class="flex flex-col justify-center px-4 py-10 sm:px-8 lg:px-14 lg:py-16"
        >
            <div
                class="relative mx-auto w-full max-w-lg animate-in duration-700 fade-in slide-in-from-bottom-4"
            >
                <div
                    class="pointer-events-none absolute inset-x-6 -top-6 h-32 rounded-full bg-gold/20 blur-3xl"
                />

                <div
                    class="relative overflow-hidden rounded-2xl border border-gold/15 bg-card shadow-2xl shadow-black/10"
                >
                    <div
                        class="h-1 w-full bg-gradient-to-r from-transparent via-gold to-transparent"
                    />
                    <div
                        class="flex flex-col items-center gap-5 px-8 py-10 text-center"
                    >
                        <div
                            class="flex size-16 animate-in items-center justify-center rounded-full bg-gradient-to-br from-gold/25 to-gold/10 text-gold-foreground ring-1 ring-gold/40 zoom-in-95 delay-150 duration-500 fade-in"
                        >
                            <CheckCircle2 class="size-9 text-gold-foreground" />
                        </div>

                        <div class="space-y-2">
                            <h1 class="text-xl font-semibold">
                                Mensaje enviado correctamente
                            </h1>
                            <p class="text-sm text-muted-foreground">
                                Gracias por compartir tu mensaje. Será
                                revisado con atención y confidencialidad por
                                el área correspondiente.
                            </p>
                        </div>

                        <div
                            class="flex w-full items-start gap-3 rounded-xl border border-gold/25 bg-gradient-to-br from-gold/10 to-transparent p-4 text-left"
                        >
                            <MessageCircleHeart
                                class="mt-0.5 size-5 shrink-0 text-gold"
                            />
                            <p class="text-sm text-muted-foreground">
                                Si se requiere más información, Recursos
                                Humanos se pondrá en contacto contigo para
                                dar el seguimiento correspondiente.
                            </p>
                        </div>

                        <Button as-child class="mt-2 w-full">
                            <Link :href="create()">Enviar otro mensaje</Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right column: institutional visual block -->
        <div
            class="relative flex flex-col justify-between gap-10 overflow-hidden bg-[#171717] px-8 py-12 text-marfil sm:px-12 lg:min-h-svh lg:px-14 lg:py-16"
        >
            <div class="absolute inset-0">
                <div
                    class="size-full bg-gradient-to-br from-[#1E1E1E] via-[#232323] to-[#171717]"
                />
                <img
                    v-if="!heroImageMissing"
                    src="/images/bgGracias.jpg"
                    alt=""
                    class="absolute inset-0 size-full object-cover"
                    @error="heroImageMissing = true"
                />
            </div>
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"
            />
            <div
                class="pointer-events-none absolute -top-16 -left-16 size-72 rounded-full bg-gold/15 blur-3xl"
            />
            <div
                class="pointer-events-none absolute inset-x-10 bottom-24 h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent"
            />

            <div
                class="relative z-10 flex animate-in items-center justify-center duration-700 fade-in slide-in-from-top-4"
            >
                <div
                    class="rounded-2xl bg-black/45 px-6 py-4 shadow-lg backdrop-blur-sm"
                >
                    <img
                        src="/images/logo.png"
                        alt="Solariega Cenit"
                        class="h-20 w-auto object-contain transition-transform duration-300 hover:scale-105 sm:h-28 lg:h-32 xl:h-40"
                    />
                </div>
            </div>

            <div
                class="relative z-10 max-w-md animate-in space-y-5 duration-700 fade-in slide-in-from-bottom-4"
            >
                <div class="space-y-2">
                    <h2 class="text-3xl font-semibold sm:text-4xl">
                        Gracias por escribirnos
                    </h2>
                    <p class="text-sm text-marfil/70">
                        Cada mensaje se revisa con seriedad, confidencialidad
                        y respeto. Tu opinión nos ayuda a mejorar todos los
                        días.
                    </p>
                </div>
            </div>

            <p class="relative z-10 text-xs text-marfil/50">
                © {{ new Date().getFullYear() }} Solariega Cenit
            </p>
        </div>
    </div>
</template>
