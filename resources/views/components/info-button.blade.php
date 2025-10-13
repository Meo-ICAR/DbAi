@props(['page'])

<button type="button"
        @click="showInfo = true"
        class="p-2 bg-sky-500 hover:bg-sky-600 text-white rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500"
        title="Show help">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
    </svg>
</button>

<div x-show="showInfo"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto"
         @click.away="showInfo = false">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Help & Instructions</h3>
            <button @click="showInfo = false" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="prose max-w-none">
            <!-- Debug: Current page is: {{ $page }} -->
            @php
                // For debugging - show the page name
                // Map page names to instruction views
                $instructionView = match($page) {
                    'history-index' => 'history-index',
                    'history-show' => 'history-show',
                    'history-edit' => 'history-edit',
                    'history-create' => 'history-create',
                    'history-display' => 'history-display',
                    'chat' => 'chat',
                    'dashboard' => 'dashboard',
                    default => 'test'  // Fallback to test view if no match found
                };

                // Output help content directly based on the page
                echo "<div class='p-4 bg-white border border-gray-200 rounded-lg shadow-sm text-gray-900'>";

                switch ($page) {
                    case 'history-index':
                        echo "<h3 class='text-lg font-semibold mb-3'>Query History</h3>";
                        echo "<p class='mb-3'>This page displays your saved query history. Here's what you can do:</p>";
                        echo "<ul class='list-disc pl-5 space-y-1 mb-4'>";
                        echo "<li><strong>View past queries</strong> with their execution times and status</li>";
                        echo "<li><strong>Search</strong> through your history using the search bar</li>";
                        echo "<li><strong>Filter</strong> queries by date, status, or type</li>";
                        echo "<li><strong>Click on any query</strong> to view its details and results</li>";
                        echo "<li>Use the <strong>actions menu</strong> to rerun, edit, or delete queries</li>";
                        echo "</ul>";

                        echo "<h4 class='font-semibold mb-2'>Tips:</h4>";
                        echo "<ul class='list-disc pl-5 space-y-1'>";
                        echo "<li>Use the star icon to bookmark important queries</li>";
                        echo "<li>You can export your query history using the export button</li>";
                        echo "<li>Hover over query status indicators to see more details</li>";
                        echo "</ul>";
                        break;

                    case 'chat':
                        echo "<h3 class='text-lg font-semibold mb-3'>Chat Assistant</h3>";
                        echo "<p class='mb-3'>Ask questions about your database in natural language.</p>";
                        echo "<ul class='list-disc pl-5 space-y-1'>";
                        echo "<li>Type your question in the input field and press Enter</li>";
                        echo "<li>Use specific questions for more accurate results</li>";
                        echo "<li>Previous chat history is displayed above the input</li>";
                        echo "</ul>";
                        break;

                    case 'dashboard':
                        echo "<h3 class='text-lg font-semibold mb-3'>Dashboard</h3>";
                        echo "<p class='mb-3'>View important metrics and statistics about your queries.</p>";
                        break;

                    default:
                        echo "<h3 class='text-lg font-semibold mb-3'>Help & Instructions</h3>";
                        echo "<p>No specific help available for this page. Please contact support if you need assistance.</p>";
                        echo "<p class='mt-2 text-sm text-gray-500'>Page: " . e($page) . "</p>";
                }

                echo "</div>";
            @endphp
        </div>
    </div>
</div>
