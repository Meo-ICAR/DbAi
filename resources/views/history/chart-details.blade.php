@extends('history.layout')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">
                    {{ $history->message }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Filtered by: <span class="font-medium">{{ $filterColumn }} = {{ $filterValue }}</span>
                </p>
            </div>
            <a href="{{ route('history.show', $parentHistory) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-1"></i> Back to Parent Chart
            </a>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="font-mono text-sm bg-gray-100 p-3 rounded overflow-x-auto">
                {{ $history->sqlstatement }}
            </div>
        </div>

        @if(!empty($results))
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(array_keys($results[0] ?? []) as $column)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $column }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($results as $row)
                            <tr class="hover:bg-gray-50">
                                @foreach($row as $value)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $value }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-database text-4xl mb-4"></i>
                <p>No data found for this selection.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug panel functionality
    const debugPanel = document.createElement('div');
    debugPanel.id = 'debug-panel';
    debugPanel.className = 'fixed bottom-0 right-0 w-1/3 h-1/3 bg-black bg-opacity-90 text-green-400 font-mono text-xs p-4 overflow-auto z-50 border-t border-l border-gray-700 hidden';
    debugPanel.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <h3 class="font-bold">Debug Console</h3>
            <div class="flex space-x-2">
                <button id="clear-debug" class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Clear</button>
                <button id="close-debug" class="text-xs bg-gray-600 text-white px-2 py-1 rounded hover:bg-gray-700">×</button>
            </div>
        </div>
        <div id="debug-content" class="space-y-1"></div>
    `;
    document.body.appendChild(debugPanel);

    const debugContent = document.getElementById('debug-content');
    const showDebugBtn = document.createElement('button');
    showDebugBtn.id = 'show-debug';
    showDebugBtn.className = 'fixed bottom-4 right-4 bg-blue-600 text-white p-2 rounded-full shadow-lg hover:bg-blue-700 z-50';
    showDebugBtn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    `;
    document.body.appendChild(showDebugBtn);

    // Override console.log to also write to our debug panel
    const originalConsoleLog = console.log;
    const originalConsoleError = console.error;
    const originalConsoleWarn = console.warn;
    const originalConsoleGroup = console.group;
    const originalConsoleGroupEnd = console.groupEnd;
    
    console.log = function(...args) {
        originalConsoleLog.apply(console, args);
        logToPanel('log', ...args);
    };
    
    console.error = function(...args) {
        originalConsoleError.apply(console, args);
        logToPanel('error', ...args);
    };
    
    console.warn = function(...args) {
        originalConsoleWarn.apply(console, args);
        logToPanel('warn', ...args);
    };
    
    console.group = function(label) {
        originalConsoleGroup.apply(console, arguments);
        logToPanel('group', label);
    };
    
    console.groupEnd = function() {
        originalConsoleGroupEnd.apply(console, arguments);
        logToPanel('groupEnd');
    };
    
    function logToPanel(type, ...args) {
        if (!debugContent) return;
        
        const line = document.createElement('div');
        line.className = `flex ${type === 'error' ? 'text-red-400' : type === 'warn' ? 'text-yellow-400' : 'text-green-400'}`;
        
        if (type === 'group') {
            line.textContent = `▶ ${args[0]}`;
            line.className += ' font-bold cursor-pointer';
            line.onclick = function() {
                this.nextElementSibling.classList.toggle('hidden');
                this.textContent = this.textContent.startsWith('▶') 
                    ? this.textContent.replace('▶', '▼') 
                    : this.textContent.replace('▼', '▶');
            };
        } else if (type === 'groupEnd') {
            return;
        } else {
            const content = args.map(arg => {
                if (typeof arg === 'object') {
                    try {
                        return JSON.stringify(arg, null, 2);
                    } catch (e) {
                        return String(arg);
                    }
                }
                return String(arg);
            }).join(' ');
            
            line.textContent = `[${new Date().toISOString()}] ${content}`;
        }
        
        debugContent.appendChild(line);
        debugContent.scrollTop = debugContent.scrollHeight;
    }
    
    // Toggle debug panel
    showDebugBtn.addEventListener('click', () => {
        debugPanel.classList.toggle('hidden');
    });
    
    document.getElementById('close-debug').addEventListener('click', () => {
        debugPanel.classList.add('hidden');
    });
    
    document.getElementById('clear-debug').addEventListener('click', () => {
        debugContent.innerHTML = '';
    });
    
    // Make panel draggable
    let isDragging = false;
    let offsetX, offsetY;
    
    debugPanel.querySelector('h3').addEventListener('mousedown', (e) => {
        isDragging = true;
        offsetX = e.clientX - debugPanel.getBoundingClientRect().left;
        offsetY = e.clientY - debugPanel.getBoundingClientRect().top;
        debugPanel.style.cursor = 'grabbing';
    });
    
    document.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        
        const x = e.clientX - offsetX;
        const y = e.clientY - offsetY;
        
        const maxX = window.innerWidth - debugPanel.offsetWidth;
        const maxY = window.innerHeight - debugPanel.offsetHeight;
        
        debugPanel.style.left = `${Math.min(Math.max(0, x), maxX)}px`;
        debugPanel.style.top = `${Math.min(Math.max(0, y), maxY)}px`;
    });
    
    document.addEventListener('mouseup', () => {
        isDragging = false;
        debugPanel.style.cursor = 'default';
    });
    
    // Initial log
    console.log('Debug panel initialized. Click the (i) button in the bottom right to show/hide.');
    
    // Any additional JavaScript for the details view can go here
});
</script>
@endpush

@endsection
