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
<div class="container mx-auto p-6 w-full" style="max-width: 95%;" x-data="{
    searchTerm: '',
    
    filterResults() {
        const searchTerm = this.searchTerm.trim().toLowerCase();
        const rows = document.querySelectorAll('#results-table tr[data-searchable]');
        const tfoot = document.querySelector('tfoot');
        let hasVisibleRows = false;
        
        // Show/hide footer based on search
        if (tfoot) {
            tfoot.style.display = searchTerm ? 'none' : '';
        }
        
        // Filter rows
        rows.forEach(row => {
            const rowText = Array.from(row.querySelectorAll('td'))
                .map(cell => cell.textContent.toLowerCase())
                .join(' ');
                
            const isVisible = !searchTerm || rowText.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
            
            if (isVisible) {
                hasVisibleRows = true;
            }
        });
        
        // Show/hide no results message
        const noResults = document.getElementById('no-results-message');
        if (noResults) {
            noResults.style.display = hasVisibleRows || !searchTerm ? 'none' : '';
        }
    }
}">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ $history->message }}</h1>
            <div class="flex items-center space-x-4">
                @if($history->charttype !== 'Table')
                    <a href="{{ url("/history/{$history->id}/chart") }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                        <i class="fas fa-chart-pie mr-2"></i> View Chart
                    </a>
                @endif

                <form action="{{ route('history.display', $history) }}" method="GET" class="inline">
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
            <div class="mb-4">
                <div class="relative max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           x-model="searchTerm" 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Search in all columns..."
                           x-on:input.debounce.300ms="filterResults()"
                           ">
                </div>
            </div>
            
            <div class="overflow-x-auto text-base">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
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
                                    <span class="cursor-pointer hover:bg-gray-100 px-2 py-1 rounded" 
                                          onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $isSorted && $sortDirection === 'asc' ? 'desc' : 'asc']) }}#results-table'">
                                        {{ $column }}
                                        {!! $sortIcon !!}
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="results-table">

                        <tr id="no-results-message" style="display: none;">
                            <td colspan="100%" class="px-6 py-4 text-center text-gray-500">
                                No matching records found
                            </td>
                        </tr>
                        @foreach($results as $index => $row)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-100 [&:hover>*]:text-blue-800 transition-colors duration-200" 
                                data-searchable
                                x-data="{}">
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
                                    <td class="{{ $cellClass }} py-3">
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
                                    <td class="px-6 py-3 text-sm text-gray-500 row-count" data-total="-1">N. {{ count($results) }}</td>
                                    @php 
                                        $first = true;
                                        $colIndex = 0;
                                    @endphp
                                    @foreach($totals as $column => $total)
                                        @if(!$first)
                                            <td class="px-6 py-3 text-sm {{ $total !== null ? 'text-right font-mono' : 'text-gray-500' }}" 
                                                data-total="{{ $colIndex }}">
                                                @if($total !== null)
                                                    {{ is_float($total) ? number_format($total, 2, ',', '.') : number_format($total, 0, ',', '.') }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </td>
                                        @endif
                                        @php 
                                            $first = false;
                                            $colIndex++;
                                        @endphp
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
    </div>
</div>

                
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Any additional Alpine.js initializations can go here
    });
</script>
@endpush
@push('styles')
<style>
    /* Highlight matching text */
    .highlight {
        background-color: #fef08a;
        padding: 0.1em 0.2em;
        border-radius: 0.25em;
    }
    
    /* Search input focus state */
    #searchInput:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
</style>
@endpush
@endsection
