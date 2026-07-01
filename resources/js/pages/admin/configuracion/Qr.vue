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
            width: 320,
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

    <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-semibold">Código QR del buzón</h1>
            <p class="text-sm text-muted-foreground">
                Comparte este código para que los empleados accedan directamente
                al formulario público.
            </p>
        </div>

        <Card>
            <CardHeader>
                <CardTitle
                    class="flex items-center gap-2 text-sm font-semibold"
                >
                    <QrCode class="size-4" /> Enlace público del formulario
                </CardTitle>
                <CardDescription
                    >Cualquier persona con este enlace puede enviar una
                    solicitud, sin necesidad de iniciar sesión.</CardDescription
                >
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="flex gap-2">
                    <Input
                        :model-value="publicUrl"
                        readonly
                        class="font-mono text-xs"
                    />
                    <Button variant="outline" @click="copyLink">
                        <Check v-if="copied" class="size-4 text-primary" />
                        <Copy v-else class="size-4" />
                        {{ copied ? 'Copiado' : 'Copiar' }}
                    </Button>
                </div>

                <div
                    class="flex flex-col items-center gap-4 rounded-xl border bg-muted/30 p-6"
                >
                    <canvas
                        ref="canvasRef"
                        class="rounded-lg bg-white p-2 shadow-sm"
                    />
                    <Button @click="downloadPng">
                        <Download class="size-4" />
                        Descargar QR en PNG
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
