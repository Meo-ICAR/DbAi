<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Chat Interface</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-4xl p-4">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-4">
                <h1 class="text-xl font-bold">Database Chat Assistant</h1>
                <p class="text-sm opacity-75">Ask questions about your database</p>
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
                        placeholder="Type your question about the database..." 
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
        
        // Scroll to bottom of chat
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Toggle debug section
        function toggleDebug() {
            const debugContent = document.getElementById('debug-content');
            const arrow = document.getElementById('debug-arrow');
            
            debugContent.classList.toggle('hidden');
            arrow.classList.toggle('fa-chevron-down');
            arrow.classList.toggle('fa-chevron-up');
        }
        
        // Add message to chat
        function addMessage(role, content) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;
            
            const bubble = document.createElement('div');
            bubble.className = `max-w-3/4 p-3 rounded-lg ${role === 'user' 
                ? 'bg-blue-600 text-white rounded-br-none' 
                : 'bg-gray-200 text-gray-800 rounded-bl-none'}`;
                
            // Format content with line breaks
            const formattedContent = content.replace(/\n/g, '<br>');
            bubble.innerHTML = formattedContent;
            
            messageDiv.appendChild(bubble);
            chatMessages.appendChild(messageDiv);
            scrollToBottom();
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
            
            // Add user message to chat
            addMessage('user', message);
            userInput.value = '';
            
            // Show typing indicator
            const typingIndicator = document.createElement('div');
            typingIndicator.id = 'typing-indicator';
            typingIndicator.className = 'flex justify-start';
            typingIndicator.innerHTML = `
                <div class="bg-gray-200 text-gray-800 p-3 rounded-lg rounded-bl-none">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            `;
            chatMessages.appendChild(typingIndicator);
            scrollToBottom();
            
            try {
                // Send message to server
                const response = await fetch('{{ route('chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message })
                });
                
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
                } else {
                    addMessage('assistant', 'Sorry, there was an error processing your request.');
                    console.error('Error:', data.message);
                }
            } catch (error) {
                document.getElementById('typing-indicator')?.remove();
                addMessage('assistant', 'Sorry, there was an error connecting to the server.');
                console.error('Error:', error);
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
            addMessage('assistant', 'Hello! I\'m your database assistant. Ask me anything about your database.');
        });
    </script>
</body>
</html>
