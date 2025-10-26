@php
    $page = 'history-tables';
@endphp
@extends('history.layout')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('history.tables') }}" method="GET" class="mb-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-semibold">Tabelle Salvate</h2>
                        <div class="relative w-1/3">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cerca per messaggio..." 
                                   class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            @if(request('search'))
                                <a href="{{ route('history.tables') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($tables->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Messaggio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                                </tr>
                            </thead>
                            <tbody id="tablesBody" class="bg-white divide-y divide-gray-200">
                                @foreach($tables as $table)
                                    <tr class="table-row" data-message="{{ strtolower($table->message) }}">
                                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-900 max-w-xs">
                                            <div class="line-clamp-2">
                                                {{ $table->message }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $table->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('history.display', $table) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                <i class="fas fa-eye"></i> Visualizza
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tables->links() }}
                    </div>
                @else
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Nessuna tabella trovata.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit the form when typing (with a small delay)
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            this.form.submit();
        }, 500); // 500ms delay
    });

    // Handle the clear button
    const clearButton = document.querySelector('.fa-times');
    if (clearButton) {
        clearButton.addEventListener('click', function(e) {
            e.preventDefault();
            searchInput.value = '';
            searchInput.form.submit();
        });
    }
});
</script>
@endpush

@endsection
