<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Database Assistant</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}" class="text-xl font-bold">Database Assistant</a>
                        <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Dashboard x</a>
                        <a href="{{ url('/history') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Query</a>
                        <a href="{{ url('/chat') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Chat</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div x-data="{ showInfo: false }">
                            <x-info-button page="dashboard" />
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-white hover:bg-blue-700 px-3 py-2 rounded">
                                <i class="fas fa-sign-out-alt mr-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="container mx-auto px-4 py-6">
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
        @if(!empty( $title))
        <a href="{{ url('/dashboard') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
        @endif
    </div>

    @if(empty($charts) && empty($errors))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No charts found for the dashboard. To add charts to the dashboard, set a positive dashboard order number in the history items.
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($errors))
        <div class="mb-6">
            @foreach($errors as $error)
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-2">
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
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($charts as $chart)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">{{ $chart['title'] }}</h3>
                        <div class="flex space-x-2">
                        @if($chart['data-has-slaves'])
                        <i class="fas fa-search-plus" class="text-gray-400 hover:text-gray-600"
                        title="Click on value for details"></i>
                        @endif
                        @if(empty(request('filter_value')))

                        <a href="{{ route('history.display', [
                                'history' => $chart['history'],
                                'filter_column' => request('filter_column'),
                                'filter_value' => request('filter_value')
                            ]) }}"
                               class="text-gray-400 hover:text-gray-600"
                               title="View Table">
                                <i class="fas fa-table"></i>
                            </a>
                            <a href="{{ route('history.chart', [
                                'history' => $chart['history'],
                                'filter_column' => request('filter_column'),
                                'filter_value' => request('filter_value')
                            ]) }}"
                               class="text-gray-400 hover:text-blue-600"
                               title="View Full Chart">
                                <i class="fas fa-chart-pie"></i>
                            </a>

                        @endif
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="chart-container" style="position: relative; height: 300px;">
                    @if($chart['data-has-slaves'])
                    <canvas id="{{ $chart['id'] }}" data-url="{{ route('history.subdashboard', ['history' => $chart['history']->id]) }}">
                    @else
                    <canvas id="{{ $chart['id'] }}" >
                    @endif
                    </canvas>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

</main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($charts as $chart)
            try {
                const ctx{{ $loop->index }} = document.getElementById('{{ $chart['id'] }}');

                if (!ctx{{ $loop->index }}) {
                    return;
                }

                const chartData = {
                    type: '{{ $chart['type'] }}',
                    data: {
                        labels: @json($chart['labels']),
                        datasets: [{
                           // label: '{{ $chart['title'] }}',
                            data: @json($chart['data']),
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.5)',
                                'rgba(255, 99, 132, 0.5)',
                                'rgba(255, 206, 86, 0.5)',
                                'rgba(75, 192, 192, 0.5)',
                                'rgba(153, 102, 255, 0.5)',
                                'rgba(255, 159, 64, 0.5)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: (function() {
                        const isPieChart = '{{ $chart['type'] }}'.toLowerCase().includes('pie') || '{{ $chart['type'] }}'.toLowerCase().includes('doughnut');

                        return {
                            responsive: true,
                            maintainAspectRatio: false,

                            onHover: (e, chartElement) => {
                                const canvasx = e.chart.canvas.id;
                                const baseUrl = document.getElementById(canvasx).getAttribute('data-url');  // Alternative method
                                if (baseUrl) {
                                    e.native.target.style.cursor = chartElement[0] ? 'default':'zoom-in';
                                }
                            },
                            onClick: (e, elements) => {
                                const canvasx = e.chart.canvas.id;
                                const baseUrl = document.getElementById(canvasx).getAttribute('data-url');  // Alternative method
                                if (!baseUrl) {
                                    //    console.error('Error: data-url attribute is missing from the canvas.');
                                        return;
                                }
                                console.log(baseUrl);
                                if (elements.length > 0) {
                                    const elementIndex = elements[0].index;
                                    const label = e.chart.data.labels[elementIndex];
                                    const value = e.chart.data.datasets[0].data[elementIndex];
                                    const url = new URL(baseUrl);
                                    url.searchParams.append('filter_column', value);
                                    url.searchParams.append('filter_value', label);
                                    window.location.href = url.toString();
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuad'
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                title: {
                                    display: false,
                                    text: '{{ $chart['title'].' --'}}',
                                    font: {
                                        size: 16
                                    },
                                    padding: {
                                        top: 10,
                                        bottom: 20
                                    }
                                }
                            },
                            scales: isPieChart ? {} : {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        display: true,
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        stepSize: 1
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            layout: {
                                padding: {
                                    left: 10,
                                    right: 10,
                                    top: 10,
                                    bottom: 10
                                }
                            }
                        };
                    })()
                };

                // Initialize the chart
                new Chart(ctx{{ $loop->index }}, chartData);
            } catch (error) {
                // Silently handle chart initialization errors
            }
        @endforeach
    });
    </script>
</body>
</html>
