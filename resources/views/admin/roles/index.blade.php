@extends('layout.master')
@section('title', 'Roles')
@section('header', 'Daftar Role')
@section('content')
<a href="{{ route('roles.create') }}" class="mb-4 inline-block bg-slate-900 text-white px-4 py-2 rounded">+ Tambah Role</a>
<table class="min-w-full bg-white shadow rounded">
    <thead class="bg-slate-100">
        <tr>
            <th class="text-left py-2 px-4 border border-slate-200">Nama</th>
            <th class="text-left py-2 px-4 border border-slate-200">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $role)
        <tr class="border-t">
            <td class="py-2 px-4 border border-slate-200">{{ $role->name }}</td>
            <td class="py-2 px-4 space-x-2 border border-slate-200">
                <a href="{{ route('roles.permissions', $role->id) }}" class="text-slate-900 hover:underline">Permissions</a>
                <a href="{{ route('roles.edit', $role->id) }}" class="text-slate-900 hover:underline">Edit</a>
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-slate-900 hover:underline">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
