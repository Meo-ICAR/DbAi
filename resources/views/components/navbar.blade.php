@props(['page'])

<nav class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('chat.index') }}" class="text-xl font-bold">Database Assistant</a>
                <a href="{{ route('dashboard') }}" class="{{ Route::is('dashboard') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Dashboard</a>
                <a href="{{ route('history.index') }}" class="{{ Route::is('history.index') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Query</a>
                <a href="{{ route('tables') }}" class="{{ Route::is('tables') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Tabelle</a>
                <a href="{{ route('chat.index') }}" class="{{ Route::is('chat*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Chat</a>
            </div>
            <div class="flex items-center space-x-4">
                <div x-data="{ showInfo: false }">
                    <x-info-button :page="$page ?? 'default'" />
                </div>
                @auth
                    @if(auth()->user()->company && auth()->user()->company->urllogo)
                        <div class="flex items-center space-x-2 bg-blue-700 bg-opacity-50 px-3 py-1 rounded-full">
                            <img src="{{ asset(auth()->user()->company->urllogo) }}"
                                 alt="{{ auth()->user()->company->name }}"
                                 class="h-8 w-8 rounded-full object-cover border-2 border-white">
                            <span class="text-sm font-medium">{{ auth()->user()->company->name }}</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2 bg-blue-700 bg-opacity-50 px-3 py-1 rounded-full">
                            <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                        </div>
                    @endif
                @endauth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white hover:bg-blue-700 px-3 py-2 rounded flex items-center">
                        <i class="fas fa-sign-out-alt mr-1"></i> {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
