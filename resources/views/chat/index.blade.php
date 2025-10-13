<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Database Chat Interface</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    @php
        $page = 'chat';
    @endphp
    <x-navbar :page="$page" />

    <div class="container mx-auto max-w-4xl p-4">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-4">
                <h1 class="text-xl font-bold">Database Chat Assistant</h1>
                <p class="text-sm opacity-75">Interroga il database con AI</p>
            </div>

            <!-- Chat Messages -->
            <div id="chat-messages" class="h-96 overflow-y-auto p-4 space-y-4">
                <!-- Messages will be added here -->
            </div>

            <!-- Input Area -->
            <div class="border-t p-4 bg-gray-50">
                <form id="chat-form" class="flex space-x-2">
                    <input
                        type="text"
                        id="user-input"
                        placeholder="Digita la tua domanda..."
                        class="flex-1 p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        autocomplete="off"
                    >
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Send
                    </button>
                </form>
            </div>
        </div>

        <!-- Query Results Section -->
        <div class="mt-6 bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-green-100 p-3">
                <h2 class="text-sm font-semibold text-gray-700">Query Results</h2>
            </div>
            <div class="p-4 bg-white">
                <div id="query-results" class="overflow-x-auto">
                    <!-- Results will be displayed here -->
                    <p class="text-gray-500 text-sm text-center py-4">Your query results will appear here</p>
                </div>
            </div>
        </div>

        <!-- Query Debug Section (collapsible) -->
        <div class="mt-6 bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-100 p-3 cursor-pointer" onclick="toggleDebug()">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center">
                    <span>Debug: Show/Hide SQL Queries</span>
                    <i class="fas fa-chevron-down ml-2" id="debug-arrow"></i>
                </h2>
            </div>
            <div id="debug-content" class="hidden p-4 bg-gray-50">
                <div id="query-debug" class="text-sm font-mono text-gray-700">
                    <!-- SQL queries will be shown here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        const chatForm = document.getElementById('chat-form');
        const userInput = document.getElementById('user-input');
        const chatMessages = document.getElementById('chat-messages');
        const queryDebug = document.getElementById('query-debug');

        // Smooth scroll to bottom of chat
        function scrollToBottom() {
            // Use requestAnimationFrame for smooth scrolling
            requestAnimationFrame(() => {
                chatMessages.scrollTo({
                    top: chatMessages.scrollHeight,
                    behavior: 'smooth'
                });
            });
        }

        // Handle window resize to maintain scroll position
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(scrollToBottom, 250);
        });

        // Toggle debug section
        function toggleDebug() {
            const debugContent = document.getElementById('debug-content');
            const arrow = document.getElementById('debug-arrow');

            debugContent.classList.toggle('hidden');
            arrow.classList.toggle('fa-chevron-down');
            arrow.classList.toggle('fa-chevron-up');
        }

        // Add message to chat with smooth scrolling
        function addMessage(role, content) {
            // Create message container
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'} mb-4 last:mb-0`;

            // Create message bubble
            const bubble = document.createElement('div');
            bubble.className = `max-w-3/4 p-3 rounded-lg ${role === 'user'
                ? 'bg-blue-600 text-white rounded-br-none'
                : 'bg-gray-200 text-gray-800 rounded-bl-none'}`;

            // Format content with line breaks and preserve whitespace
            const formattedContent = content
                .replace(/\n/g, '<br>')
                .replace(/  /g, '&nbsp;&nbsp;');
            
            bubble.innerHTML = formattedContent;
            messageDiv.appendChild(bubble);
            
            // Add typing indicator for assistant messages
            if (role === 'assistant') {
                const typingIndicator = document.createElement('div');
                typingIndicator.id = 'typing-indicator';
                typingIndicator.className = 'flex space-x-1 py-2 px-4';
                typingIndicator.innerHTML = `
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                `;
                
                // Add message and typing indicator to chat
                chatMessages.appendChild(messageDiv);
                chatMessages.appendChild(typingIndicator);
            } else {
                chatMessages.appendChild(messageDiv);
            }
            
            // Smooth scroll to bottom
            scrollToBottom();
            
            // Return the message div for later reference
            return messageDiv;
        }

        // Display query results in a table
        function displayQueryResults(data) {
            const resultsContainer = document.getElementById('query-results');

            // Clear previous results
            resultsContainer.innerHTML = '';

            if (!data || !Array.isArray(data) || data.length === 0) {
                resultsContainer.innerHTML = '<p class="text-gray-500 text-sm text-center py-4">No data to display</p>';
                return;
            }

            // Create table
            const table = document.createElement('table');
            table.className = 'min-w-full divide-y divide-gray-200';

            // Create table header
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');

            // Get headers from first row
            const headers = Object.keys(data[0]);
            headers.forEach(header => {
                const th = document.createElement('th');
                th.className = 'px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';
                th.textContent = header;
                headerRow.appendChild(th);
            });

            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Create table body
            const tbody = document.createElement('tbody');
            tbody.className = 'bg-white divide-y divide-gray-200';

            // Add rows
            data.forEach(row => {
                const tr = document.createElement('tr');

                headers.forEach(header => {
                    const td = document.createElement('td');
                    td.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-500';

                    // Handle different data types
                    const value = row[header];
                    if (value === null || value === undefined) {
                        td.textContent = 'NULL';
                        td.className += ' text-gray-400';
                    } else if (typeof value === 'object') {
                        td.textContent = JSON.stringify(value);
                    } else {
                        td.textContent = value;
                    }

                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            resultsContainer.appendChild(table);

            // Add result count
            const resultCount = document.createElement('div');
            resultCount.className = 'text-xs text-gray-500 mt-2';
            resultCount.textContent = `${data.length} row${data.length !== 1 ? 's' : ''} returned`;
            resultsContainer.appendChild(resultCount);
        }

        // Add query to debug section
        function addQueryDebug(queryData) {
            if (!queryData) return;

            const queryDiv = document.createElement('div');
            queryDiv.className = 'mb-4 p-3 bg-white rounded border border-gray-200';

            const timestamp = document.createElement('div');
            timestamp.className = 'text-xs text-gray-500 mb-1';
            timestamp.textContent = `Executed at: ${queryData.timestamp}`;

            const sql = document.createElement('div');
            sql.className = 'p-2 bg-gray-100 rounded font-mono text-sm mb-2 overflow-x-auto';
            sql.textContent = queryData.sql;

            const params = document.createElement('div');
            params.className = 'text-xs text-gray-600';
            params.textContent = `Parameters: ${JSON.stringify(queryData.params)}`;

            queryDiv.appendChild(timestamp);
            queryDiv.appendChild(sql);
            queryDiv.appendChild(params);

            queryDebug.prepend(queryDiv);
        }

        // Handle form submission
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const message = userInput.value.trim();
            if (!message) return;

            // Get submit button and disable it
            const submitButton = chatForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            try {
                // Disable input and button while processing
                userInput.disabled = true;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                // Add user message to chat
                addMessage('user', message);
                userInput.value = '';
                userInput.focus();

                // Show typing indicator
                const typingIndicator = document.createElement('div');
                typingIndicator.id = 'typing-indicator';
                typingIndicator.className = 'flex space-x-1 py-2 px-4';
                typingIndicator.innerHTML = `
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                `;
                chatMessages.appendChild(typingIndicator);
                scrollToBottom();

                // Get CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Send message to server
                const response = await fetch('{{ route('chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ message })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                // Remove typing indicator
                document.getElementById('typing-indicator')?.remove();

                if (data.status === 'success') {
                    // Add assistant's response to chat
                    addMessage('assistant', data.response);

                    // Add query to debug section if available
                    if (data.query) {
                        addQueryDebug(data.query);
                    }

                    // Display query results if available
                    if (data.results) {
                        displayQueryResults(data.results);
                    }
                } else {
                    addMessage('assistant', 'Spiacente, c\u00f9 avvenuto un errore: ' + (data.message || 'Errore sconosciuto'));
                    console.error('Error:', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('typing-indicator')?.remove();
                addMessage('assistant', 'Spiacente, si è verificato un errore durante l\'elaborazione della tua richiesta.');
            } finally {
                // Always re-enable the form and button
                userInput.disabled = false;
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                userInput.focus();
            }
        });

        // Allow sending message with Enter key (but allow Shift+Enter for new line)
        userInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        // Initial welcome message
        document.addEventListener('DOMContentLoaded', () => {
            addMessage('assistant', 'Ciao! Sono il tuo assistente AI per il database. Chiedimi qualsiasi cosa tu voglia sapere.');
        });
    </script>
</body>
</html>
