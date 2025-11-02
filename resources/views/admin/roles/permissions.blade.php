@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-key me-2"></i>Gestisci Permessi per Ruolo: {{ $role->name }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Ruoli</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Permessi</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Torna ai Ruoli
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-tasks me-2 text-primary"></i>Assegna Permessi
            </h5>
        </div>
        <form action="{{ route('admin.roles.permissions.sync', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    @foreach($permissions->groupBy(fn($permission) => explode('.', $permission->name)[0]) as $group => $groupPermissions)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        {{ ucfirst($group) }}
                                        <div class="form-check form-switch float-end">
                                            <input class="form-check-input toggle-group" type="checkbox" 
                                                data-group="group-{{ $group }}">
                                        </div>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach($groupPermissions as $permission)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox" 
                                                type="checkbox" 
                                                name="permissions[]" 
                                                value="{{ $permission->id }}"
                                                id="permission-{{ $permission->id }}"
                                                data-group="group-{{ $group }}"
                                                {{ $role->hasPermissionTo($permission) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                {{ ucwords(str_replace('.', ' → ', $permission->name)) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer bg-white text-end border-top">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Salva Modifiche
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle all permissions in a group
        document.querySelectorAll('.toggle-group').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const group = this.dataset.group;
                const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        });

        // Update group toggle when individual checkboxes change
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const group = this.dataset.group;
                const groupCheckboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
                const groupToggle = document.querySelector(`.toggle-group[data-group="${group}"]`);
                
                const allChecked = Array.from(groupCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(groupCheckboxes).some(cb => cb.checked);
                
                groupToggle.checked = allChecked;
                groupToggle.indeterminate = someChecked && !allChecked;
            });
        });

        // Initialize group toggles on page load
        document.querySelectorAll('.toggle-group').forEach(toggle => {
            const group = toggle.dataset.group;
            const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]:checked`);
            
            if (checkboxes.length === 0) {
                toggle.checked = false;
                toggle.indeterminate = false;
            } else if (checkboxes.length === document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`).length) {
                toggle.checked = true;
                toggle.indeterminate = false;
            } else {
                toggle.checked = false;
                toggle.indeterminate = true;
            }
        });
    });
</script>
@endpush

@endsection
