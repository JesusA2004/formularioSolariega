import {
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    DoughnutController,
    Filler,
    Legend,
    LinearScale,
    LineController,
    LineElement,
    PointElement,
    Tooltip,
} from 'chart.js';

ChartJS.register(
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Filler,
    Legend,
    LinearScale,
    LineController,
    LineElement,
    PointElement,
    Tooltip,
);

ChartJS.defaults.font.family =
    'Instrument Sans, ui-sans-serif, system-ui, sans-serif';
ChartJS.defaults.color = 'oklch(0.55 0.01 0)';

export { ChartJS };

export const chartPalette = [
    '#059669',
    '#f59e0b',
    '#0284c7',
    '#ef4444',
    '#8b5cf6',
    '#64748b',
    '#ec4899',
    '#14b8a6',
];
