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
    '#283B2A',
    '#C6A45A',
    '#9A6242',
    '#6B4A2F',
    '#A23E2E',
    '#10251B',
    '#D9BF7A',
    '#8a8574',
];
