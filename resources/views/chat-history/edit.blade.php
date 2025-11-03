@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Edit Chat History') }}
                </div>

                <div class="card-body">
                    <form action="{{ route('chat-history.update', $history->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="thread_id" class="form-label">Thread ID</label>
                            <input type="text" class="form-control @error('thread_id') is-invalid @enderror" 
                                   id="thread_id" name="thread_id" value="{{ old('thread_id', $history->thread_id) }}" required>
                            @error('thread_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="messages" class="form-label">Messages (JSON)</label>
                            <textarea class="form-control @error('messages') is-invalid @enderror" 
                                     id="messages" name="messages" rows="10" required>{{ old('messages', json_encode($history->messages, JSON_PRETTY_PRINT)) }}</textarea>
                            @error('messages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Enter a valid JSON array of messages
                            </small>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('chat-history.show', $history->id) }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end">
                        <form action="{{ route('chat-history.destroy', $history->id) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete this item? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                {{ __('Delete Chat History') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Format JSON on page load
    document.addEventListener('DOMContentLoaded', function() {
        const messagesTextarea = document.getElementById('messages');
        try {
            const json = JSON.parse(messagesTextarea.value);
            messagesTextarea.value = JSON.stringify(json, null, 4);
        } catch (e) {
            // If invalid JSON, leave as is
        }
    });
</script>
@endpush
@endsection
