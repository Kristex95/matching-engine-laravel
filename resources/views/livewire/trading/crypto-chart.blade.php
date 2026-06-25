<div class="w-full bg-neutral-900 border border-neutral-800 rounded-lg p-4">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4 select-none">
        <div class="flex gap-2">
            <button wire:click="changeInterval('1m')"
                class="px-3 py-1 text-xs rounded {{ $interval === '1m' ? 'bg-emerald-600 text-white' : 'bg-neutral-800 text-neutral-400' }}">1M</button>
            <button wire:click="changeInterval('5m')"
                class="px-3 py-1 text-xs rounded {{ $interval === '5m' ? 'bg-emerald-600 text-white' : 'bg-neutral-800 text-neutral-400' }}">5M</button>
            <button wire:click="changeInterval('1h')"
                class="px-3 py-1 text-xs rounded {{ $interval === '1h' ? 'bg-emerald-600 text-white' : 'bg-neutral-800 text-neutral-400' }}">1H</button>
            <button wire:click="changeInterval('1d')"
                class="px-3 py-1 text-xs rounded {{ $interval === '1d' ? 'bg-emerald-600 text-white' : 'bg-neutral-800 text-neutral-400' }}">1D</button>
        </div>

        <div id="ohlcDisplayPanel" class="flex gap-4 text-xs font-mono text-neutral-400">
            <span class="text-neutral-500">TIME: <b id="ohlcTime" class="text-neutral-200">-</b></span>
            <span>O: <b id="ohlcOpen" class="text-neutral-200">-</b></span>
            <span>H: <b id="ohlcHigh" class="text-neutral-200">-</b></span>
            <span>L: <b id="ohlcLow" class="text-neutral-200">-</b></span>
            <span>C: <b id="ohlcClose" class="text-neutral-200">-</b></span>
        </div>
    </div>

    <div wire:ignore style="height: 400px; position: relative;">
        <canvas id="cryptoChartCanvas"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-financial@0.2.0/dist/chartjs-chart-financial.min.js"></script>

    <script>
        document.addEventListener('livewire:init', () => {
            let chartInstance = null;
            const ctx = document.getElementById('cryptoChartCanvas').getContext('2d');

            const elTime = document.getElementById('ohlcTime');
            const elOpen = document.getElementById('ohlcOpen');
            const elHigh = document.getElementById('ohlcHigh');
            const elLow = document.getElementById('ohlcLow');
            const elClose = document.getElementById('ohlcClose');

            function formatPrice(val) {
                if (val === undefined || val === null) return '-';
                return Number(val).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function renderChart(chartData, chartUnit) {
                if (chartInstance) {
                    chartInstance.destroy();
                }

                if (!chartData || chartData.length === 0) {
                    console.warn("No chart data available.");
                    return;
                }

                const lows = chartData.map(d => d.l);
                const highs = chartData.map(d => d.h);
                const minPrice = Math.min(...lows);
                const maxPrice = Math.max(...highs);
                const priceBuffer = (maxPrice - minPrice) * 0.05 || minPrice * 0.05;
                const yMin = Math.max(0, minPrice - priceBuffer);

                const displayFormats = {
                    minute: 'HH:mm',
                    hour: 'MMM d, HH:mm',
                    day: 'MMM d'
                };

                elTime.textContent = elOpen.textContent = elHigh.textContent = elLow.textContent = elClose
                    .textContent = '-';

                chartInstance = new Chart(ctx, {
                    type: 'candlestick',
                    data: {
                        datasets: [{
                            label: 'Market Price',
                            data: chartData,
                            borderWidth: 1.5,
                            hoverBorderWidth: 2.5,
                            borderColor: {
                                up: '#26a69a',
                                down: '#ef5350',
                                unchanged: '#999999'
                            },
                            backgroundColors: {
                                up: '#26a69a',
                                down: '#ef5350',
                                unchanged: '#999999'
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            tooltip: {
                                enabled: false
                            }
                        },
                        onHover: (event, activeElements, chart) => {
                            const points = chart.getElementsAtEventForMode(
                                event['native'] || event,
                                'index', {
                                    intersect: false
                                },
                                true
                            );

                            if (points && points.length > 0) {
                                const index = points[0].index;
                                const rawPoint = chart.data.datasets[0].data[index];

                                if (rawPoint) {
                                    const dateObj = new Date(rawPoint.x);
                                    const timeStr = chartUnit === 'day' ?
                                        dateObj.toLocaleDateString(undefined, {
                                            month: 'short',
                                            day: 'numeric'
                                        }) :
                                        dateObj.toLocaleTimeString(undefined, {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            hour12: false
                                        });

                                    elTime.textContent = timeStr;
                                    elOpen.textContent = formatPrice(rawPoint.o);
                                    elHigh.textContent = formatPrice(rawPoint.h);
                                    elLow.textContent = formatPrice(rawPoint.l);
                                    elClose.textContent = formatPrice(rawPoint.c);

                                    const isUp = rawPoint.c >= rawPoint.o;
                                    const panelColor = isUp ? '#26a69a' : '#ef5350';
                                    elOpen.style.color = elHigh.style.color = elLow.style.color =
                                        elClose.style.color = panelColor;
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'time',
                                distribution: 'series',
                                time: {
                                    unit: chartUnit,
                                    displayFormats: displayFormats
                                },
                                ticks: {
                                    color: '#a3a3a3',
                                    source: 'auto',
                                    autoSkip: true,
                                    maxTicksLimit: 10,
                                    maxRotation: 0,
                                    minRotation: 0,
                                    padding: 10
                                },
                                grid: {
                                    color: '#262626'
                                }
                            },
                            y: {
                                position: 'right',
                                min: yMin,
                                max: maxPrice + priceBuffer,
                                ticks: {
                                    color: '#a3a3a3'
                                },
                                grid: {
                                    color: '#262626'
                                }
                            }
                        }
                    }
                });
            }

            renderChart(@json($initialData), @json($initialUnit));

            Livewire.on('chart-updated', (event) => {
                const data = event[0] ? event[0] : event;
                renderChart(data.chartData, data.chartUnit);
            });
        });
    </script>
</div>
