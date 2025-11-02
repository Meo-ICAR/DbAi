@extends('layout.master')
@section('title', 'Permissions')
@section('header', 'Daftar Permission')
@section('content')
<a href="{{ route('permissions.create') }}" class="mb-4 inline-block bg-slate-900 text-white px-4 py-2 rounded">+ Tambah Permission</a>
<table class="min-w-full bg-white shadow rounded">
    <thead class="bg-slate-100">
        <tr>
            <th class="text-left py-2 px-4 border border-slate-200">Nama</th>
            <th class="text-left py-2 px-4 border border-slate-200">Guard</th>
            <th class="text-left py-2 px-4 border border-slate-200">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($permissions as $permission)
        <tr class="border-t">
            <td class="py-2 px-4 border border-slate-200">{{ $permission->name }}</td>
            <td class="py-2 px-4 border border-slate-200">{{ $permission->guard_name }}</td>
            <td class="py-2 px-4 space-x-2 border border-slate-200">
                <a href="{{ route('permissions.edit', $permission->id) }}" class="text-slate-900 hover:underline">Edit</a>
                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-slate-900 hover:underline" 
                        onclick="return confirm('Are you sure you want to delete this permission?')">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $permissions->links() }}
</div>
@endsection
