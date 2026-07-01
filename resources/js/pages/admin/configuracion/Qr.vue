<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Check, Copy, Download, QrCode } from '@lucide/vue';
import QRCode from 'qrcode';
import { onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import qr from '@/routes/qr';

const props = defineProps<{
    publicUrl: string;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Código QR', href: qr.index() },
        ],
    },
});

const canvasRef = ref<HTMLCanvasElement | null>(null);
const copied = ref(false);

onMounted(async () => {
    if (canvasRef.value) {
        await QRCode.toCanvas(canvasRef.value, props.publicUrl, {
            width: 360,
            margin: 2,
            color: { dark: '#1E1E1E', light: '#ffffff' },
        });
    }
});

async function copyLink() {
    await navigator.clipboard.writeText(props.publicUrl);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
}

function downloadPng() {
    if (!canvasRef.value) {
        return;
    }

    const link = document.createElement('a');
    link.download = 'buzon-solariega-cenit-qr.png';
    link.href = canvasRef.value.toDataURL('image/png');
    link.click();
}
</script>

<template>
    <Head title="Código QR" />

    <div class="flex w-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="animate-in duration-500 fade-in slide-in-from-bottom-2">
            <h1 class="text-3xl font-semibold">Código QR del buzón</h1>
            <p class="text-sm text-muted-foreground">
                Comparte este código para que los empleados accedan directamente
                al formulario público.
            </p>
        </div>

        <Card
            class="animate-in rounded-2xl duration-500 fade-in slide-in-from-bottom-2"
        >
            <CardHeader>
                <CardTitle
                    class="flex items-center gap-2 text-base font-semibold"
                >
                    <QrCode class="size-5" /> Enlace público del formulario
                </CardTitle>
                <CardDescription class="text-sm"
                    >Cualquier persona con este enlace puede enviar una
                    solicitud, sin necesidad de iniciar sesión.</CardDescription
                >
            </CardHeader>
            <CardContent>
                <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <p class="text-sm font-medium">Enlace directo</p>
                            <div class="flex gap-2">
                                <Input
                                    :model-value="publicUrl"
                                    readonly
                                    class="h-11 font-mono text-sm"
                                />
                                <Button
                                    size="lg"
                                    variant="outline"
                                    @click="copyLink"
                                >
                                    <Check
                                        v-if="copied"
                                        class="size-4 text-primary"
                                    />
                                    <Copy v-else class="size-4" />
                                    {{ copied ? 'Copiado' : 'Copiar' }}
                                </Button>
                            </div>
                        </div>
                        <div
                            class="space-y-2 rounded-xl border bg-muted/30 p-4 text-sm text-muted-foreground"
                        >
                            <p>
                                Imprime el código o compártelo digitalmente para
                                que cualquier colaborador pueda escanearlo y
                                llegar directo al formulario, sin necesidad de
                                recordar la URL.
                            </p>
                        </div>
                        <Button size="lg" class="w-full" @click="downloadPng">
                            <Download class="size-4" />
                            Descargar QR en PNG
                        </Button>
                    </div>

                    <div
                        class="flex flex-col items-center justify-center gap-4 rounded-2xl border bg-muted/30 p-8"
                    >
                        <canvas
                            ref="canvasRef"
                            class="rounded-xl bg-white p-3 shadow-md transition-transform duration-300 hover:scale-[1.02]"
                        />
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
