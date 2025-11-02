@extends('layout.master')
@section('title', $title)
@section('header', $title)

@section('content')
<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if(isset($role))
        @method('PUT')
    @endif
    
    <div class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Role</label>
            <input type="text" name="name" id="name" value="{{ old('name', $role->name ?? '') }}" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
            <div class="space-y-2">
                @foreach($permissions as $permission)
                    <div class="flex items-center">
                        <input id="permission-{{ $permission->id }}" name="permissions[]" type="checkbox" 
                               value="{{ $permission->id }}"
                               @if(isset($role) && $role->hasPermissionTo($permission)) checked @endif
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="permission-{{ $permission->id }}" class="ml-2 block text-sm text-gray-900">
                            {{ $permission->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('permissions')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center justify-end space-x-4">
        <a href="{{ route('roles.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-500">Batal</a>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            {{ isset($role) ? 'Update' : 'Simpan' }}
        </button>
    </div>
</form>
@endsection
