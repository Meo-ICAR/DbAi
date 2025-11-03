@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chat History Details') }}</span>
                    <div>
                        <a href="{{ route('chat-history.edit', $history->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('chat-history.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID:</th>
                                <td>{{ $history->id }}</td>
                            </tr>
                            <tr>
                                <th>Thread ID:</th>
                                <td>{{ $history->thread_id }}</td>
                            </tr>
                            <tr>
                                <th>Messages:</th>
                                <td><pre>{{ json_encode($history->messages, JSON_PRETTY_PRINT) }}</pre></td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At:</th>
                                <td>{{ $history->updated_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
