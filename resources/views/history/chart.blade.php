@php
    $page = 'history-chart';
@endphp

@extends('history.layout')

@section('content')
<div class="container mx-auto max-w-6xl p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ $history->message }}</h1>
            <div class="flex items-center space-x-4">
            <select id="chart-type" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="bar" {{ str_contains(strtolower($history->charttype), 'bar') ? 'selected' : '' }}>Bar Chart</option>
                    <option value="line" {{ str_contains(strtolower($history->charttype), 'line') ? 'selected' : '' }}>Line Chart</option>
                    <option value="pie" {{ str_contains(strtolower($history->charttype), 'pie') ? 'selected' : '' }}>Pie Chart</option>
                    <option value="doughnut" {{ str_contains(strtolower($history->charttype), 'doughnut') ? 'selected' : '' }}>Doughnut Chart</option>
                </select>
            <a href="{{ url("/history/{$history->id}/display") }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                        <i class="fas fa-table mr-2"></i> View Table
                    </a>
                    <a href="{{ url('/history') }}"
                   class="text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to History
                </a>
            </div>
        </div>
    <div class="chart-container p-6">
        @if($error)
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @else
        <canvas id="results-chart" data-url="{{ route('history.subdashboard', ['history' => $history->id

        ]) }}">
        </canvas>
        @endif
    </div>

    @if($chartData)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);
            const canvas = document.getElementById('results-chart');
            let chart;

            function initChart(type = chartData.type) {
                if (chart) {
                    chart.destroy();
                }

                const ctx = canvas.getContext('2d');
                chart = new Chart(ctx, {
                    type: type,
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                           // label: chartData.title,
                            data: chartData.data,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(153, 102, 255, 0.7)',
                                'rgba(255, 159, 64, 0.7)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        onClick: (e, elements) => {
                            const baseUrl = canvas.getAttribute('data-url');  // Alternative method
                            if (!baseUrl) {
                                    console.error('Error: data-url attribute is missing from the canvas.');
                                    return;
                            }
                            console.log(baseUrl);
                            if (elements.length > 0) {
                                const elementIndex = elements[0].index;
                                const label = chartData.labels[elementIndex];
                                const value = chartData.data[elementIndex];
                                const url = new URL(baseUrl);
                                url.searchParams.append('filter_column', value || 'label');
                                url.searchParams.append('filter_value', label);
                                window.location.href = url.toString();
                            }
                        },

                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false,
                                text: chartData.title,
                                font: {
                                    size: 16
                                }
                            },

                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuart'
                        },
                        layout: {
                            padding: 20
                        },
                        cursor: 'pointer'
                    }
                });
            }

            // Initial chart render
            initChart();

            // Handle chart type change
            document.getElementById('chart-type').addEventListener('change', function() {
                initChart(this.value);
            });

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (chart) {
                        chart.resize();
                    }
                }, 250);
            });
        });
    </script>
    @endif
    </div>
</div>
@endsection
