import ApexCharts from 'apexcharts';

let charts = {};

function isDark() {
    return document.documentElement.classList.contains('dark');
}

function getTheme() {
    return isDark() ? 'dark' : 'light';
}

function chartColors() {
    return {
        primary: '#6366f1',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#06b6d4',
        purple: '#8b5cf6',
        pink: '#ec4899',
    };
}

function createSalesTrendChart(canvasId, data) {
    const colors = chartColors();
    return new ApexCharts(document.querySelector(canvasId), {
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false },
            animations: { enabled: true, dynamicAnimation: { speed: 500 } },
            zoom: { enabled: false },
            fontFamily: 'Inter, system-ui, sans-serif',
        },
        series: [{
            name: document.getElementById(canvasId.replace('#', '')).dataset.label || 'Ventas',
            data: data.data,
        }],
        xaxis: {
            categories: data.labels,
            labels: { style: { colors: isDark() ? '#9ca3af' : '#6b7280', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { colors: isDark() ? '#9ca3af' : '#6b7280', fontSize: '11px' },
                formatter: (v) => '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
            },
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2.5 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 100] },
        },
        colors: [colors.primary],
        grid: {
            borderColor: isDark() ? '#374151' : '#e5e7eb',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
        },
        tooltip: {
            theme: getTheme(),
            y: { formatter: (v) => '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 2 }) },
        },
        theme: { mode: getTheme() },
    });
}

function createPaymentMethodChart(canvasId, data) {
    const colors = chartColors();
    return new ApexCharts(document.querySelector(canvasId), {
        chart: {
            type: 'donut',
            height: 300,
            toolbar: { show: false },
            animations: { enabled: true, dynamicAnimation: { speed: 500 } },
            fontFamily: 'Inter, system-ui, sans-serif',
        },
        series: data.data,
        labels: data.labels,
        colors: [colors.primary, colors.success, colors.warning, colors.info],
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom',
            labels: { colors: isDark() ? '#d1d5db' : '#374151' },
            markers: { radius: 4 },
            itemMargin: { horizontal: 12 },
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '62%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: document.getElementById(canvasId.replace('#', '')).dataset.totalLabel || 'Total',
                            formatter: () => {
                                const total = data.data.reduce((a, b) => a + b, 0);
                                return '$' + total.toLocaleString('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                            },
                            color: isDark() ? '#d1d5db' : '#374151',
                            fontSize: '14px',
                            fontWeight: 600,
                        },
                    },
                },
            },
        },
        tooltip: {
            theme: getTheme(),
            y: { formatter: (v) => '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 2 }) },
        },
        responsive: [{ breakpoint: 480, options: { chart: { height: 260 }, legend: { position: 'bottom' } } }],
        theme: { mode: getTheme() },
    });
}

function createTopProductsChart(canvasId, data) {
    const colors = chartColors();
    const reversedLabels = [...data.labels].reverse();
    const reversedData = [...data.data].reverse();
    const barColors = reversedData.map((_, i) => {
        const palette = [colors.primary, colors.purple, colors.info, colors.success, colors.warning, colors.danger, colors.pink, colors.primary, colors.purple, colors.info];
        return palette[i % palette.length];
    });

    return new ApexCharts(document.querySelector(canvasId), {
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false },
            animations: { enabled: true, dynamicAnimation: { speed: 500 } },
            fontFamily: 'Inter, system-ui, sans-serif',
        },
        series: [{
            name: document.getElementById(canvasId.replace('#', '')).dataset.label || 'Cantidad',
            data: reversedData,
        }],
        xaxis: {
            categories: reversedLabels,
            labels: { style: { colors: isDark() ? '#9ca3af' : '#6b7280', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { colors: isDark() ? '#9ca3af' : '#6b7280', fontSize: '11px' },
                formatter: (v) => Number.isInteger(v) ? v : v.toFixed(1),
            },
        },
        dataLabels: { enabled: false },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                distributed: true,
                barHeight: '70%',
            },
        },
        colors: barColors,
        grid: {
            borderColor: isDark() ? '#374151' : '#e5e7eb',
            strokeDashArray: 4,
            xaxis: { lines: { show: true } },
        },
        tooltip: {
            theme: getTheme(),
            y: { formatter: (v) => v + ' ' + (document.getElementById(canvasId.replace('#', '')).dataset.unit || 'uds') },
        },
        legend: { show: false },
        theme: { mode: getTheme() },
    });
}

function createDayOfWeekChart(canvasId, data) {
    const colors = chartColors();
    const dayColors = data.data.map((_, i) => {
        const palette = [colors.danger, colors.warning, colors.info, colors.success, colors.primary, colors.purple, colors.pink];
        return palette[i % palette.length];
    });

    return new ApexCharts(document.querySelector(canvasId), {
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false },
            animations: { enabled: true, dynamicAnimation: { speed: 500 } },
            fontFamily: 'Inter, system-ui, sans-serif',
        },
        series: [{
            name: document.getElementById(canvasId.replace('#', '')).dataset.label || 'Ventas',
            data: data.data,
        }],
        xaxis: {
            categories: data.labels,
            labels: { style: { colors: isDark() ? '#9ca3af' : '#6b7280', fontSize: '12px', fontWeight: 500 } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { colors: isDark() ? '#9ca3af' : '#6b7280', fontSize: '11px' },
                formatter: (v) => '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
            },
        },
        dataLabels: {
            enabled: true,
            style: { colors: [isDark() ? '#f3f4f6' : '#1f2937'], fontSize: '11px', fontWeight: 600 },
            formatter: (v) => '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
            offsetY: -4,
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '55%',
                distributed: true,
                dataLabels: { position: 'top' },
            },
        },
        colors: dayColors,
        grid: {
            borderColor: isDark() ? '#374151' : '#e5e7eb',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
        },
        tooltip: {
            theme: getTheme(),
            y: { formatter: (v) => '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 2 }) },
        },
        legend: { show: false },
        theme: { mode: getTheme() },
    });
}

function updateChart(chart, data, key) {
    if (!chart) return;
    chart.updateSeries([{
        data: data[key].data,
    }]);
    chart.updateOptions({
        xaxis: { categories: data[key].labels },
        theme: { mode: getTheme() },
    });
}

function updateDonutChart(chart, data, key) {
    if (!chart) return;
    chart.updateSeries(data[key].data);
    chart.updateOptions({
        labels: data[key].labels,
        theme: { mode: getTheme() },
    });
}

function updateBarChart(chart, data, key) {
    if (!chart) return;
    const reversedData = [...data[key].data].reverse();
    const reversedLabels = [...data[key].labels].reverse();
    chart.updateSeries([{ data: reversedData }]);
    chart.updateOptions({
        xaxis: { categories: reversedLabels },
        theme: { mode: getTheme() },
    });
}

function refreshAllCharts() {
    fetch('/dashboard/chart-data')
        .then(r => r.json())
        .then(data => {
            if (!data || !data.dailySales) return;
            updateChart(charts.salesTrend, data, 'dailySales');
            updateDonutChart(charts.paymentMethod, data, 'paymentMethods');
            updateBarChart(charts.topProducts, data, 'topProducts');
            updateChart(charts.dayOfWeek, data, 'dayOfWeek');
        })
        .catch(() => {});
}

export function initDashboardCharts() {
    const containers = document.querySelectorAll('[data-chart]');
    if (!containers.length) return;

    fetch('/dashboard/chart-data')
        .then(r => r.json())
        .then(data => {
            if (!data || !data.dailySales) return;

            charts.salesTrend = createSalesTrendChart('#salesTrendChart', data.dailySales);
            charts.paymentMethod = createPaymentMethodChart('#paymentMethodChart', data.paymentMethods);
            charts.topProducts = createTopProductsChart('#topProductsChart', data.topProducts);
            charts.dayOfWeek = createDayOfWeekChart('#dayOfWeekChart', data.dayOfWeek);

            charts.salesTrend.render();
            charts.paymentMethod.render();
            charts.topProducts.render();
            charts.dayOfWeek.render();

            setInterval(refreshAllCharts, 30000);
        })
        .catch(() => {});

    // Listen for dark mode changes
    const observer = new MutationObserver(() => {
        const mode = getTheme();
        Object.values(charts).forEach(chart => {
            if (chart) chart.updateOptions({ theme: { mode } });
        });
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
}