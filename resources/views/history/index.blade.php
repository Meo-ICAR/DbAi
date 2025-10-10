@extends('history.layout')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
                <h2 class="text-2xl font-semibold text-gray-800">Query History</h2>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <!-- Search Form -->
                    <form method="GET" action="{{ url('/history') }}" class="flex-1">
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search" 
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" 
                                   placeholder="Search queries..." 
                                   value="{{ request('search') }}">
                            @if(request('search'))
                                <a href="{{ url('/history') }}" 
                                   class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                    
                    <a href="{{ url('/history/create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md whitespace-nowrap">
                        <i class="fas fa-plus mr-2"></i>New Query
                    </a>
                </div>
            </div>

            @if($histories->isEmpty())
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-history text-4xl mb-4"></i>
                    <p>No query history found.</p>
                    @if(request()->has('search'))
                        <a href="{{ url('/history') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            Clear search
                        </a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'dashboardorder', 'direction' => $sortField === 'dashboardorder' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        #
                                        @if($sortField === 'dashboardorder')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'message', 'direction' => $sortField === 'message' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        Message
                                        @if($sortField === 'message')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'charttype', 'direction' => $sortField === 'charttype' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        Chart Type
                                        @if($sortField === 'charttype')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'submission_date', 'direction' => $sortField === 'submission_date' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        Last Executed
                                        @if($sortField === 'submission_date')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($histories as $history)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ $history->dashboardorder }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-medium">{{ Str::limit($history->message, 40) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <code class="truncate block max-w-xs">{{ Str::limit($history->sqlstatement, 50) }}</code>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $history->charttype === 'Table' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $history->charttype }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div title="{{ $history->submission_date->format('Y-m-d H:i:s') }}">
                                            {{ $history->submission_date->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ url("/history/{$history->id}/display") }}" 
                                           class="text-green-600 hover:text-green-900 mr-3"
                                           title="Execute query">
                                            <i class="fas fa-play"></i>
                                        </a>
                                        <a href="{{ url("/history/{$history->id}") }}" 
                                           class="text-blue-600 hover:text-blue-900 mr-3"
                                           title="View details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ url("/history/{$history->id}/edit") }}" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-3"
                                           title="Edit query">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ url("/history/{$history->id}") }}" 
                                              method="POST" 
                                              class="inline delete-form"
                                              onsubmit="return confirm('Are you sure you want to delete this query?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Delete query">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
