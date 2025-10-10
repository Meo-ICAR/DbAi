<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Query Chart</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-6xl p-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Query Results Chart</h1>
            
            <!-- Chart Type Selector -->
            <div class="mb-6">
                <label for="chart-type" class="block text-sm font-medium text-gray-700 mb-2">Chart Type</label>
                <select id="chart-type" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="bar">Bar Chart</option>
                    <option value="line">Line Chart</option>
                    <option value="pie">Pie Chart</option>
                    <option value="doughnut">Doughnut Chart</option>
                </select>
            </div>
            
            <!-- Chart Container -->
            <div class="bg-white p-4 border border-gray-200 rounded-lg">
                <canvas id="results-chart" height="400"></canvas>
            </div>
            
            <!-- Table View Toggle -->
            <div class="mt-6">
                <button id="toggle-table" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Show Table Data
                </button>
                <div id="table-container" class="mt-4 hidden">
                    <div class="overflow-x-auto">
                        <table id="data-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr id="table-header">
                                    <!-- Headers will be inserted here by JavaScript -->
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                <!-- Data rows will be inserted here by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Back Button -->
            <div class="mt-8">
                <a href="{{ route('chat') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Chat
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the chart data from the server
            const chartData = @json($chartData);
            const query = @json($query);
            
            // Check if we have data to display
            if (!chartData || chartData.length === 0) {
                document.querySelector('#results-chart').parentElement.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-gray-500">No chart data available.</p>
                        <p class="text-sm text-gray-400 mt-2">The query did not return any results or is not a GROUP BY query.</p>
                    </div>
                `;
                return;
            }
            
            // Extract labels and data from the query results
            const labels = chartData.map(item => item[Object.keys(item)[0]]);
            const data = chartData.map(item => item[Object.keys(item)[1]]);
            const label = Object.keys(chartData[0])[1];
            
            // Get chart context
            const ctx = document.getElementById('results-chart').getContext('2d');
            
            // Create chart
            let chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: generateColors(data.length, 0.7),
                        borderColor: generateColors(data.length, 1),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Query Results Visualization',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y || context.parsed}`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Handle chart type change
            document.getElementById('chart-type').addEventListener('change', function() {
                chart.destroy();
                chart = new Chart(ctx, {
                    type: this.value,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: generateColors(data.length, 0.7),
                            borderColor: generateColors(data.length, 1),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Query Results Visualization',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.parsed.y || context.parsed}`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
            
            // Generate colors for the chart
            function generateColors(count, opacity = 1) {
                const colors = [
                    'rgba(54, 162, 235, ' + opacity + ')',  // blue
                    'rgba(255, 99, 132, ' + opacity + ')',   // red
                    'rgba(75, 192, 192, ' + opacity + ')',   // teal
                    'rgba(255, 205, 86, ' + opacity + ')',   // yellow
                    'rgba(153, 102, 255, ' + opacity + ')',  // purple
                    'rgba(255, 159, 64, ' + opacity + ')'    // orange
                ];
                
                const result = [];
                for (let i = 0; i < count; i++) {
                    result.push(colors[i % colors.length]);
                }
                return result;
            }
            
            // Toggle table view
            document.getElementById('toggle-table').addEventListener('click', function() {
                const tableContainer = document.getElementById('table-container');
                const isHidden = tableContainer.classList.contains('hidden');
                
                if (isHidden) {
                    // Populate table if not already done
                    if (document.getElementById('table-body').children.length === 0) {
                        populateTable();
                    }
                    this.textContent = 'Hide Table Data';
                } else {
                    this.textContent = 'Show Table Data';
                }
                
                tableContainer.classList.toggle('hidden');
            });
            
            // Populate the data table
            function populateTable() {
                const tableHeader = document.getElementById('table-header');
                const tableBody = document.getElementById('table-body');
                
                // Clear existing content
                tableHeader.innerHTML = '';
                tableBody.innerHTML = '';
                
                if (chartData.length === 0) return;
                
                // Add headers
                const headers = Object.keys(chartData[0]);
                headers.forEach(header => {
                    const th = document.createElement('th');
                    th.className = 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';
                    th.textContent = header;
                    tableHeader.appendChild(th);
                });
                
                // Add rows
                chartData.forEach(row => {
                    const tr = document.createElement('tr');
                    headers.forEach(header => {
                        const td = document.createElement('td');
                        td.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-500';
                        td.textContent = row[header];
                        tr.appendChild(td);
                    });
                    tableBody.appendChild(tr);
                });
            }
        });
    </script>
</body>
</html>
