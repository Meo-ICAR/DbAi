@php
    $page = 'history-index';
@endphp
@extends('history.layout')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
                <h2 class="text-2xl font-semibold text-gray-800">{{ __('Cronologia delle Query') }}</h2>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <!-- Search Form -->
                    <form method="GET" action="{{ url('/history') }}" class="flex-1">
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md"
                                   placeholder="{{ __('Cerca query...') }}"
                                   value="{{ request('search') }}">
                            @if(request('search'))
                                <a href="{{ url('/history') }}"
                                   class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                   title="{{ __('Cancella ricerca') }}">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <a href="{{ url('/history/create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md whitespace-nowrap">
                        <i class="fas fa-plus mr-2"></i>{{ __('Nuova Query') }}
                    </a>
                </div>
            </div>

            @if($histories->isEmpty())
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-history text-4xl mb-4"></i>
                    <p>{{ __('Nessuna cronologia trovata.') }}</p>
                    @if(request()->has('search'))
                        <a href="{{ url('/history') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            {{ __('Cancella ricerca') }}
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
                                        Dashboard #
                                        @if($sortField === 'dashboardorder')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'message', 'direction' => $sortField === 'message' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        {{ __('Messaggio') }}
                                        @if($sortField === 'message')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'charttype', 'direction' => $sortField === 'charttype' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        {{ __('Tipo di Grafico') }}
                                        @if($sortField === 'charttype')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => $sortField === 'created_at' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                        {{ __('Data di Creazione') }}
                                        @if($sortField === 'created_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'nviewed', 'direction' => $sortField === 'nviewed' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center">
                                        {{ __('Visualizzazioni') }}
                                        @if($sortField === 'nviewed')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Azioni') }}
                                </th>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($histories as $index => $history)
                                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-100 [&:hover>*]:text-blue-800 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            <div class="flex items-center justify-center space-x-1">
                                                <button data-history-id="{{ $history->id }}" data-change="-10"
                                                        class="update-order-btn text-gray-500 hover:text-blue-600 p-1 rounded-full hover:bg-gray-100"
                                                        title="Decrease order by 10">
                                                    <i class="fas fa-minus-circle"></i>
                                                </button>
                                                <span class="px-2">{{ $history->dashboardorder }}</span>
                                                <button data-history-id="{{ $history->id }}" data-change="10"
                                                        class="update-order-btn text-gray-500 hover:text-blue-600 p-1 rounded-full hover:bg-gray-100"
                                                        title="Increase order by 10">
                                                    <i class="fas fa-plus-circle"></i>
                                                </button>
                                            </div>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100">
                                            {{ $history->nviewed }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ url("/history/{$history->id}/display") }}"
                                           class="text-green-600 hover:text-green-900 mr-3"
                                           title="Execute query">
                                            <i class="fas fa-play"></i>
                                        </a>
                                        <a href="{{ url("/history/{$history->id}/edit") }}"
                                           class="text-indigo-600 hover:text-indigo-900 mr-3"
                                           title="Edit query">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('history.clone', $history) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-blue-600 hover:text-blue-900"
                                                    title="Clone">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        </form>
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

@push('scripts')
<script>
// Function to handle dashboard order updates
function updateDashboardOrder(button, historyId, change) {
    if (!button || !historyId || isNaN(change)) return;

    // Show loading state
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('Errore di sicurezza. Si prega di ricaricare la pagina.');
        button.innerHTML = originalHtml;
        button.disabled = false;
        return;
    }

    const token = csrfToken.getAttribute('content');
    const url = `/history/${historyId}/update-order`;

    // Send AJAX request
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ change })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`Errore: ${response.status}`);
            });
        }
        window.location.reload();
    })
    .catch(error => {
        alert('Si è verificato un errore durante l\'aggiornamento dell\'ordine.');
        button.innerHTML = originalHtml;
        button.disabled = false;
    });
}

// Add event listeners after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all order buttons
    document.querySelectorAll('.update-order-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const historyId = this.getAttribute('data-history-id');
            const change = parseInt(this.getAttribute('data-change'));

            if (!historyId || isNaN(change)) return;

            updateDashboardOrder(this, historyId, change);
        });
    });
});
</script>
@endpush
