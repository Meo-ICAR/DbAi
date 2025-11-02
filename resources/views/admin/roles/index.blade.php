@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-shield me-2"></i>Gestione Ruoli
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ruoli</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Nuovo Ruolo
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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nome Ruolo</th>
                            <th>Permessi</th>
                            <th class="text-end pe-4">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($roles as $role)
                            <tr class="border-bottom">
                                <td class="align-middle ps-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-shield-alt fa-fw me-2 text-primary"></i>
                                        <div>
                                            <h6 class="mb-0">{{ $role->name }}</h6>
                                            <small class="text-muted">Creato il {{ $role->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    @if($role->permissions->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($role->permissions->take(3) as $permission)
                                                <span class="badge bg-light text-dark border">{{ $permission->name }}</span>
                                            @endforeach
                                            @if($role->permissions->count() > 3)
                                                <span class="badge bg-light text-muted">+{{ $role->permissions->count() - 3 }} altri</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Nessun permesso</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.roles.permissions', $role) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Gestisci Permessi">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        <a href="{{ route('roles.edit', $role) }}" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           data-bs-toggle="tooltip" 
                                           title="Modifica Ruolo">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Sei sicuro di voler eliminare questo ruolo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Elimina Ruolo">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-user-shield fa-2x text-muted mb-2"></i>
                                        <p class="mb-0 text-muted">Nessun ruolo trovato</p>
                                        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-plus me-1"></i> Crea il primo ruolo
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($roles->hasPages())
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Mostrati {{ $roles->firstItem() }} - {{ $roles->lastItem() }} di {{ $roles->total() }} ruoli
                    </div>
                    {{ $roles->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection
