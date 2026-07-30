/**
 * ChartManager — mounts Chart.js on [data-chart] cards.
 * Dark theme defaults aligned with panel / admin chrome.
 */

import {
    Chart,
    LineController,
    BarController,
    DoughnutController,
    PieController,
    PolarAreaController,
    RadarController,
    CategoryScale,
    LinearScale,
    RadialLinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Filler,
    Legend,
    Tooltip,
} from 'chart.js';

Chart.register(
    LineController,
    BarController,
    DoughnutController,
    PieController,
    PolarAreaController,
    RadarController,
    CategoryScale,
    LinearScale,
    RadialLinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Filler,
    Legend,
    Tooltip,
);

type ChartConfigPayload = {
    type: string;
    labels: string[];
    datasets: Array<{
        label: string;
        data: number[];
        color?: string;
        fill?: boolean | string;
        tension?: number;
    }>;
    legend?: boolean;
    options?: Record<string, unknown>;
};

const SERIES: Record<string, string> = {
    'series-1': '#e44545',
    'series-2': '#5ec8c0',
    'series-3': '#e8b923',
    'series-4': '#7aa2ff',
};

function readCssColor(token: string, fallback: string): string {
    if (token.startsWith('#') || token.startsWith('rgb') || token.startsWith('hsl')) {
        return token;
    }
    if (SERIES[token]) return SERIES[token];
    const fromDoc = getComputedStyle(document.documentElement).getPropertyValue(token).trim();
    return fromDoc || fallback;
}

function resolveColor(raw: string | undefined, index: number): string {
    const key = raw || `series-${(index % 4) + 1}`;
    return readCssColor(key, SERIES[`series-${(index % 4) + 1}`]);
}

function withAlpha(hex: string, alpha: number): string {
    if (!hex.startsWith('#') || (hex.length !== 7 && hex.length !== 4)) {
        return hex;
    }
    const full =
        hex.length === 4
            ? `#${hex[1]}${hex[1]}${hex[2]}${hex[2]}${hex[3]}${hex[3]}`
            : hex;
    const r = parseInt(full.slice(1, 3), 16);
    const g = parseInt(full.slice(3, 5), 16);
    const b = parseInt(full.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

export class ChartManager {
    private charts = new Map<string, Chart>();

    constructor() {
        document.querySelectorAll<HTMLElement>('[data-chart]').forEach((root) => this.mount(root));
    }

    mount(root: HTMLElement): Chart | null {
        const canvas = root.querySelector<HTMLCanvasElement>('[data-chart-canvas]');
        const cfgNode = root.querySelector<HTMLScriptElement>('[data-chart-config]');
        if (!canvas || !cfgNode) return null;

        let payload: ChartConfigPayload;
        try {
            payload = JSON.parse(cfgNode.textContent || '{}') as ChartConfigPayload;
        } catch {
            return null;
        }

        const id = root.id || `chart-${Math.random().toString(36).slice(2, 8)}`;
        if (!root.id) root.id = id;

        // Destroy previous instance if remounting
        this.charts.get(id)?.destroy();

        const isRadial = ['doughnut', 'pie', 'polarArea', 'radar'].includes(payload.type);
        const datasets = (payload.datasets || []).map((ds, i) => {
            const color = resolveColor(ds.color, i);
            const base: Record<string, unknown> = {
                label: ds.label,
                data: ds.data,
                borderColor: color,
                backgroundColor: isRadial
                    ? (payload.labels || []).map((_, li) =>
                          resolveColor(payload.datasets[li]?.color, li),
                      )
                    : withAlpha(color, payload.type === 'bar' ? 0.75 : 0.18),
                borderWidth: isRadial ? 0 : 2,
                pointBackgroundColor: color,
                pointBorderColor: '#111',
                pointRadius: payload.type === 'line' ? 3 : 0,
                pointHoverRadius: 5,
            };
            if (ds.fill != null) base.fill = ds.fill;
            if (ds.tension != null) base.tension = ds.tension;
            if (payload.type === 'bar') {
                base.borderRadius = 6;
                base.borderSkipped = false;
            }
            return base;
        });

        // For doughnut/pie use per-slice colors from first dataset values count
        if (isRadial && datasets[0]) {
            datasets[0].backgroundColor = (payload.labels || []).map((_, i) =>
                resolveColor(undefined, i),
            );
            datasets[0].borderWidth = 0;
        }

        const chart = new Chart(canvas, {
            type: payload.type as 'line',
            data: {
                labels: payload.labels || [],
                datasets: datasets as never,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: payload.legend !== false,
                        position: isRadial ? 'bottom' : 'top',
                        align: 'end',
                        labels: {
                            color: 'rgba(255,255,255,0.72)',
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 14,
                            font: { size: 11, weight: 600 },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleColor: '#fff',
                        bodyColor: 'rgba(255,255,255,0.85)',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: true,
                        callbacks: {},
                    },
                },
                scales: isRadial
                    ? undefined
                    : {
                          x: {
                              grid: { color: 'rgba(255,255,255,0.06)' },
                              ticks: { color: 'rgba(255,255,255,0.48)', font: { size: 11 } },
                              border: { display: false },
                          },
                          y: {
                              grid: { color: 'rgba(255,255,255,0.06)' },
                              ticks: { color: 'rgba(255,255,255,0.48)', font: { size: 11 } },
                              border: { display: false },
                              beginAtZero: true,
                          },
                      },
                ...(payload.options || {}),
            },
        });

        this.charts.set(id, chart);
        return chart;
    }

    destroy(id: string): void {
        this.charts.get(id)?.destroy();
        this.charts.delete(id);
    }

    get(id: string): Chart | undefined {
        return this.charts.get(id);
    }
}
