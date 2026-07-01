<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Calendar,
    Download,
    FileText,
    Image as ImageIcon,
    MessageSquareText,
    Save,
    ShieldCheck,
    UserRound,
} from '@lucide/vue';
import EvidenceBadge from '@/components/admin/EvidenceBadge.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';
import solicitudes from '@/routes/solicitudes';
import adjuntos from '@/routes/solicitudes/adjuntos';
import type { OptionItem, RequestDetail } from '@/types/buzon';

const props = defineProps<{
    request: RequestDetail;
    statuses: OptionItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Mensajes', href: solicitudes.index() },
            { title: 'Detalle del mensaje', href: solicitudes.index() },
        ],
    },
});

const form = useForm({
    status: props.request.status,
    internal_notes: props.request.internal_notes ?? '',
});

function submit() {
    form.patch(solicitudes.update.url(props.request.id), {
        preserveScroll: true,
    });
}

function formatDate(value: string | null) {
    if (!value) {
        return 'No especificada';
    }

    return new Intl.DateTimeFormat('es-MX', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(value));
}

function formatDateTime(value: string | null) {
    if (!value) {
        return null;
    }

    return new Intl.DateTimeFormat('es-MX', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}
</script>

<template>
    <Head :title="request.folio" />

    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <Button as-child variant="ghost" size="sm">
                <Link :href="solicitudes.index()">
                    <ArrowLeft class="size-4" />
                    Volver a mensajes
                </Link>
            </Button>
        </div>

        <!-- Información general -->
        <Card
            class="animate-in rounded-2xl fade-in slide-in-from-bottom-2 duration-500"
        >
            <CardContent
                class="flex flex-col gap-4 pt-6 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <p class="font-mono text-sm text-muted-foreground">
                        {{ request.folio }}
                    </p>
                    <h1 class="text-xl font-semibold">
                        {{ request.request_type_label }}
                    </h1>
                    <p class="text-xs text-muted-foreground">
                        Enviado el {{ formatDateTime(request.created_at) }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <StatusBadge
                        :status="request.status"
                        :label="request.status_label"
                    />
                    <EvidenceBadge :has-evidence="request.has_evidence" />
                </div>
            </CardContent>
        </Card>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <!-- Sección 1: Datos del colaborador -->
                <Card
                    class="animate-in rounded-2xl fade-in slide-in-from-bottom-2 delay-100 duration-500 hover:border-gold/30"
                >
                    <CardHeader>
                        <CardTitle
                            class="flex items-center gap-2 text-sm font-semibold"
                        >
                            <UserRound class="size-4" /> Datos del colaborador
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <dl class="grid gap-3 sm:grid-cols-3">
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    Nombre completo
                                </dt>
                                <dd class="text-sm font-medium">
                                    {{
                                        request.full_name || 'No proporcionado'
                                    }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    Correo o teléfono
                                </dt>
                                <dd class="text-sm font-medium">
                                    {{
                                        request.contact_info ||
                                        'No proporcionado'
                                    }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    Área o departamento
                                </dt>
                                <dd class="text-sm font-medium">
                                    {{ request.department_label }}
                                </dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <!-- Sección 2: Mensaje -->
                <Card
                    class="animate-in rounded-2xl fade-in slide-in-from-bottom-2 delay-150 duration-500 hover:border-gold/30"
                >
                    <CardHeader>
                        <CardTitle
                            class="flex items-center gap-2 text-sm font-semibold"
                        >
                            <MessageSquareText class="size-4" /> Mensaje
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    Tipo de mensaje
                                </dt>
                                <dd class="text-sm font-medium">
                                    {{ request.request_type_label }}
                                </dd>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <Calendar
                                    class="size-3.5 text-muted-foreground"
                                />
                                <div>
                                    <dt class="text-xs text-muted-foreground">
                                        Fecha aproximada
                                    </dt>
                                    <dd class="text-sm font-medium">
                                        {{ formatDate(request.incident_date) }}
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <div v-if="request.involved_people">
                            <dt class="mb-1 text-xs text-muted-foreground">
                                Personas relacionadas
                            </dt>
                            <dd class="text-sm whitespace-pre-line">
                                {{ request.involved_people }}
                            </dd>
                        </div>

                        <div class="border-t pt-4">
                            <dt class="mb-1 text-xs text-muted-foreground">
                                Descripción
                            </dt>
                            <dd class="text-sm whitespace-pre-line">
                                {{ request.description }}
                            </dd>
                        </div>
                    </CardContent>
                </Card>

                <!-- Sección 3: Evidencia -->
                <Card
                    class="animate-in rounded-2xl fade-in slide-in-from-bottom-2 delay-200 duration-500 hover:border-gold/30"
                >
                    <CardHeader>
                        <CardTitle class="text-sm font-semibold"
                            >Evidencia</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="request.attachments.length === 0"
                            class="flex flex-col items-center gap-2 py-8 text-center text-muted-foreground"
                        >
                            <FileText class="size-8 opacity-40" />
                            <p class="text-sm">
                                No se adjuntaron archivos con este mensaje.
                            </p>
                        </div>
                        <ul v-else class="grid gap-3 sm:grid-cols-2">
                            <li
                                v-for="file in request.attachments"
                                :key="file.id"
                                class="flex items-center gap-3 rounded-xl border p-3 transition-colors hover:border-gold/40 hover:bg-accent/20"
                            >
                                <div
                                    class="flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-md bg-muted"
                                >
                                    <img
                                        v-if="file.is_image"
                                        :src="
                                            adjuntos.download.url(
                                                [request.id, file.id],
                                                { query: { preview: 1 } },
                                            )
                                        "
                                        :alt="file.original_name"
                                        class="size-full object-cover"
                                    />
                                    <ImageIcon
                                        v-else-if="
                                            file.mime_type?.startsWith('image/')
                                        "
                                        class="size-5 text-muted-foreground"
                                    />
                                    <FileText
                                        v-else
                                        class="size-5 text-muted-foreground"
                                    />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">
                                        {{ file.original_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ file.human_size }}
                                    </p>
                                </div>
                                <Button
                                    as-child
                                    size="icon-sm"
                                    variant="outline"
                                >
                                    <a
                                        :href="
                                            adjuntos.download.url([
                                                request.id,
                                                file.id,
                                            ])
                                        "
                                        :aria-label="`Descargar ${file.original_name}`"
                                        download
                                    >
                                        <Download class="size-4" />
                                    </a>
                                </Button>
                            </li>
                        </ul>
                    </CardContent>
                </Card>
            </div>

            <!-- Sección 4: Seguimiento interno -->
            <div class="space-y-6">
                <Card
                    class="animate-in rounded-2xl fade-in slide-in-from-bottom-2 delay-100 duration-500 lg:sticky lg:top-6"
                >
                    <CardHeader>
                        <CardTitle
                            class="flex items-center gap-2 text-sm font-semibold"
                        >
                            <ShieldCheck class="size-4" /> Seguimiento interno
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-1.5">
                            <Label>Estado</Label>
                            <Select v-model="form.status">
                                <SelectTrigger class="w-full"
                                    ><SelectValue
                                /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in statuses"
                                        :key="opt.value"
                                        :value="opt.value"
                                        >{{ opt.label }}</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="internal_notes">Notas internas</Label>
                            <Textarea
                                id="internal_notes"
                                v-model="form.internal_notes"
                                rows="6"
                                placeholder="Notas visibles solo para el equipo administrativo"
                            />
                            <p class="text-xs text-muted-foreground">
                                Estas notas no son visibles en el formulario
                                público.
                            </p>
                        </div>

                        <div
                            class="space-y-1 border-t pt-3 text-xs text-muted-foreground"
                        >
                            <p v-if="request.reviewed_by">
                                Revisado por {{ request.reviewed_by }} —
                                {{ formatDateTime(request.reviewed_at) }}
                            </p>
                            <p v-if="request.closed_at">
                                Cerrado el
                                {{ formatDateTime(request.closed_at) }}
                            </p>
                        </div>

                        <Button
                            class="w-full"
                            :disabled="form.processing"
                            @click="submit"
                        >
                            <Spinner v-if="form.processing" />
                            <Save v-else class="size-4" />
                            Guardar cambios
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
