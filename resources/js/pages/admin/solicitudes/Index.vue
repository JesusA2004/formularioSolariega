<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowDown,
    ArrowUp,
    ArrowUpDown,
    Eye,
    Inbox,
    Paperclip,
    RotateCcw,
    Search,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import PaginationBar from '@/components/admin/PaginationBar.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import UrgencyBadge from '@/components/admin/UrgencyBadge.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';
import solicitudes from '@/routes/solicitudes';
import type { OptionItem, Paginated, RequestSummary } from '@/types/buzon';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Solicitudes', href: solicitudes.index() },
        ],
    },
});

const props = defineProps<{
    requests: Paginated<RequestSummary>;
    filters: Record<string, string | undefined>;
    options: {
        requestTypes: OptionItem[];
        departments: OptionItem[];
        urgencyLevels: OptionItem[];
        statuses: OptionItem[];
    };
}>();

const ALL = 'all';

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? ALL);
const requestType = ref(props.filters.request_type ?? ALL);
const urgencyLevel = ref(props.filters.urgency_level ?? ALL);
const department = ref(props.filters.department ?? ALL);
const isAnonymous = ref(props.filters.is_anonymous ?? ALL);
const hasEvidence = ref(props.filters.has_evidence ?? ALL);
const sort = ref(props.filters.sort ?? 'created_at');
const direction = ref<'asc' | 'desc'>(
    (props.filters.direction as 'asc' | 'desc') ?? 'desc',
);

let debounceTimer: ReturnType<typeof setTimeout> | undefined;

function applyFilters() {
    router.get(
        solicitudes.index.url(),
        {
            search: search.value || undefined,
            status: status.value === ALL ? undefined : status.value,
            request_type:
                requestType.value === ALL ? undefined : requestType.value,
            urgency_level:
                urgencyLevel.value === ALL ? undefined : urgencyLevel.value,
            department: department.value === ALL ? undefined : department.value,
            is_anonymous:
                isAnonymous.value === ALL ? undefined : isAnonymous.value,
            has_evidence:
                hasEvidence.value === ALL ? undefined : hasEvidence.value,
            sort: sort.value,
            direction: direction.value,
        },
        { preserveState: true, replace: true },
    );
}

watch(search, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 350);
});

watch(
    [status, requestType, urgencyLevel, department, isAnonymous, hasEvidence],
    applyFilters,
);

function toggleSort(field: string) {
    if (sort.value === field) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc';
    } else {
        sort.value = field;
        direction.value = 'desc';
    }

    applyFilters();
}

function sortIcon(field: string) {
    if (sort.value !== field) {
        return ArrowUpDown;
    }

    return direction.value === 'asc' ? ArrowUp : ArrowDown;
}

function clearFilters() {
    search.value = '';
    status.value = ALL;
    requestType.value = ALL;
    urgencyLevel.value = ALL;
    department.value = ALL;
    isAnonymous.value = ALL;
    hasEvidence.value = ALL;
    sort.value = 'created_at';
    direction.value = 'desc';
    applyFilters();
}

const hasActiveFilters = computed(
    () =>
        search.value !== '' ||
        status.value !== ALL ||
        requestType.value !== ALL ||
        urgencyLevel.value !== ALL ||
        department.value !== ALL ||
        isAnonymous.value !== ALL ||
        hasEvidence.value !== ALL,
);
</script>

<template>
    <Head title="Solicitudes" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div
            class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center"
        >
            <div>
                <h1 class="text-2xl font-semibold">Solicitudes</h1>
                <p class="text-sm text-muted-foreground">
                    Consulta y da seguimiento a todas las solicitudes recibidas.
                </p>
            </div>
        </div>

        <div class="rounded-xl border bg-card p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative sm:col-span-2 lg:col-span-2">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        placeholder="Buscar por folio, descripción, ubicación..."
                        class="pl-9"
                    />
                </div>

                <Select v-model="status">
                    <SelectTrigger class="w-full"
                        ><SelectValue placeholder="Estado"
                    /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL">Todos los estados</SelectItem>
                        <SelectItem
                            v-for="opt in options.statuses"
                            :key="opt.value"
                            :value="opt.value"
                            >{{ opt.label }}</SelectItem
                        >
                    </SelectContent>
                </Select>

                <Select v-model="urgencyLevel">
                    <SelectTrigger class="w-full"
                        ><SelectValue placeholder="Urgencia"
                    /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL">Toda urgencia</SelectItem>
                        <SelectItem
                            v-for="opt in options.urgencyLevels"
                            :key="opt.value"
                            :value="opt.value"
                            >{{ opt.label }}</SelectItem
                        >
                    </SelectContent>
                </Select>

                <Select v-model="requestType">
                    <SelectTrigger class="w-full"
                        ><SelectValue placeholder="Tipo de solicitud"
                    /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL">Todos los tipos</SelectItem>
                        <SelectItem
                            v-for="opt in options.requestTypes"
                            :key="opt.value"
                            :value="opt.value"
                            >{{ opt.label }}</SelectItem
                        >
                    </SelectContent>
                </Select>

                <Select v-model="department">
                    <SelectTrigger class="w-full"
                        ><SelectValue placeholder="Departamento"
                    /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL"
                            >Todos los departamentos</SelectItem
                        >
                        <SelectItem
                            v-for="opt in options.departments"
                            :key="opt.value"
                            :value="opt.value"
                            >{{ opt.label }}</SelectItem
                        >
                    </SelectContent>
                </Select>

                <Select v-model="isAnonymous">
                    <SelectTrigger class="w-full"
                        ><SelectValue placeholder="Anónima"
                    /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL"
                            >Anónimas y no anónimas</SelectItem
                        >
                        <SelectItem value="1">Solo anónimas</SelectItem>
                        <SelectItem value="0">Solo no anónimas</SelectItem>
                    </SelectContent>
                </Select>

                <Select v-model="hasEvidence">
                    <SelectTrigger class="w-full"
                        ><SelectValue placeholder="Evidencia"
                    /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL"
                            >Con y sin evidencia</SelectItem
                        >
                        <SelectItem value="1">Con evidencia</SelectItem>
                        <SelectItem value="0">Sin evidencia</SelectItem>
                    </SelectContent>
                </Select>

                <Button
                    v-if="hasActiveFilters"
                    variant="ghost"
                    class="justify-self-start"
                    @click="clearFilters"
                >
                    <RotateCcw class="size-4" />
                    Limpiar filtros
                </Button>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border bg-card">
            <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead
                                class="cursor-pointer select-none"
                                @click="toggleSort('folio')"
                            >
                                <span class="inline-flex items-center gap-1"
                                    >Folio
                                    <component
                                        :is="sortIcon('folio')"
                                        class="size-3.5"
                                /></span>
                            </TableHead>
                            <TableHead>Tipo</TableHead>
                            <TableHead
                                class="cursor-pointer select-none"
                                @click="toggleSort('department')"
                            >
                                <span class="inline-flex items-center gap-1"
                                    >Departamento
                                    <component
                                        :is="sortIcon('department')"
                                        class="size-3.5"
                                /></span>
                            </TableHead>
                            <TableHead>Ubicación</TableHead>
                            <TableHead
                                class="cursor-pointer select-none"
                                @click="toggleSort('urgency_level')"
                            >
                                <span class="inline-flex items-center gap-1"
                                    >Urgencia
                                    <component
                                        :is="sortIcon('urgency_level')"
                                        class="size-3.5"
                                /></span>
                            </TableHead>
                            <TableHead
                                class="cursor-pointer select-none"
                                @click="toggleSort('status')"
                            >
                                <span class="inline-flex items-center gap-1"
                                    >Estado
                                    <component
                                        :is="sortIcon('status')"
                                        class="size-3.5"
                                /></span>
                            </TableHead>
                            <TableHead>Anónima</TableHead>
                            <TableHead>Evidencia</TableHead>
                            <TableHead
                                class="cursor-pointer select-none"
                                @click="toggleSort('created_at')"
                            >
                                <span class="inline-flex items-center gap-1"
                                    >Fecha
                                    <component
                                        :is="sortIcon('created_at')"
                                        class="size-3.5"
                                /></span>
                            </TableHead>
                            <TableHead class="text-right">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="requests.data.length === 0">
                            <TableCell colspan="10" class="py-16 text-center">
                                <div
                                    class="flex flex-col items-center gap-2 text-muted-foreground"
                                >
                                    <Inbox class="size-8" />
                                    <p class="text-sm">
                                        No se encontraron solicitudes con estos
                                        filtros.
                                    </p>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow
                            v-for="item in requests.data"
                            :key="item.id"
                            class="hover:bg-accent/30"
                        >
                            <TableCell class="font-mono text-xs font-medium">{{
                                item.folio
                            }}</TableCell>
                            <TableCell>{{ item.request_type_label }}</TableCell>
                            <TableCell>{{ item.department_label }}</TableCell>
                            <TableCell class="max-w-40 truncate">{{
                                item.location
                            }}</TableCell>
                            <TableCell
                                ><UrgencyBadge
                                    :level="item.urgency_level ?? ''"
                                    :label="item.urgency_level_label"
                            /></TableCell>
                            <TableCell
                                ><StatusBadge
                                    :status="item.status"
                                    :label="item.status_label"
                            /></TableCell>
                            <TableCell>
                                <span
                                    v-if="item.is_anonymous"
                                    class="text-xs font-medium text-amber-700 dark:text-amber-400"
                                    >Sí</span
                                >
                                <span
                                    v-else
                                    class="text-xs text-muted-foreground"
                                    >No</span
                                >
                            </TableCell>
                            <TableCell>
                                <Paperclip
                                    v-if="item.has_evidence"
                                    class="size-4 text-emerald-600"
                                />
                                <span
                                    v-else
                                    class="text-xs text-muted-foreground"
                                    >—</span
                                >
                            </TableCell>
                            <TableCell class="text-xs text-muted-foreground">
                                {{
                                    new Date(
                                        item.created_at,
                                    ).toLocaleDateString('es-MX', {
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric',
                                    })
                                }}
                            </TableCell>
                            <TableCell class="text-right">
                                <Button as-child size="sm" variant="outline">
                                    <Link :href="solicitudes.show(item.id)">
                                        <Eye class="size-3.5" />
                                        Ver detalle
                                    </Link>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <PaginationBar
                :links="requests.links"
                :from="requests.from"
                :to="requests.to"
                :total="requests.total"
            />
        </div>
    </div>
</template>
