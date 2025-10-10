@extends('history.layout')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Save New Query</h2>
                <a href="{{ route('history.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>

            <form action="{{ route('history.store') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                        Message
                    </label>
                    <input type="text" name="message" id="message" value="{{ old('message') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                           placeholder="E.g., Count of calls by user" required>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="sqlstatement" class="block text-sm font-medium text-gray-700 mb-1">
                        SQL Statement
                    </label>
                    <textarea name="sqlstatement" id="sqlstatement" rows="6"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 font-mono text-sm"
                              placeholder="E.g., SELECT user, COUNT(*) as count FROM calls GROUP BY user" required>{{ old('sqlstatement') }}</textarea>
                    @error('sqlstatement')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="charttype" class="block text-sm font-medium text-gray-700 mb-1">
                        Chart Type
                    </label>
                    <select name="charttype" id="charttype"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="Pie Chart" {{ old('charttype') === 'Pie Chart' ? 'selected' : '' }}>Pie Chart</option>
                        <option value="Bar Chart" {{ old('charttype') === 'Bar Chart' ? 'selected' : '' }}>Bar Chart</option>
                        <option value="Line Chart" {{ old('charttype') === 'Line Chart' ? 'selected' : '' }}>Line Chart</option>
                        <option value="Table" {{ old('charttype') === 'Table' ? 'selected' : '' }}>Table</option>
                    </select>
                    @error('charttype')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('history.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Query
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add SQL syntax highlighting and tab support
        document.addEventListener('DOMContentLoaded', function() {
            const sqlTextarea = document.getElementById('sqlstatement');
            if (sqlTextarea) {
                sqlTextarea.addEventListener('keydown', function(e) {
                    if (e.key === 'Tab') {
                        e.preventDefault();
                        const start = this.selectionStart;
                        const end = this.selectionEnd;
                        
                        // Set textarea value to: text before caret + tab + text after caret
                        this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
                        
                        // Put caret at right position again
                        this.selectionStart = this.selectionEnd = start + 4;
                    }
                });
            }
        });
    </script>
@endsection
