<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    AlertOctagon,
    ClipboardList,
    Eye,
    EyeOff,
    FileSpreadsheet,
    FileText,
    Paperclip,
    RotateCcw,
    Table as TableIcon,
} from '@lucide/vue';
import { ref, watch } from 'vue';
import { Bar, Doughnut } from 'vue-chartjs';
import ChartCard from '@/components/admin/ChartCard.vue';
import PaginationBar from '@/components/admin/PaginationBar.vue';
import StatCard from '@/components/admin/StatCard.vue';
import DatePickerField from '@/components/public/DatePickerField.vue';
import { Button } from '@/components/ui/button';
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
import { chartPalette } from '@/lib/chart';
import { dashboard } from '@/routes';
import reportes from '@/routes/reportes';
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
        urgencyLevels: OptionItem[];
        statuses: OptionItem[];
    };
    summary: {
        total: number;
        criticas: number;
        anonimas: number;
        con_evidencia: number;
    };
    charts: {
        byType: ChartPoint[];
        byUrgency: ChartPoint[];
        byDepartment: ChartPoint[];
        byStatus: ChartPoint[];
    };
    requests: Paginated<RequestSummary>;
}>();

const ALL = 'all';

const dateFrom = ref<string | null>(props.filters.date_from ?? null);
const dateTo = ref<string | null>(props.filters.date_to ?? null);
const status = ref(props.filters.status ?? ALL);
const requestType = ref(props.filters.request_type ?? ALL);
const urgencyLevel = ref(props.filters.urgency_level ?? ALL);
const department = ref(props.filters.department ?? ALL);
const isAnonymous = ref(props.filters.is_anonymous ?? ALL);
const hasEvidence = ref(props.filters.has_evidence ?? ALL);

function currentFilters() {
    return {
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
        status: status.value === ALL ? undefined : status.value,
        request_type: requestType.value === ALL ? undefined : requestType.value,
        urgency_level:
            urgencyLevel.value === ALL ? undefined : urgencyLevel.value,
        department: department.value === ALL ? undefined : department.value,
        is_anonymous: isAnonymous.value === ALL ? undefined : isAnonymous.value,
        has_evidence: hasEvidence.value === ALL ? undefined : hasEvidence.value,
    };
}

function applyFilters() {
    router.get(reportes.index.url(), currentFilters(), {
        preserveState: true,
        replace: true,
    });
}

watch(
    [
        dateFrom,
        dateTo,
        status,
        requestType,
        urgencyLevel,
        department,
        isAnonymous,
        hasEvidence,
    ],
    applyFilters,
);

function clearFilters() {
    dateFrom.value = null;
    dateTo.value = null;
    status.value = ALL;
    requestType.value = ALL;
    urgencyLevel.value = ALL;
    department.value = ALL;
    isAnonymous.value = ALL;
    hasEvidence.value = ALL;
    applyFilters();
}

function exportUrl(base: { url: (options?: RouteQueryOptions) => string }) {
    return base.url({ query: currentFilters() });
}

function barData(points: ChartPoint[]) {
    return {
        labels: points.map((p) => p.label),
        datasets: [
            {
                data: points.map((p) => p.value),
                backgroundColor: chartPalette,
                borderRadius: 6,
                maxBarThickness: 36,
            },
        ],
    };
}

function doughnutData(points: ChartPoint[]) {
    return {
        labels: points.map((p) => p.label),
        datasets: [
            {
                data: points.map((p) => p.value),
                backgroundColor: chartPalette,
                borderWidth: 0,
            },
        ],
    };
}

const baseOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, ticks: { precision: 0 } },
    },
};

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
            labels: { boxWidth: 10, font: { size: 11 } },
        },
    },
};
</script>

<template>
    <Head title="Reportes" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div
            class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center"
        >
            <div>
                <h1 class="text-2xl font-semibold">Reportes</h1>
                <p class="text-sm text-muted-foreground">
                    Analiza las solicitudes filtrando por el criterio que
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
                    <a :href="exportUrl(reportes.export.csv)">
                        <TableIcon class="size-4" /> CSV
                    </a>
                </Button>
                <Button variant="outline" size="sm" as-child>
                    <a :href="exportUrl(reportes.export.pdf)">
                        <FileText class="size-4" /> PDF
                    </a>
                </Button>
            </div>
        </div>

        <div class="rounded-xl border bg-card p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
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
                :icon="ClipboardList"
            />
            <StatCard
                label="Críticas"
                :value="summary.criticas"
                :icon="AlertOctagon"
                tone="red"
            />
            <StatCard
                label="Anónimas"
                :value="summary.anonimas"
                :icon="EyeOff"
            />
            <StatCard
                label="Con evidencia"
                :value="summary.con_evidencia"
                :icon="Paperclip"
            />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <ChartCard title="Solicitudes por tipo">
                <Bar :data="barData(charts.byType)" :options="baseOptions" />
            </ChartCard>
            <ChartCard title="Solicitudes por nivel de urgencia">
                <Doughnut
                    :data="doughnutData(charts.byUrgency)"
                    :options="doughnutOptions"
                />
            </ChartCard>
            <ChartCard title="Solicitudes por departamento">
                <Bar
                    :data="barData(charts.byDepartment)"
                    :options="baseOptions"
                />
            </ChartCard>
            <ChartCard title="Solicitudes por estado">
                <Doughnut
                    :data="doughnutData(charts.byStatus)"
                    :options="doughnutOptions"
                />
            </ChartCard>
        </div>

        <div class="overflow-hidden rounded-xl border bg-card">
            <div class="border-b p-4">
                <h2 class="flex items-center gap-2 text-sm font-semibold">
                    <Eye class="size-4" /> Tabla resumen
                </h2>
            </div>
            <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Folio</TableHead>
                            <TableHead>Tipo</TableHead>
                            <TableHead>Departamento</TableHead>
                            <TableHead>Ubicación</TableHead>
                            <TableHead>Estado</TableHead>
                            <TableHead>Fecha</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="requests.data.length === 0">
                            <TableCell
                                colspan="6"
                                class="py-10 text-center text-sm text-muted-foreground"
                            >
                                No hay resultados para estos filtros.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="item in requests.data" :key="item.id">
                            <TableCell class="font-mono text-xs">{{
                                item.folio
                            }}</TableCell>
                            <TableCell>{{ item.request_type_label }}</TableCell>
                            <TableCell>{{ item.department_label }}</TableCell>
                            <TableCell class="max-w-40 truncate">{{
                                item.location
                            }}</TableCell>
                            <TableCell>{{ item.status_label }}</TableCell>
                            <TableCell class="text-xs text-muted-foreground">
                                {{
                                    new Date(
                                        item.created_at,
                                    ).toLocaleDateString('es-MX')
                                }}
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
