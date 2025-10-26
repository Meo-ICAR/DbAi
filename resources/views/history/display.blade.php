@php
    $page = 'history-display';
@endphp

@extends('history.layout')

@php
    // Initialize variables
    $numericColumns = [];
    $hasNumericColumns = false;
    $totals = [];

    // First pass: Determine which columns contain numeric values
    if ($results && count($results) > 0) {
        foreach (array_keys((array)$results[0]) as $column) {
            $isNumericColumn = false;
            $totals[$column] = 0;

            foreach ($results as $row) {
                $value = $row->$column ?? null;
                if (is_numeric($value)) {
                    $isNumericColumn = true;
                    $totals[$column] += $value;
                } elseif ($value !== null && is_string($value) && is_numeric(str_replace([',', ' '], ['.', ''], $value))) {
                    $isNumericColumn = true;
                    $totals[$column] += (float)str_replace([',', ' '], ['.', ''], $value);
                }
            }

            $numericColumns[$column] = $isNumericColumn;
            if ($isNumericColumn) {
                $hasNumericColumns = true;
            } else {
                $totals[$column] = null;
            }
        }
    }
@endphp

@section('content')
<div class="container mx-auto max-w-6xl p-6" x-data="{ showFilters: false }">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ request()->url() }}" method="GET" id="filter-form">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ $history->message }}</h1>
            <div class="flex items-center space-x-4">
                @if($history->charttype !== 'Table')
                    <a href="{{ url("/history/{$history->id}/chart") }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                        <i class="fas fa-chart-pie mr-2"></i> View Chart
                    </a>
                @endif

                <!-- Add this form for Excel export -->
                <form action="{{ route('history.display', $history) }}" method="GET" class="inline">
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request('direction'))
                        <input type="hidden" name="direction" value="{{ request('direction') }}">
                    @endif
                    <input type="hidden" name="export" value="1">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                        <i class="fas fa-file-excel mr-2"></i> Export to Excel
                    </button>
                </form>

                <a href="{{ url('/history') }}"
                   class="text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to History
                </a>
            </div>
        </div>

        @if($error)
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
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
        @endif

        @if($results && count($results) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(array_keys((array)$results[0]) as $column)
                                @php
                                    $isSorted = $sortColumn === $column;
                                    $sortIcon = '';
                                    if ($isSorted) {
                                        $sortIcon = $sortDirection === 'asc'
                                            ? '<i class="fas fa-sort-up ml-1"></i>'
                                            : '<i class="fas fa-sort-down ml-1"></i>';
                                    } else {
                                        $sortIcon = '<i class="fas fa-sort ml-1 text-gray-300"></i>';
                                    }
                                @endphp
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-between">
                                        <span class="cursor-pointer hover:bg-gray-100 px-2 py-1 rounded" 
                                              onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $isSorted && $sortDirection === 'asc' ? 'desc' : 'asc']) }}#results-table'">
                                            {{ $column }}
                                            {!! $sortIcon !!}
                                        </span>
                                        @if(!in_array($column, array_keys(array_filter($numericColumns))))
                                            <button type="button" 
                                                    class="text-gray-400 hover:text-gray-600 focus:outline-none"
                                                    @click="showFilters = !showFilters"
                                                    title="Toggle filters">
                                                <i class="fas fa-filter"></i>
                                            </button>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                        <tr class="bg-gray-50" x-show="showFilters" x-transition>
                            @php
                                // Get the first row to determine column types
                                $firstRow = $results[0] ?? null;
                                $columns = $firstRow ? array_keys((array)$firstRow) : [];
                            @endphp
                            @foreach($columns as $column)
                                @php
                                    $isNumeric = $numericColumns[$column] ?? false;
                                    $filterValue = request('filter')[$column] ?? '';
                                @endphp
                                <td class="px-2 py-1">
                                    @if(!$isNumeric)
                                        <div class="relative">
                                            <input type="text" 
                                                   name="filter[{{ $column }}]" 
                                                   value="{{ $filterValue }}" 
                                                   placeholder="Filter {{ $column }}" 
                                                   class="w-full text-xs pl-8 pr-2 py-1 border rounded focus:ring-2 focus:ring-blue-300 focus:border-blue-300"
                                                   onchange="this.form.submit()">
                                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                <i class="fas fa-search text-gray-400 text-xs"></i>
                                            </div>
                                            @if($filterValue)
                                                <a href="{{ request()->fullUrlWithQuery(['filter' => array_merge(request('filter', []), [$column => ''])]) }}" 
                                                   class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 hover:text-gray-600">
                                                    <i class="fas fa-times text-xs"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="results-table">

                        @foreach($results as $index => $row)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-100 [&:hover>*]:text-blue-800 transition-colors duration-200">
                                @foreach($row as $column => $value)
                                    @php
                                        $isNumeric = $numericColumns[$column] ?? false;
                                        $cellClass = 'px-6 py-4 whitespace-nowrap text-sm ' . ($isNumeric ? 'text-right font-mono' : 'text-gray-500');

                                        // Format numeric values
                                        $displayValue = $value;
                                        if ($isNumeric && $value !== null) {
                                            $numericValue = is_numeric($value) ? $value : (is_string($value) ? (float)str_replace([',', ' '], ['.', ''], $value) : $value);
                                            if (is_numeric($numericValue)) {
                                                $displayValue = is_float($numericValue)
                                                    ? number_format($numericValue, 2, ',', '.')
                                                    : number_format($numericValue, 2, ',', '.');
                                                   // : number_format($numericValue, 0, ',', '.');
                                            }
                                        }
                                    @endphp
                                    <td class="{{ $cellClass }}">
                                        @if($isNumeric && is_numeric($value))
                                            <div class="flex justify-end">
                                                {{ $displayValue }}
                                            </div>
                                        @else
                                            {{ $displayValue }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        @if($hasNumericColumns || true)
                            <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                                <tr class="font-bold">
                                    <td class="px-6 py-3 text-sm text-gray-500">N. {{ count($results) }}</td>
                                    @php $first = true; @endphp
                                    @foreach($totals as $column => $total)
                                        @if(!$first)
                                            <td class="px-6 py-3 text-sm {{ $total !== null ? 'text-right font-mono' : 'text-gray-500' }}">
                                                @if($total !== null)
                                                    {{ is_float($total) ? number_format($total, 2, ',', '.') : number_format($total, 0, ',', '.') }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </td>
                                        @endif
                                        @php $first = false; @endphp
                                    @endforeach
                                </tr>
                            </tfoot>
                        @endif
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">No results found.</p>
            </div>
        @endif
        </form>
    </div>
</div>

<script>
    // Submit form when clicking on sortable headers
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filter-form');
        const sortableHeaders = document.querySelectorAll('th[onclick]');
        
        sortableHeaders.forEach(header => {
            header.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.getAttribute('onclick').match(/window\.location\.href='([^']+)'/)[1]);
                const params = new URLSearchParams(url.search);
                
                // Update sort and direction in our form
                if (params.has('sort')) {
                    const sortInput = form.querySelector('input[name="sort"]');
                    if (sortInput) {
                        sortInput.value = params.get('sort');
                    } else {
                        const newInput = document.createElement('input');
                        newInput.type = 'hidden';
                        newInput.name = 'sort';
                        newInput.value = params.get('sort');
                        form.appendChild(newInput);
                    }
                }
                
                if (params.has('direction')) {
                    const dirInput = form.querySelector('input[name="direction"]');
                    if (dirInput) {
                        dirInput.value = params.get('direction');
                    } else {
                        const newInput = document.createElement('input');
                        newInput.type = 'hidden';
                        newInput.name = 'direction';
                        newInput.value = params.get('direction');
                        form.appendChild(newInput);
                    }
                }
                
                form.submit();
            });
        });
    });
</script>
@endsection
