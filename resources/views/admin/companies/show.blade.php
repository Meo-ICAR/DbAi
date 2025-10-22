@extends('admin.companies.layout')

@section('title', $company->name)

@section('actions')
    <a href="{{ route('admin.companies.edit', $company) }}" 
       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        Edit
    </a>
    <a href="{{ route('admin.companies.index') }}" 
       class="ml-2 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to List
    </a>
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Company Information
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Detailed information about {{ $company->name }}
            </p>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <!-- Basic Info -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $company->id }}
                    </dd>
                </div>
                
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $company->name }}
                    </dd>
                </div>
                
                @if($company->urlogo)
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Logo</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <img src="{{ $company->urlogo }}" alt="Company Logo" class="h-20 w-auto rounded-md">
                    </dd>
                </div>
                @endif
                
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Admin Email</dt>
                    <dd class="mt-1 text-sm text-blue-600 hover:text-blue-800 sm:mt-0 sm:col-span-2">
                        <a href="mailto:{{ $company->email_admin }}" class="hover:underline">{{ $company->email_admin }}</a>
                    </dd>
                </div>
                
                @if($company->url_attivazione)
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Activation URL</dt>
                    <dd class="mt-1 text-sm text-blue-600 hover:text-blue-800 sm:mt-0 sm:col-span-2">
                        <a href="{{ $company->url_attivazione }}" target="_blank" class="hover:underline break-all">{{ $company->url_attivazione }}</a>
                    </dd>
                </div>
                @endif
                
                <!-- Database Connection -->
                <div class="py-5 px-6 bg-gray-50">
                    <h4 class="text-md font-medium text-gray-900">Database Connection</h4>
                </div>
                
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Connection Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ strtoupper($company->db_connection) }}
                        </span>
                    </dd>
                </div>
                
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Host</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono sm:mt-0 sm:col-span-2">
                        {{ $company->db_host }}:{{ $company->db_port }}
                    </dd>
                </div>
                
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Database Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono sm:mt-0 sm:col-span-2">
                        {{ $company->db_database }}
                    </dd>
                </div>
                
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Username</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono sm:mt-0 sm:col-span-2">
                        {{ $company->db_username }}
                    </dd>
                </div>
                
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Secret Key</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex rounded-md shadow-sm">
                            <input type="password" 
                                   id="secretKey" 
                                   value="{{ $company->db_secrete }}" 
                                   class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   readonly>
                            <button type="button" 
                                    onclick="toggleSecret()"
                                    class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <svg id="eyeIcon" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </dd>
                </div>
                
                <!-- Timestamps -->
                <div class="py-5 px-6 bg-gray-50">
                    <h4 class="text-md font-medium text-gray-900">Timestamps</h4>
                </div>
                
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $company->created_at->format('M d, Y H:i') }}
                        <span class="text-gray-500 text-xs">({{ $company->created_at->diffForHumans() }})</span>
                    </dd>
                </div>
                
                @if($company->updated_at->ne($company->created_at))
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $company->updated_at->format('M d, Y H:i') }}
                        <span class="text-gray-500 text-xs">({{ $company->updated_at->diffForHumans() }})</span>
                    </dd>
                </div>
                @endif
            </dl>
        </div>
        
        <div class="px-4 py-4 bg-gray-50 sm:px-6 flex justify-end space-x-3">
            <form action="{{ route('admin.companies.destroy', $company) }}" 
                  method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this company? This action cannot be undone.');"
                  class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Company
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleSecret() {
        const secretInput = document.getElementById('secretKey');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (secretInput.type === 'password') {
            secretInput.type = 'text';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            `;
        } else {
            secretInput.type = 'password';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        }
    }
</script>
@endpush
