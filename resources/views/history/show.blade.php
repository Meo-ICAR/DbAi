@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col space-y-4 md:flex-row md:justify-between md:items-center mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Query Details</h2>
                    <p class="text-sm text-gray-500">
                        Created {{ $history->created_at->diffForHumans() }}
                        @if($history->updated_at != $history->created_at)
                            • Updated {{ $history->updated_at->diffForHumans() }}
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($history->isShareable())
                    <div class="relative flex-1">
                        <input type="text" 
                               id="share-link" 
                               value="{{ route('history.share', $history->share_token) }}" 
                               class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-md" 
                               readonly>
                        <button onclick="copyShareLink()" 
                                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                            <i class="far fa-copy"></i>
                        </button>
                    </div>
                    @endif
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('history.edit', $history) }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                        <form action="{{ route('history.generate-share-link', $history) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md">
                                <i class="fas fa-share-alt mr-2"></i> {{ $history->isShareable() ? 'Renew Link' : 'Share' }}
                            </button>
                        </form>
                        <form action="{{ route('history.destroy', $history) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to delete this history item?')"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md">
                                <i class="fas fa-trash mr-2"></i> Delete
                            </button>
                        </form>
                        <a href="{{ route('history.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            @if(session('status') === 'share-link-generated')
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                Shareable link created! It will expire 
                                @if(session('expires_at'))
                                    {{ \Carbon\Carbon::parse(session('expires_at'))->diffForHumans() }}
                                @else
                                    never
                                @endif
                                .
                            </p>
                            <div class="mt-2 flex">
                                <input type="text" 
                                       value="{{ session('share_url') }}" 
                                       class="flex-1 text-sm border-gray-300 rounded-l-md"
                                       readonly>
                                <button onclick="copyToClipboard('{{ session('share_url') }}')" 
                                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-700 text-sm rounded-r-md hover:bg-gray-100">
                                    <i class="far fa-copy mr-1"></i> Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
                </div>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Query Information
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Details about the saved query.
                            </p>
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $history->message }}
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Dashboard Order
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $history->dashboardorder ?? '0' }}
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                SQL Statement
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <pre class="bg-gray-100 p-4 rounded-md overflow-x-auto"><code>{{ $history->sqlstatement }}</code></pre>
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Chart Type
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $history->charttype }}
                                </span>
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Last Executed
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $history->submission_date->format('F j, Y, g:i a') }}
                                <span class="text-gray-500">({{ $history->submission_date->diffForHumans() }})</span>
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Created At
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $history->created_at->format('F j, Y, g:i a') }}
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Last Updated
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $history->updated_at->format('F j, Y, g:i a') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <form action="{{ route('chat') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="message" value="{{ $history->message }}">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-play mr-2"></i>Run Query Again
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show a temporary success message
        const button = event.target.closest('button');
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
        button.classList.add('bg-green-100', 'text-green-800');
        
        setTimeout(() => {
            button.innerHTML = originalHtml;
            button.classList.remove('bg-green-100', 'text-green-800');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

function copyShareLink() {
    const shareLink = document.getElementById('share-link');
    shareLink.select();
    document.execCommand('copy');
    
    // Show a temporary success message
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    
    setTimeout(() => {
        button.innerHTML = originalHtml;
    }, 2000);
}
</script>
@endpush
