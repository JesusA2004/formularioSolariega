<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    CalendarClock,
    Eye,
    FileSpreadsheet,
    FileText,
    FolderCheck,
    MessagesSquare,
    Paperclip,
    RotateCcw,
    Search,
} from '@lucide/vue';
import type { ApexChartEventOpts, ApexOptions } from 'apexcharts';
import { computed, ref, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import ChartCard from '@/components/admin/ChartCard.vue';
import ChartEmptyState from '@/components/admin/ChartEmptyState.vue';
import ChartSkeleton from '@/components/admin/ChartSkeleton.vue';
import EvidenceBadge from '@/components/admin/EvidenceBadge.vue';
import PaginationBar from '@/components/admin/PaginationBar.vue';
import StatCard from '@/components/admin/StatCard.vue';
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
import { useChartTheme } from '@/composables/useChartTheme';
import { useMounted } from '@/composables/useMounted';
import { chartPalette } from '@/lib/chart';
import { dashboard } from '@/routes';
import reportes from '@/routes/reportes';
import solicitudes from '@/routes/solicitudes';
import type {
    ChartPoint,
    OptionItem,
    Paginated,
    RequestSummary,
} from '@/types/buzon';
import type { RouteQueryOptions } from '@/wayfinder';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Reportes', href: reportes.index() },
        ],
    },
});

const props = defineProps<{
    filters: Record<string, string | undefined>;
    options: {
        requestTypes: OptionItem[];
        departments: OptionItem[];
        statuses: OptionItem[];
    };
    summary: {
        total: number;
        pendientes: number;
        con_evidencia: number;
        cerrados: number;
    };
    charts: {
        byType: ChartPoint[];
        byDepartment: ChartPoint[];
        byStatus: ChartPoint[];
        byEvidence: ChartPoint[];
    };
    requests: Paginated<RequestSummary>;
}>();

const isMounted = useMounted();
const { foreColor } = useChartTheme();

const ALL = 'all';

const search = ref(props.filters.search ?? '');
const dateFrom = ref<string | null>(props.filters.date_from ?? null);
const dateTo = ref<string | null>(props.filters.date_to ?? null);
const status = ref(props.filters.status ?? ALL);
const requestType = ref(props.filters.request_type ?? ALL);
const department = ref(props.filters.department ?? ALL);
const hasEvidence = ref(props.filters.has_evidence ?? ALL);

let debounceTimer: ReturnType<typeof setTimeout> | undefined;

function currentFilters() {
    return {
        search: search.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
        status: status.value === ALL ? undefined : status.value,
        request_type: requestType.value === ALL ? undefined : requestType.value,
        department: department.value === ALL ? undefined : department.value,
        has_evidence: hasEvidence.value === ALL ? undefined : hasEvidence.value,
    };
}

function applyFilters() {
    router.get(reportes.index.url(), currentFilters(), {
        preserveState: true,
        replace: true,
    });
}

watch(search, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 350);
});

watch(
    [dateFrom, dateTo, status, requestType, department, hasEvidence],
    applyFilters,
);

function clearFilters() {
    search.value = '';
    dateFrom.value = null;
    dateTo.value = null;
    status.value = ALL;
    requestType.value = ALL;
    department.value = ALL;
    hasEvidence.value = ALL;
    applyFilters();
}

function exportUrl(base: { url: (options?: RouteQueryOptions) => string }) {
    return base.url({ query: currentFilters() });
}

function hasData(points: ChartPoint[]) {
    return points.some((p) => p.value > 0);
}

function goToList(params: Record<string, string>) {
    router.visit(solicitudes.index.url({ query: params }));
}

function makeSelectHandler(points: ChartPoint[], param: string) {
    return (_event: unknown, _chart: unknown, options?: ApexChartEventOpts) => {
        const point =
            options?.dataPointIndex !== undefined
                ? points[options.dataPointIndex]
                : undefined;

        if (point?.key !== undefined) {
            goToList({ [param]: point.key });
        }
    };
}

const statusColors = ['#3B82F6', '#D4AF37', '#22C55E', '#64748B', '#EF4444'];
const evidenceColors = ['#8B5CF6', '#64748B'];

const typeSeries = computed(() => [
    { name: 'Mensajes', data: props.charts.byType.map((p) => p.value) },
]);

const typeOptions = computed<ApexOptions>(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false },
        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
        foreColor: foreColor.value,
        events: {
            dataPointSelection: makeSelectHandler(
                props.charts.byType,
                'request_type',
            ),
        },
    },
    plotOptions: {
        bar: { distributed: true, borderRadius: 6, columnWidth: '55%' },
    },
    colors: chartPalette,
    legend: { show: false },
    dataLabels: { enabled: false },
    xaxis: { categories: props.charts.byType.map((p) => p.label) },
}));

const departmentSeries = computed(() => [
    { name: 'Mensajes', data: props.charts.byDepartment.map((p) => p.value) },
]);

const departmentOptions = computed<ApexOptions>(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false },
        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
        foreColor: foreColor.value,
        events: {
            dataPointSelection: makeSelectHandler(
                props.charts.byDepartment,
                'department',
            ),
        },
    },
    plotOptions: {
        bar: {
            horizontal: true,
            distributed: true,
            borderRadius: 6,
            barHeight: '60%',
        },
    },
    colors: chartPalette,
    legend: { show: false },
    dataLabels: { enabled: false },
    xaxis: { categories: props.charts.byDepartment.map((p) => p.label) },
}));

const statusSeries = computed(() => props.charts.byStatus.map((p) => p.value));

const statusOptions = computed<ApexOptions>(() => ({
    chart: {
        type: 'donut',
        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
        foreColor: foreColor.value,
        events: {
            dataPointSelection: makeSelectHandler(
                props.charts.byStatus,
                'status',
            ),
        },
    },
    labels: props.charts.byStatus.map((p) => p.label),
    colors: statusColors,
    legend: { position: 'bottom' },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: '70%' } } },
}));

const evidenceSeries = computed(() =>
    props.charts.byEvidence.map((p) => p.value),
);

const evidenceOptions = computed<ApexOptions>(() => ({
    chart: {
        type: 'donut',
        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
        foreColor: foreColor.value,
        events: {
            dataPointSelection: makeSelectHandler(
                props.charts.byEvidence,
                'has_evidence',
            ),
        },
    },
    labels: props.charts.byEvidence.map((p) => p.label),
    colors: evidenceColors,
    legend: { position: 'bottom' },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: '70%' } } },
}));
</script>

<template>
    <Head title="Reportes" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div
            class="flex animate-in flex-col justify-between gap-2 duration-500 fade-in slide-in-from-bottom-2 sm:flex-row sm:items-center"
        >
            <div>
                <h1 class="text-3xl font-semibold">Reportes</h1>
                <p class="text-sm text-muted-foreground">
                    Analiza los mensajes filtrando por el criterio que
                    necesites.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <Button variant="outline" size="sm" as-child>
                    <a :href="exportUrl(reportes.export.excel)">
                        <FileSpreadsheet class="size-4" /> Excel
                    </a>
                </Button>
                <Button variant="outline" size="sm" as-child>
                    <a :href="exportUrl(reportes.export.pdf)">
                        <FileText class="size-4" /> PDF
                    </a>
                </Button>
            </div>
        </div>

        <div
            class="animate-in rounded-2xl border bg-card p-4 duration-500 fade-in slide-in-from-bottom-2"
        >
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative self-start sm:col-span-2">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        placeholder="Buscar por nombre, contacto, folio, descripción..."
                        class="pl-9"
                    />
                </div>

                <div class="space-y-1.5">
                    <p class="text-xs font-medium text-muted-foreground">
                        Desde
                    </p>
                    <DatePickerField
                        v-model="dateFrom"
                        placeholder="Fecha inicial"
                    />
                </div>
                <div class="space-y-1.5">
                    <p class="text-xs font-medium text-muted-foreground">
                        Hasta
                    </p>
                    <DatePickerField
                        v-model="dateTo"
                        placeholder="Fecha final"
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

                <Select v-model="requestType">
                    <SelectTrigger class="w-full"
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
                    variant="ghost"
                    class="justify-self-start"
                    @click="clearFilters"
                >
                    <RotateCcw class="size-4" />
                    Limpiar filtros
                </Button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <StatCard
                label="Total en el filtro"
                :value="summary.total"
                :icon="MessagesSquare"
            />
            <StatCard
                label="Pendientes"
                :value="summary.pendientes"
                :icon="CalendarClock"
                tone="gold"
            />
            <StatCard
                label="Con evidencia"
                :value="summary.con_evidencia"
                :icon="Paperclip"
                tone="purple"
            />
            <StatCard
                label="Cerrados"
                :value="summary.cerrados"
                :icon="FolderCheck"
                tone="gray"
            />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <ChartCard title="Mensajes por tipo">
                <ChartSkeleton v-if="!isMounted" />
                <ChartEmptyState v-else-if="!hasData(charts.byType)" />
                <VueApexCharts
                    v-else
                    type="bar"
                    height="100%"
                    :options="typeOptions"
                    :series="typeSeries"
                />
            </ChartCard>
            <ChartCard title="Mensajes por departamento">
                <ChartSkeleton v-if="!isMounted" />
                <ChartEmptyState v-else-if="!hasData(charts.byDepartment)" />
                <VueApexCharts
                    v-else
                    type="bar"
                    height="100%"
                    :options="departmentOptions"
                    :series="departmentSeries"
                />
            </ChartCard>
            <ChartCard title="Mensajes por estado">
                <ChartSkeleton v-if="!isMounted" />
                <ChartEmptyState v-else-if="!hasData(charts.byStatus)" />
                <VueApexCharts
                    v-else
                    type="donut"
                    height="100%"
                    :options="statusOptions"
                    :series="statusSeries"
                />
            </ChartCard>
            <ChartCard title="Evidencia">
                <ChartSkeleton v-if="!isMounted" />
                <ChartEmptyState v-else-if="!hasData(charts.byEvidence)" />
                <VueApexCharts
                    v-else
                    type="donut"
                    height="100%"
                    :options="evidenceOptions"
                    :series="evidenceSeries"
                />
            </ChartCard>
        </div>

        <div
            class="animate-in overflow-hidden rounded-2xl border bg-card duration-500 fade-in slide-in-from-bottom-2"
        >
            <div class="border-b p-4">
                <h2 class="flex items-center gap-2 text-sm font-semibold">
                    <Eye class="size-4" /> Tabla resumen
                </h2>
            </div>
            <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Fecha de envío</TableHead>
                            <TableHead>Nombre</TableHead>
                            <TableHead>Contacto</TableHead>
                            <TableHead>Departamento</TableHead>
                            <TableHead>Tipo</TableHead>
                            <TableHead>Estado</TableHead>
                            <TableHead>Evidencia</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="requests.data.length === 0">
                            <TableCell
                                colspan="7"
                                class="py-10 text-center text-sm text-muted-foreground"
                            >
                                No hay resultados para estos filtros.
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
                                    ).toLocaleDateString('es-MX')
                                }}
                            </TableCell>
                            <TableCell class="font-medium">{{
                                item.full_name ?? '—'
                            }}</TableCell>
                            <TableCell class="text-muted-foreground">{{
                                item.contact_info ?? '—'
                            }}</TableCell>
                            <TableCell>{{ item.department_label }}</TableCell>
                            <TableCell>{{ item.request_type_label }}</TableCell>
                            <TableCell>{{ item.status_label }}</TableCell>
                            <TableCell
                                ><EvidenceBadge
                                    :has-evidence="!!item.has_evidence"
                            /></TableCell>
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
