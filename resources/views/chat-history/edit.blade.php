@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Edit Chat History') }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Update the chat history details below.') }}
                </p>
                <div class="mt-4">
                    <a href="{{ route('chat-history.show', $history->id) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('Back to details') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('chat-history.update', $history->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="thread_id" class="block text-sm font-medium text-gray-700">Thread ID</label>
                                <input type="text" 
                                       id="thread_id" 
                                       name="thread_id" 
                                       value="{{ old('thread_id', $history->thread_id) }}"
                                       required
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('thread_id') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                @error('thread_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="messages" class="block text-sm font-medium text-gray-700">Messages (JSON)</label>
                                <div class="mt-1">
                                    <textarea id="messages" 
                                              name="messages" 
                                              rows="15" 
                                              required
                                              class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md font-mono text-sm @error('messages') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">{{ old('messages', json_encode($history->messages, JSON_PRETTY_PRINT)) }}</textarea>
                                </div>
                                @error('messages')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @else
                                    <p class="mt-2 text-sm text-gray-500">Enter a valid JSON array of messages.</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <div class="flex justify-between">
                            <form action="{{ route('chat-history.destroy', $history->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this chat history? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    {{ __('Delete') }}
                                </button>
                            </form>
                            <div class="space-x-3">
                                <a href="{{ route('chat-history.show', $history->id) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('Save Changes') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesTextarea = document.getElementById('messages');
        
        // Format JSON on page load
        try {
            const json = JSON.parse(messagesTextarea.value);
            messagesTextarea.value = JSON.stringify(json, null, 2);
        } catch (e) {
            console.error('Invalid JSON:', e);
        }
        
        // Auto-resize textarea
        function adjustTextareaHeight() {
            messagesTextarea.style.height = 'auto';
            messagesTextarea.style.height = (messagesTextarea.scrollHeight) + 'px';
        }
        
        messagesTextarea.addEventListener('input', adjustTextareaHeight);
        adjustTextareaHeight(); // Initial adjustment
        
        // Add JSON validation on form submit
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            try {
                JSON.parse(messagesTextarea.value);
            } catch (error) {
                e.preventDefault();
                alert('Invalid JSON format. Please check your input.');
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    textarea#messages {
        font-family: 'Fira Code', 'Courier New', monospace;
        min-height: 400px;
        white-space: pre;
        overflow-x: auto;
    }
</style>
@endpush
@endsection
