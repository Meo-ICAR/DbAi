@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Shared Query</h1>
            <div class="text-sm text-gray-500">
                @if($history->share_expires_at)
                    Expires {{ $history->share_expires_at->diffForHumans() }}
                @else
                    No expiration
                @endif
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2 text-gray-700">Query</h2>
            <div class="bg-gray-50 p-4 rounded border border-gray-200 overflow-x-auto">
                <pre class="font-mono text-sm text-gray-800 whitespace-pre-wrap">{{ $history->sqlstatement }}</pre>
            </div>
        </div>

        @if($history->message)
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2 text-gray-700">Message</h2>
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <p class="text-gray-800">{{ $history->message }}</p>
            </div>
        </div>
        @endif

        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex flex-col space-y-2 text-sm text-gray-500">
                <div>Shared by: {{ $history->user->name ?? 'Unknown User' }}</div>
                <div>Created: {{ $history->created_at->format('M j, Y g:i A') }}</div>
                @if($history->database_name)
                    <div>Database: {{ $history->database_name }}</div>
                @endif
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
    .bg-gray-50 {
        background-color: #f9fafb;
    }
</style>
@endpush
@endsection
