<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Building2,
    Calendar,
    Download,
    FileText,
    Image as ImageIcon,
    Save,
    ShieldCheck,
    UserRound,
} from '@lucide/vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import UrgencyBadge from '@/components/admin/UrgencyBadge.vue';
import { Badge } from '@/components/ui/badge';
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
            { title: 'Solicitudes', href: solicitudes.index() },
            { title: 'Detalle de solicitud', href: solicitudes.index() },
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
                    Volver a solicitudes
                </Link>
            </Button>
        </div>

        <!-- Información general -->
        <Card>
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
                        Enviada el {{ formatDateTime(request.created_at) }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <UrgencyBadge
                        :level="request.urgency_level"
                        :label="request.urgency_level_label"
                    />
                    <StatusBadge
                        :status="request.status"
                        :label="request.status_label"
                    />
                </div>
            </CardContent>
        </Card>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <!-- Datos del solicitante -->
                <Card>
                    <CardHeader>
                        <CardTitle
                            class="flex items-center gap-2 text-sm font-semibold"
                        >
                            <UserRound class="size-4" /> Datos del solicitante
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Badge
                            v-if="request.is_anonymous"
                            variant="outline"
                            class="border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300"
                        >
                            Solicitud anónima
                        </Badge>
                        <dl v-else class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    Nombre
                                </dt>
                                <dd class="text-sm font-medium">
                                    {{
                                        request.full_name || 'No proporcionado'
                                    }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted-foreground">
                                    Contacto
                                </dt>
                                <dd class="text-sm font-medium">
                                    {{
                                        request.contact_info ||
                                        'No proporcionado'
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <!-- Ubicación y departamento -->
                <Card>
                    <CardHeader>
                        <CardTitle
                            class="flex items-center gap-2 text-sm font-semibold"
                        >
                            <Building2 class="size-4" /> Ubicación y
                            departamento
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs text-muted-foreground">
                                Departamento
                            </dt>
                            <dd class="text-sm font-medium">
                                {{ request.department_label }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted-foreground">
                                Ubicación
                            </dt>
                            <dd class="text-sm font-medium">
                                {{ request.location }}
                            </dd>
                        </div>
                        <div class="flex items-center gap-1.5 sm:col-span-2">
                            <Calendar class="size-3.5 text-muted-foreground" />
                            <dt class="text-xs text-muted-foreground">
                                Fecha aproximada del hecho:
                            </dt>
                            <dd class="text-sm font-medium">
                                {{ formatDate(request.incident_date) }}
                            </dd>
                        </div>
                    </CardContent>
                </Card>

                <!-- Descripción -->
                <Card>
                    <CardHeader>
                        <CardTitle
                            class="flex items-center gap-2 text-sm font-semibold"
                        >
                            <FileText class="size-4" /> Descripción
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <p class="text-sm whitespace-pre-line">
                            {{ request.description }}
                        </p>
                        <div v-if="request.involved_people">
                            <dt class="mb-1 text-xs text-muted-foreground">
                                Personas involucradas
                            </dt>
                            <dd class="text-sm whitespace-pre-line">
                                {{ request.involved_people }}
                            </dd>
                        </div>
                    </CardContent>
                </Card>

                <!-- Evidencia -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm font-semibold"
                            >Evidencia</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <p
                            v-if="request.attachments.length === 0"
                            class="text-sm text-muted-foreground"
                        >
                            No se adjuntaron archivos con esta solicitud.
                        </p>
                        <ul v-else class="grid gap-3 sm:grid-cols-2">
                            <li
                                v-for="file in request.attachments"
                                :key="file.id"
                                class="flex items-center gap-3 rounded-lg border p-3"
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

            <!-- Seguimiento interno -->
            <div class="space-y-6">
                <Card>
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
                                Revisada por {{ request.reviewed_by }} —
                                {{ formatDateTime(request.reviewed_at) }}
                            </p>
                            <p v-if="request.closed_at">
                                Cerrada el
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

                <Card v-if="request.wants_follow_up">
                    <CardContent class="pt-6 text-sm">
                        <Badge
                            variant="outline"
                            class="border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-300"
                        >
                            Solicita seguimiento
                        </Badge>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
