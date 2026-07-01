<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CalendarClock,
    CheckCircle2,
    Eye,
    Inbox,
    MessagesSquare,
    Paperclip,
} from '@lucide/vue';
import type { ApexChartEventOpts, ApexOptions } from 'apexcharts';
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import ChartCard from '@/components/admin/ChartCard.vue';
import ChartEmptyState from '@/components/admin/ChartEmptyState.vue';
import ChartSkeleton from '@/components/admin/ChartSkeleton.vue';
import EvidenceBadge from '@/components/admin/EvidenceBadge.vue';
import StatCard from '@/components/admin/StatCard.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useMounted } from '@/composables/useMounted';
import { chartPalette } from '@/lib/chart';
import { dashboard } from '@/routes';
import solicitudes from '@/routes/solicitudes';
import type { ChartPoint, RequestSummary } from '@/types/buzon';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});

const props = defineProps<{
    stats: {
        total: number;
        recibido: number;
        en_revision: number;
        atendido: number;
        cerrado: number;
        con_evidencia: number;
        este_mes: number;
    };
    charts: {
        byMonth: ChartPoint[];
        byType: ChartPoint[];
        byDepartment: ChartPoint[];
        byStatus: ChartPoint[];
        byEvidence: ChartPoint[];
    };
    recent: RequestSummary[];
}>();

const isMounted = useMounted();

const statusColors = ['#3B82F6', '#D4AF37', '#22C55E', '#64748B', '#EF4444'];
const evidenceColors = ['#8B5CF6', '#64748B'];

function hasData(points: ChartPoint[]) {
    return points.some((p) => p.value > 0);
}

function goToList(params: Record<string, string>) {
    router.visit(solicitudes.index.url({ query: params }));
}

function makeSelectHandler(points: ChartPoint[], param: string) {
    return (
        _event: unknown,
        _chart: unknown,
        options?: ApexChartEventOpts,
    ) => {
        const point =
            options?.dataPointIndex !== undefined
                ? points[options.dataPointIndex]
                : undefined;

        if (point?.key !== undefined) {
            goToList({ [param]: point.key });
        }
    };
}

const monthSeries = computed(() => [
    { name: 'Mensajes', data: props.charts.byMonth.map((p) => p.value) },
]);

const monthOptions = computed<ApexOptions>(() => ({
    chart: {
        type: 'area',
        toolbar: { show: false },
        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
        events: {
            dataPointSelection: (
                _e: unknown,
                _chart: unknown,
                options?: ApexChartEventOpts,
            ) => {
                const point =
                    options?.dataPointIndex !== undefined
                        ? props.charts.byMonth[options.dataPointIndex]
                        : undefined;

                if (point?.dateFrom && point?.dateTo) {
                    goToList({
                        date_from: point.dateFrom,
                        date_to: point.dateTo,
                    });
                }
            },
        },
    },
    colors: ['#D4AF37'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.35,
            opacityTo: 0.05,
            stops: [0, 90, 100],
        },
    },
    stroke: { curve: 'smooth', width: 3 },
    dataLabels: { enabled: false },
    markers: { size: 4, strokeWidth: 0 },
    xaxis: { categories: props.charts.byMonth.map((p) => p.label) },
    yaxis: { labels: { formatter: (v: number) => Math.round(v).toString() } },
    grid: { strokeDashArray: 4 },
}));

const typeSeries = computed(() => [
    { name: 'Mensajes', data: props.charts.byType.map((p) => p.value) },
]);

const typeOptions = computed<ApexOptions>(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false },
        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
        events: { dataPointSelection: makeSelectHandler(props.charts.byType, 'request_type') },
    },
    plotOptions: { bar: { distributed: true, borderRadius: 6, columnWidth: '55%' } },
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
        events: {
            dataPointSelection: makeSelectHandler(props.charts.byDepartment, 'department'),
        },
    },
    plotOptions: {
        bar: { horizontal: true, distributed: true, borderRadius: 6, barHeight: '60%' },
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
        events: { dataPointSelection: makeSelectHandler(props.charts.byStatus, 'status') },
    },
    labels: props.charts.byStatus.map((p) => p.label),
    colors: statusColors,
    legend: { position: 'bottom' },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: '70%' } } },
}));

const evidenceSeries = computed(() => props.charts.byEvidence.map((p) => p.value));

const evidenceOptions = computed<ApexOptions>(() => ({
    chart: {
        type: 'donut',
        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
        events: {
            dataPointSelection: makeSelectHandler(props.charts.byEvidence, 'has_evidence'),
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
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div
            class="animate-in duration-500 fade-in slide-in-from-bottom-2"
        >
            <h1 class="text-3xl font-semibold">Panel administrativo</h1>
            <p class="text-sm text-muted-foreground">
                Resumen general de mensajes recibidos en Buzón Solariega.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
            <StatCard
                label="Total de mensajes"
                :value="stats.total"
                :icon="MessagesSquare"
                tone="default"
            />
            <StatCard
                label="Recibidos"
                :value="stats.recibido"
                :icon="Inbox"
                tone="blue"
            />
            <StatCard
                label="En revisión"
                :value="stats.en_revision"
                :icon="Eye"
                tone="gold"
            />
            <StatCard
                label="Atendidos"
                :value="stats.atendido"
                :icon="CheckCircle2"
                tone="green"
            />
            <StatCard
                label="Con evidencia"
                :value="stats.con_evidencia"
                :icon="Paperclip"
                tone="purple"
            />
            <StatCard
                label="Este mes"
                :value="stats.este_mes"
                :icon="CalendarClock"
                tone="gray"
            />
        </div>

        <ChartCard
            title="Mensajes por mes"
            hint="Últimos 12 meses — haz clic en un punto para filtrar"
        >
            <ChartSkeleton v-if="!isMounted" />
            <ChartEmptyState
                v-else-if="!hasData(charts.byMonth)"
                message="Aún no se han recibido mensajes en este periodo."
            />
            <VueApexCharts
                v-else
                type="area"
                height="100%"
                :options="monthOptions"
                :series="monthSeries"
            />
        </ChartCard>

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
            class="animate-in overflow-hidden rounded-2xl border bg-card fade-in slide-in-from-bottom-2 duration-500"
        >
            <div class="flex items-center justify-between border-b p-4">
                <h2 class="text-base font-semibold">
                    Últimos mensajes recibidos
                </h2>
                <Link
                    :href="solicitudes.index()"
                    class="text-xs font-medium text-primary hover:underline"
                    >Ver todos</Link
                >
            </div>
            <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Folio</TableHead>
                            <TableHead>Tipo</TableHead>
                            <TableHead>Departamento</TableHead>
                            <TableHead>Estado</TableHead>
                            <TableHead>Evidencia</TableHead>
                            <TableHead>Fecha</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="recent.length === 0">
                            <TableCell
                                colspan="6"
                                class="py-12 text-center text-sm text-muted-foreground"
                            >
                                Aún no se han recibido mensajes.
                            </TableCell>
                        </TableRow>
                        <TableRow
                            v-for="item in recent"
                            :key="item.id"
                            class="transition-colors hover:bg-accent/30"
                        >
                            <TableCell class="font-mono text-xs">
                                <Link
                                    :href="solicitudes.show(item.id)"
                                    class="hover:underline"
                                    >{{ item.folio }}</Link
                                >
                            </TableCell>
                            <TableCell>{{ item.request_type_label }}</TableCell>
                            <TableCell>{{ item.department_label }}</TableCell>
                            <TableCell
                                ><StatusBadge
                                    :status="item.status"
                                    :label="item.status_label"
                            /></TableCell>
                            <TableCell
                                ><EvidenceBadge
                                    :has-evidence="!!item.has_evidence"
                            /></TableCell>
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
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </div>
</template>
