@props(['page'])

<nav class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('chat.index') }}" class="text-xl font-bold">Database Assistant</a>
                <a href="{{ route('dashboard') }}" class="{{ Route::is('dashboard') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Dashboard</a>
                <a href="{{ route('history.index') }}" class="{{ Route::is('history.index') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Query</a>

                <a href="{{ route('history.tables') }}" class="{{ Route::is('history.tables') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Tabelle</a>
                                <a href="{{ route('chat.index') }}" class="{{ Route::is('chat*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Chat</a>
            </div>
            <div class="flex items-center space-x-4">
                <div x-data="{ showInfo: false }">
                    <x-info-button :page="$page ?? 'default'" />
                </div>
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
