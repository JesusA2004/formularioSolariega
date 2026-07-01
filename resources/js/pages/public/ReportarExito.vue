<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2 } from '@lucide/vue';
import PublicPageShell from '@/components/public/PublicPageShell.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { create } from '@/routes/reportar';

defineProps<{
    folio: string;
}>();
</script>

<template>
    <Head title="Solicitud enviada" />

    <PublicPageShell>
        <template #title>Solicitud enviada correctamente</template>

        <Card
            class="mx-auto w-full max-w-lg animate-in shadow-xl duration-500 zoom-in-95 fade-in"
        >
            <CardContent
                class="flex flex-col items-center gap-5 px-8 py-10 text-center"
            >
                <div
                    class="flex size-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                >
                    <CheckCircle2 class="size-9" />
                </div>

                <div class="space-y-2">
                    <h2 class="text-xl font-semibold">
                        ¡Gracias por tu reporte!
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        La información será revisada de manera confidencial por
                        el área correspondiente.
                    </p>
                </div>

                <div
                    class="w-full rounded-lg border border-dashed bg-muted/50 px-4 py-3"
                >
                    <p
                        class="text-xs tracking-wide text-muted-foreground uppercase"
                    >
                        Folio de tu solicitud
                    </p>
                    <p
                        class="font-mono text-lg font-semibold tracking-wide text-primary"
                    >
                        {{ folio }}
                    </p>
                </div>

                <p class="text-xs text-muted-foreground">
                    Guarda este folio si deseas dar seguimiento a tu reporte.
                </p>

                <Button as-child class="mt-2 w-full">
                    <Link :href="create()">Enviar otra solicitud</Link>
                </Button>
            </CardContent>
        </Card>
    </PublicPageShell>
</template>
