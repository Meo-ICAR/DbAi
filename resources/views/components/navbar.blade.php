@props(['page'])

<nav class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('chat.index') }}" class="text-xl font-bold">Database Assistant</a>
                <a href="{{ route('chat.index') }}" class="{{ Route::is('chat*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Chat</a>
                <a href="{{ route('history.index') }}" class="{{ Route::is('history.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Query</a>
                <a href="{{ route('history.dashboard') }}" class="{{ Route::is('dashboard*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Dashboard</a>
            </div>
            <div class="flex items-center space-x-2">
                <div x-data="{ showInfo: false }">
                    <x-info-button :page="$page ?? 'default'" />
                </div>
            </div>
        </div>
    </div>
</nav>
