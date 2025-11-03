@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">{{ __('Chat History Details') }}</h2>
            <div class="space-x-2">
                <a href="{{ route('chat-history.edit', $history->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('chat-history.index') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <div class="bg-white overflow-hidden">
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $history->id }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Thread ID</dt>
                        <dd class="mt-1 text-sm font-mono text-gray-900 bg-gray-50 p-2 rounded">{{ $history->thread_id }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Messages</dt>
                        <dd class="mt-1">
                            <div class="bg-gray-50 p-4 rounded-md overflow-auto max-h-96">
                                <pre class="text-xs text-gray-800"><code>{{ json_encode($history->messages, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <time datetime="{{ $history->created_at->toIso8601String() }}">
                                {{ $history->created_at->format('M d, Y H:i:s') }}
                            </time>
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <time datetime="{{ $history->updated_at->toIso8601String() }}">
                                {{ $history->updated_at->format('M d, Y H:i:s') }}
                            </time>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    code {
        font-family: 'Fira Code', 'Courier New', monospace;
    }
</style>
@endpush

@push('scripts')
<script>
    // Add syntax highlighting or other JS functionality here if needed
</script>
@endpush
@endsection
