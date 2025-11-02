@extends('layout.master')
@section('title', $title)
@section('header', $title)

@section('content')
<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if(isset($permission))
        @method('PUT')
    @endif
    
    <div class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Permission</label>
            <input type="text" name="name" id="name" value="{{ old('name', $permission->name ?? '') }}" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="contoh: create users, edit posts, etc.">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="guard_name" class="block text-sm font-medium text-gray-700">Guard Name</label>
            <select name="guard_name" id="guard_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="web" {{ (old('guard_name', $permission->guard_name ?? '') == 'web') ? 'selected' : '' }}>Web</option>
                <option value="api" {{ (old('guard_name', $permission->guard_name ?? '') == 'api') ? 'selected' : '' }}>API</option>
            </select>
            @error('guard_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center justify-end space-x-4">
        <a href="{{ route('permissions.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-500">Batal</a>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            {{ isset($permission) ? 'Update' : 'Simpan' }}
        </button>
    </div>
</form>
@endsection
