<x-dashboard-layout>
    <div class="flex gap-4 h-[90vh] mt-4">
        <!-- Left Column: Cards + Chart -->
        <div class="flex-1 flex flex-col gap-y-6 overflow-y-auto">
            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($stats as $stat)
                <div class="flex flex-col gap-y-3 p-5 bg-white border border-gray-200 shadow-xl rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="inline-flex items-center gap-2">
                        <span class="size-2 inline-block bg-{{ $stat['color'] }}-500 rounded-full"></span>
                        <span class="text-xs font-semibold uppercase text-gray-600 dark:text-neutral-400">
                            {{ $stat['label'] }}
                        </span>
                    </div>

                    <div class="text-center mt-2">
                        <h3 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-gray-800 dark:text-neutral-200">
                            {{ $stat['total'] }}
                        </h3>
                    </div>

                    <dl class="flex justify-between items-center divide-x divide-gray-200 dark:divide-neutral-800 mt-2">
                        <dt class="pe-3 flex items-center gap-1">
                            <span class="text-{{ $stat['is_up'] ? 'green' : 'red' }}-600 flex items-center gap-1">
                                @if ($stat['is_up'])
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z" />
                                </svg>
                                @else
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z" />
                                </svg>
                                @endif
                                <span class="text-sm font-semibold">{{ $stat['growth'] }}%</span>
                            </span>
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">change</span>
                        </dt>
                        <dd class="text-start ps-3 bg-gray-50 dark:bg-neutral-900 rounded-md p-2 flex justify-between space-x-2">
                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">{{ $stat['diff'] > 0 ? '+' : '' }}{{ $stat['diff'] }}</span>
                            <span class="block text-sm text-gray-500 dark:text-emerald-500">last month</span>
                        </dd>
                    </dl>
                </div>
                @endforeach
            </div>

            <!-- Chart -->
            <div class="bg-white border border-gray-200 shadow-xl rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                <div id="userGrowthChart" class="p-4"></div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<script>
    window.chartData = @json($chart);

    document.addEventListener('DOMContentLoaded', function () {
        const options = {
            chart: {
                type: 'line',
                height: 320,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, ui-sans-serif, system-ui',
                foreColor: '#059669'
            },
            series: chartData.series,
            colors: ['#059669'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 0.6,
                    opacityFrom: 1,
                    opacityTo: 0.3,
                    stops: [0, 80, 100]
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4
            },
            markers: {
                size: 0,
                hover: { size: 6 }
            },
            xaxis: {
                categories: chartData.labels,
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: val => Math.round(val)
                }
            },
            tooltip: {
                theme: 'dark'
            }
        };

        new ApexCharts(
            document.querySelector("#userGrowthChart"),
            options
        ).render();
    });
</script>
