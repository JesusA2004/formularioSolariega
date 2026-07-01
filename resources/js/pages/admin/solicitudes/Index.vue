<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowDown,
    ArrowUp,
    ArrowUpDown,
    Eye,
    Inbox,
    RotateCcw,
    Search,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import EvidenceBadge from '@/components/admin/EvidenceBadge.vue';
import PaginationBar from '@/components/admin/PaginationBar.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import DatePickerField from '@/components/public/DatePickerField.vue';
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
            { title: 'Mensajes', href: solicitudes.index() },
        ],
    },
});

const props = defineProps<{
    requests: Paginated<RequestSummary>;
    filters: Record<string, string | undefined>;
    options: {
        requestTypes: OptionItem[];
        departments: OptionItem[];
        statuses: OptionItem[];
    };
}>();

const ALL = 'all';

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? ALL);
const requestType = ref(props.filters.request_type ?? ALL);
const department = ref(props.filters.department ?? ALL);
const hasEvidence = ref(props.filters.has_evidence ?? ALL);
const dateFrom = ref<string | null>(props.filters.date_from ?? null);
const dateTo = ref<string | null>(props.filters.date_to ?? null);
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
            department: department.value === ALL ? undefined : department.value,
            has_evidence:
                hasEvidence.value === ALL ? undefined : hasEvidence.value,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
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
    [status, requestType, department, hasEvidence, dateFrom, dateTo],
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
    department.value = ALL;
    hasEvidence.value = ALL;
    dateFrom.value = null;
    dateTo.value = null;
    sort.value = 'created_at';
    direction.value = 'desc';
    applyFilters();
}

const hasActiveFilters = computed(
    () =>
        search.value !== '' ||
        status.value !== ALL ||
        requestType.value !== ALL ||
        department.value !== ALL ||
        hasEvidence.value !== ALL ||
        !!dateFrom.value ||
        !!dateTo.value,
);
</script>

<template>
    <Head title="Mensajes" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="animate-in duration-500 fade-in slide-in-from-bottom-2">
            <h1 class="text-3xl font-semibold">Mensajes</h1>
            <p class="text-sm text-muted-foreground">
                Consulta y da seguimiento a todos los mensajes recibidos.
            </p>
        </div>

        <div
            class="animate-in rounded-2xl border bg-card p-4 duration-500 fade-in slide-in-from-bottom-2"
        >
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative sm:col-span-2 lg:col-span-2">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        placeholder="Buscar por nombre, contacto, folio, descripción..."
                        class="pl-9 transition-colors hover:border-gold/50"
                    />
                </div>

                <Select v-model="status">
                    <SelectTrigger
                        class="w-full transition-colors hover:border-gold/50"
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

                <Select v-model="requestType">
                    <SelectTrigger
                        class="w-full transition-colors hover:border-gold/50"
                        ><SelectValue placeholder="Tipo de mensaje"
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
                    <SelectTrigger
                        class="w-full transition-colors hover:border-gold/50"
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

                <Select v-model="hasEvidence">
                    <SelectTrigger
                        class="w-full transition-colors hover:border-gold/50"
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

                <DatePickerField v-model="dateFrom" placeholder="Desde" />
                <DatePickerField v-model="dateTo" placeholder="Hasta" />

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

        <div
            class="animate-in overflow-hidden rounded-2xl border bg-card duration-500 fade-in slide-in-from-bottom-2"
        >
            <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead
                                class="cursor-pointer select-none"
                                @click="toggleSort('created_at')"
                            >
                                <span class="inline-flex items-center gap-1"
                                    >Fecha de envío
                                    <component
                                        :is="sortIcon('created_at')"
                                        class="size-3.5"
                                /></span>
                            </TableHead>
                            <TableHead>Nombre</TableHead>
                            <TableHead>Contacto</TableHead>
                            <TableHead
                                class="cursor-pointer select-none"
                                @click="toggleSort('department')"
                            >
                                <span class="inline-flex items-center gap-1"
                                    >Área / Departamento
                                    <component
                                        :is="sortIcon('department')"
                                        class="size-3.5"
                                /></span>
                            </TableHead>
                            <TableHead>Tipo de mensaje</TableHead>
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
                            <TableHead>Evidencia</TableHead>
                            <TableHead class="text-right">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="requests.data.length === 0">
                            <TableCell colspan="8" class="py-16 text-center">
                                <div
                                    class="flex flex-col items-center gap-2 text-muted-foreground"
                                >
                                    <Inbox class="size-8" />
                                    <p class="text-sm">
                                        No se encontraron mensajes con estos
                                        filtros.
                                    </p>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow
                            v-for="item in requests.data"
                            :key="item.id"
                            class="transition-colors hover:bg-accent/30"
                        >
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
                            <TableCell>
                                <p class="font-medium">
                                    {{ item.full_name ?? '—' }}
                                </p>
                                <p
                                    class="font-mono text-[11px] text-muted-foreground"
                                >
                                    {{ item.folio }}
                                </p>
                            </TableCell>
                            <TableCell class="text-muted-foreground">{{
                                item.contact_info ?? '—'
                            }}</TableCell>
                            <TableCell>{{ item.department_label }}</TableCell>
                            <TableCell>{{ item.request_type_label }}</TableCell>
                            <TableCell
                                ><StatusBadge
                                    :status="item.status"
                                    :label="item.status_label"
                            /></TableCell>
                            <TableCell
                                ><EvidenceBadge
                                    :has-evidence="!!item.has_evidence"
                            /></TableCell>
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
