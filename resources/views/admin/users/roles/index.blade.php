@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-shield me-2"></i>Gestione Ruoli e Permessi
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ruoli</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.roles.bulk-assign') }}" class="btn btn-primary">
                <i class="fas fa-users me-1"></i> Assegnazione Massiva
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="fas fa-plus-circle me-1"></i> Nuovo Ruolo
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Errore!</strong> Si sono verificati i seguenti errori:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search and Filter Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2 text-primary"></i>Filtri di Ricerca
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.roles.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label fw-medium">Cerca Utente</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nome, cognome o email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label fw-medium">Ruolo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-tag text-muted"></i></span>
                        <select class="form-select" id="role" name="role">
                            <option value="">Tutti i ruoli</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                    @if(isset($role->users_count))
                                        <span class="text-muted">({{ $role->users_count }})</span>
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label fw-medium">Stato Utente</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-check text-muted"></i></span>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tutti gli stati</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                <i class="fas fa-circle-check text-success me-1"></i> Attivi
                            </option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                <i class="fas fa-ban text-danger me-1"></i> Disattivati
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex w-100 gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1" data-bs-toggle="tooltip" title="Applica filtri">
                            <i class="fas fa-filter me-1"></i> Applica
                        </button>
                        <a href="{{ route('admin.users.roles.index') }}" class="btn btn-outline-secondary" 
                           data-bs-toggle="tooltip" title="Azzera filtri">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- User Roles Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-users-cog me-2 text-primary"></i>Utenti e Ruoli
                </h5>
                <div class="text-muted small">
                    <span class="me-2">Totale: <strong>{{ $users->total() }}</strong> utenti</span>
                    <span class="badge bg-primary">{{ $users->count() }} visualizzati</span>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">
                            <div class="form-check
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th class="ps-3">Utente</th>
                        <th>Email</th>
                        <th>Ruoli</th>
                        <th class="text-end pe-4">Azioni</th>
                    </tr>
                </thead>
                    <tbody class="border-top-0">
                        @forelse($users as $user)
                            <tr class="border-bottom">
                                <td class="ps-4">
                                    <div class="form-check">
                                        <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">ID: {{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" 
                                         data-bs-toggle="tooltip" title="{{ $user->email }}">
                                        {{ $user->email }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($user->roles as $role)
                                            <span class="badge bg-{{ $role->is_system ? 'dark' : 'primary' }} d-flex align-items-center" 
                                                  data-bs-toggle="tooltip" 
                                                  title="{{ $role->description ?? 'Nessuna descrizione' }}">
                                                {{ $role->name }}
                                                @if($role->is_system)
                                                    <i class="fas fa-shield-alt ms-1"></i>
                                                @endif
                                            </span>
                                        @empty
                                            <span class="badge bg-light text-muted">Nessun ruolo</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="tooltip" 
                                            title="Modifica ruoli utente"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editRolesModal{{ $user->id }}">
                                        <i class="fas fa-user-edit"></i>
                                        <span class="d-none d-md-inline ms-1">Gestisci</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Roles Modal -->
                            <div class="modal fade" id="editRolesModal{{ $user->id }}" tabindex="-1" 
                                 aria-labelledby="editRolesModalLabel{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.users.roles.update', $user) }}" method="POST" id="roleForm{{ $user->id }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title" id="editRolesModalLabel{{ $user->id }}">
                                                    <i class="fas fa-user-tag me-2"></i>Gestisci Ruoli per {{ $user->name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Seleziona i ruoli da assegnare all'utente. I ruoli con <i class="fas fa-shield-alt text-dark"></i> sono di sistema e non possono essere modificati.
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Ruoli Disponibili</label>
                                                            <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                                                @foreach($roles->where('is_system', false) as $role)
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input role-checkbox" 
                                                                               type="checkbox" 
                                                                               name="roles[]" 
                                                                               value="{{ $role->id }}" 
                                                                               id="user{{ $user->id }}Role{{ $role->id }}"
                                                                               {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                                                        <label class="form-check-label w-100" for="user{{ $user->id }}Role{{ $role->id }}">
                                                                            <span class="fw-semibold">{{ $role->name }}</span>
                                                                            @if($role->description)
                                                                                <small class="d-block text-muted">{{ $role->description }}</small>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Ruoli di Sistema</label>
                                                            <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                                                @forelse($roles->where('is_system', true) as $role)
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input" 
                                                                               type="checkbox" 
                                                                               disabled
                                                                               {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                                                        <label class="form-check-label w-100">
                                                                            <span class="fw-semibold">
                                                                                {{ $role->name }}
                                                                                <i class="fas fa-shield-alt text-dark ms-1"></i>
                                                                            </span>
                                                                            @if($role->description)
                                                                                <small class="d-block text-muted">{{ $role->description }}</small>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @empty
                                                                    <div class="text-muted small">Nessun ruolo di sistema disponibile</div>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="border-top pt-3 mt-3">
                                                    <h6>Permessi Totali</h6>
                                                    <div class="d-flex flex-wrap gap-2" id="userPermissions{{ $user->id }}">
                                                        @php
                                                            $allPermissions = collect();
                                                            foreach($user->roles as $role) {
                                                                $allPermissions = $allPermissions->merge($role->permissions);
                                                            }
                                                            $uniquePermissions = $allPermissions->unique('id');
                                                        @endphp
                                                        
                                                        @forelse($uniquePermissions as $permission)
                                                            <span class="badge bg-info text-dark">
                                                                {{ $permission->name }}
                                                            </span>
                                                        @empty
                                                            <span class="text-muted small">Nessun permesso associato</span>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i> Annulla
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i> Salva Modifiche
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-users-slash fa-2x text-muted mb-2"></i>
                                        <p class="mb-0">Nessun utente trovato</p>
                                        <small class="text-muted">Prova a modificare i filtri di ricerca</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrati {{ $users->firstItem() }} - {{ $users->lastItem() }} di {{ $users->total() }} utenti
                        </div>
                        <div>
                            {{ $users->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-sm {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .role-checkbox:checked + label {
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    
    .form-check-label {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    
    .form-check-label:hover {
        background-color: #f8f9fa;
    }
    
    .table th {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Handle role selection changes to update permissions preview
        document.querySelectorAll('.role-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const userId = this.id.replace(/\D/g, '');
                updatePermissionsPreview(userId);
            });
        });
    });
    
    // Function to update permissions preview
    function updatePermissionsPreview(userId) {
        const form = document.getElementById(`roleForm${userId}`);
        const formData = new FormData(form);
        const selectedRoles = [];
        
        // Get all checked roles
        formData.getAll('roles[]').forEach(roleId => {
            selectedRoles.push(parseInt(roleId));
        });
        
        // This would typically be an AJAX call to get permissions for selected roles
        // For now, we'll just show a loading state
        const permissionsContainer = document.getElementById(`userPermissions${userId}`);
        permissionsContainer.innerHTML = `
            <div class="d-flex justify-content-center w-100 py-2">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Caricamento...</span>
                </div>
                <span class="ms-2">Aggiornamento permessi...</span>
            </div>
        `;
        
        // Simulate API call delay
        setTimeout(() => {
            // In a real implementation, you would fetch the permissions from the server
            // based on the selected roles and update the UI accordingly
            const permissions = [];
            
            // This is a simplified example - in a real app, you'd get this from the server
            const rolePermissions = {
                @foreach($roles as $role)
                    {{ $role->id }}: [
                        @foreach($role->permissions as $permission)
                            { id: {{ $permission->id }}, name: '{{ $permission->name }}' },
                        @endforeach
                    ],
                @endforeach
            };
            
            // Collect all unique permissions from selected roles
            const allPermissions = [];
            selectedRoles.forEach(roleId => {
                if (rolePermissions[roleId]) {
                    rolePermissions[roleId].forEach(permission => {
                        if (!allPermissions.some(p => p.id === permission.id)) {
                            allPermissions.push(permission);
                        }
                    });
                }
            });
            
            // Update the UI
            if (allPermissions.length > 0) {
                const permissionsHtml = allPermissions.map(permission => 
                    `<span class="badge bg-info text-dark">${permission.name}</span>`
                ).join('');
                permissionsContainer.innerHTML = permissionsHtml;
            } else {
                permissionsContainer.innerHTML = '<span class="text-muted small">Nessun permesso associato</span>';
            }
        }, 500);
    }
    
    // Confirm before removing a role
    function confirmRoleRemoval(form) {
        if (confirm('Sei sicuro di voler rimuovere questo ruolo? L\'utente potrebbe perdere l\'accesso a determinate funzionalità.')) {
            form.submit();
        }
        return false;
    }
</script>
@endpush

@endsection
