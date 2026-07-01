<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2, MessageCircleHeart } from '@lucide/vue';
import PublicPageShell from '@/components/public/PublicPageShell.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { create } from '@/routes/reportar';
</script>

<template>
    <Head title="Mensaje enviado" />

    <PublicPageShell>
        <template #title>Mensaje enviado correctamente</template>

        <Card
            class="mx-auto w-full max-w-lg animate-in rounded-2xl border-none bg-card/90 shadow-2xl shadow-black/10 backdrop-blur duration-500 zoom-in-95 fade-in"
        >
            <CardContent
                class="flex flex-col items-center gap-5 px-8 py-10 text-center"
            >
                <div
                    class="flex size-16 items-center justify-center rounded-full bg-gold/15 text-gold-foreground ring-1 ring-gold/40"
                >
                    <CheckCircle2 class="size-9" />
                </div>

                <div class="space-y-2">
                    <h2 class="text-xl font-semibold">
                        Mensaje enviado correctamente
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        Gracias por compartir tu mensaje. Será revisado con
                        atención y confidencialidad por el área
                        correspondiente.
                    </p>
                </div>

                <div
                    class="flex w-full items-start gap-3 rounded-xl border border-gold/30 bg-gold/5 p-4 text-left"
                >
                    <MessageCircleHeart
                        class="mt-0.5 size-5 shrink-0 text-gold"
                    />
                    <p class="text-sm text-muted-foreground">
                        Si se requiere más información, Recursos Humanos se
                        pondrá en contacto contigo para dar el seguimiento
                        correspondiente.
                    </p>
                </div>

                <Button as-child class="mt-2 w-full">
                    <Link :href="create()">Enviar otro mensaje</Link>
                </Button>
            </CardContent>
        </Card>
    </PublicPageShell>
</template>
