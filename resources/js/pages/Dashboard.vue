<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertOctagon,
    CheckCircle2,
    ClipboardList,
    Eye,
    EyeOff,
    FolderCheck,
    Inbox,
    Paperclip,
} from '@lucide/vue';
import { Bar, Doughnut, Line } from 'vue-chartjs';
import ChartCard from '@/components/admin/ChartCard.vue';
import StatCard from '@/components/admin/StatCard.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import UrgencyBadge from '@/components/admin/UrgencyBadge.vue';
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
        criticas: number;
        anonimas: number;
        con_evidencia: number;
    };
    charts: {
        byType: ChartPoint[];
        byUrgency: ChartPoint[];
        byDepartment: ChartPoint[];
        byStatus: ChartPoint[];
        byMonth: ChartPoint[];
    };
    recent: RequestSummary[];
}>();

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

const lineData = {
    labels: props.charts.byMonth.map((p) => p.label),
    datasets: [
        {
            label: 'Solicitudes',
            data: props.charts.byMonth.map((p) => p.value),
            borderColor: '#059669',
            backgroundColor: 'rgba(5, 150, 105, 0.12)',
            fill: true,
            tension: 0.35,
            pointRadius: 3,
        },
    ],
};

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
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-semibold">Dashboard</h1>
            <p class="text-sm text-muted-foreground">
                Resumen general del buzón de quejas, sugerencias y reportes.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <StatCard
                label="Total de solicitudes"
                :value="stats.total"
                :icon="ClipboardList"
                tone="default"
            />
            <StatCard
                label="Recibidas"
                :value="stats.recibido"
                :icon="Inbox"
                tone="blue"
            />
            <StatCard
                label="En revisión"
                :value="stats.en_revision"
                :icon="Eye"
                tone="amber"
            />
            <StatCard
                label="Atendidas"
                :value="stats.atendido"
                :icon="CheckCircle2"
                tone="emerald"
            />
            <StatCard
                label="Cerradas"
                :value="stats.cerrado"
                :icon="FolderCheck"
                tone="default"
            />
            <StatCard
                label="Críticas"
                :value="stats.criticas"
                :icon="AlertOctagon"
                tone="red"
            />
            <StatCard
                label="Anónimas"
                :value="stats.anonimas"
                :icon="EyeOff"
                tone="default"
            />
            <StatCard
                label="Con evidencia"
                :value="stats.con_evidencia"
                :icon="Paperclip"
                tone="default"
            />
        </div>

        <ChartCard title="Solicitudes por mes">
            <Line :data="lineData" :options="baseOptions" />
        </ChartCard>

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

        <div class="rounded-xl border bg-card">
            <div class="flex items-center justify-between border-b p-4">
                <h2 class="text-sm font-semibold">
                    Últimas solicitudes recibidas
                </h2>
                <Link
                    :href="solicitudes.index()"
                    class="text-xs font-medium text-primary hover:underline"
                    >Ver todas</Link
                >
            </div>
            <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Folio</TableHead>
                            <TableHead>Tipo</TableHead>
                            <TableHead>Departamento</TableHead>
                            <TableHead>Urgencia</TableHead>
                            <TableHead>Estado</TableHead>
                            <TableHead>Fecha</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="recent.length === 0">
                            <TableCell
                                colspan="6"
                                class="py-8 text-center text-sm text-muted-foreground"
                            >
                                Aún no hay solicitudes registradas.
                            </TableCell>
                        </TableRow>
                        <TableRow
                            v-for="item in recent"
                            :key="item.id"
                            class="cursor-pointer"
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
                                ><UrgencyBadge
                                    :level="item.urgency_level ?? ''"
                                    :label="item.urgency_level_label"
                            /></TableCell>
                            <TableCell
                                ><StatusBadge
                                    :status="item.status"
                                    :label="item.status_label"
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
