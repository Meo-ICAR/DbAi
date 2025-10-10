@extends('history.layout')

@section('content')
<div class="container mx-auto max-w-6xl p-6">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $isSorted && $sortDirection === 'asc' ? 'desc' : 'asc']) }}#results-table'">
                                    <div class="flex items-center">
                                        <span>{{ $column }}</span>
                                        {!! $sortIcon !!}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="results-table">
                        @foreach($results as $row)
                            <tr>
                                @foreach($row as $value)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $value }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
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
@endsection
