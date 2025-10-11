<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Query History - Database Assistant</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}" class="text-xl font-bold">Database Assistant</a>
                        <a href="{{ url('/chat') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Chat</a>
                        <a href="{{ url('/history') }}" class="{{ request()->is('history*') ? 'bg-blue-700' : 'hover:bg-blue-700' }} px-3 py-2 rounded">Query History</a>
                        <a href="{{ url('/dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Dashboard</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="container mx-auto px-4 py-6">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Add any necessary JavaScript here
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to delete this history entry?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
