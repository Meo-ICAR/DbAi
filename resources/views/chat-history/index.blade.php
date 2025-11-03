@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chat History') }}</span>
                    <a href="{{ route('chat-history.create') }}" class="btn btn-primary btn-sm">
                        {{ __('Add New') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thread ID</th>
                                    <th>Messages</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $history)
                                    <tr>
                                        <td>{{ $history->id }}</td>
                                        <td>{{ $history->thread_id }}</td>
                                        <td>{{ Str::limit(json_encode($history->messages), 50) }}</td>
                                        <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('chat-history.show', $history->id) }}" class="btn btn-info btn-sm">View</a>
                                            <a href="{{ route('chat-history.edit', $history->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('chat-history.destroy', $history->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No chat history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        {{ $histories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
