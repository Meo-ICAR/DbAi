@extends('layouts.app')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.roles.index') }}">Gestione Ruoli Utenti</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assegnazione Massiva Ruoli</li>
        </ol>
    </nav>

    <div class="row justify-content-between align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fas fa-users-cog me-2"></i>Assegnazione Massiva Ruoli
            </h1>
            <p class="text-muted mb-0">Assegna o rimuovi ruoli a più utenti contemporaneamente</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.users.roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Torna alla lista
            </a>
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

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.roles.process-bulk-assign') }}" method="POST" id="bulkAssignForm">
                @csrf
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Seleziona gli utenti e i ruoli su cui vuoi eseguire l'operazione. Puoi selezionare più utenti e più ruoli contemporaneamente.
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>Seleziona Utenti
                                <span class="badge bg-primary ms-2" id="selectedUsersCount">0 selezionati</span>
                            </h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllUsers" data-bs-toggle="tooltip" title="Seleziona tutti">
                                    <i class="fas fa-check-double"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllUsers" data-bs-toggle="tooltip" title="Deseleziona tutti">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="userSearch" placeholder="Cerca utenti...">
                        </div>
                        
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;" id="usersContainer">
                            @foreach($users as $user)
                                <div class="form-check user-item mb-2">
                                    <input class="form-check-input user-checkbox" type="checkbox" 
                                           name="users[]" value="{{ $user->id }}" 
                                           id="user{{ $user->id }}">
                                    <label class="form-check-label w-100" for="user{{ $user->id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                <div class="small text-muted">{{ $user->email }}</div>
                                                <div class="mt-1">
                                                    @forelse($user->roles as $role)
                                                        <span class="badge bg-{{ $role->is_system ? 'dark' : 'primary' }} me-1 mb-1">
                                                            {{ $role->name }}
                                                        </span>
                                                    @empty
                                                        <span class="badge bg-light text-muted">Nessun ruolo</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-user-tag me-2"></i>Seleziona Ruoli
                                <span class="badge bg-primary ms-2" id="selectedRolesCount">0 selezionati</span>
                            </h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllRoles" data-bs-toggle="tooltip" title="Seleziona tutti">
                                    <i class="fas fa-check-double"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllRoles" data-bs-toggle="tooltip" title="Deseleziona tutti">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="roleSearch" placeholder="Cerca ruoli...">
                        </div>
                        
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;" id="rolesContainer">
                            @foreach($roles as $role)
                                <div class="form-check role-item mb-2">
                                    <input class="form-check-input role-checkbox" type="checkbox" 
                                           name="roles[]" value="{{ $role->id }}" 
                                           id="role{{ $role->id }}">
                                    <label class="form-check-label w-100" for="role{{ $role->id }}">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">
                                                    {{ $role->name }}
                                                    @if($role->is_system)
                                                        <i class="fas fa-shield-alt text-dark ms-1" data-bs-toggle="tooltip" title="Ruolo di sistema"></i>
                                                    @endif
                                                </div>
                                                @if($role->description)
                                                    <div class="small text-muted">{{ $role->description }}</div>
                                                @endif
                                                <div class="mt-1">
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-users me-1"></i> {{ $role->users_count }} utenti
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-cogs me-2"></i>Azione da Eseguire</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="action" 
                                           id="actionAssign" value="assign" checked>
                                    <label class="form-check-label w-100 p-3 border rounded bg-light" for="actionAssign">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                                        <i class="fas fa-plus-circle fa-2x"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">Assegna Ruoli</h6>
                                                <p class="text-muted mb-0">Aggiungi i ruoli selezionati agli utenti selezionati</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="action" 
                                           id="actionRemove" value="remove">
                                    <label class="form-check-label w-100 p-3 border rounded bg-light" for="actionRemove">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-danger-subtle text-danger rounded-circle">
                                                        <i class="fas fa-minus-circle fa-2x"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">Rimuovi Ruoli</h6>
                                                <p class="text-muted mb-0">Rimuovi i ruoli selezionati dagli utenti selezionati</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmAction" required>
                                <label class="form-check-label" for="confirmAction">
                                    Confermo di voler procedere con l'operazione selezionata
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <span id="summaryText">Seleziona utenti e ruoli per continuare</span>
                    </div>
                    <div>
                        <a href="{{ route('admin.users.roles.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-1"></i> Annulla
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitButton" disabled>
                            <i class="fas fa-save me-1"></i> Applica Modifiche
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-radio {
        position: relative;
        margin-bottom: 0;
    }
    
    .card-radio .form-check-input {
        position: absolute;
        opacity: 0;
    }
    
    .card-radio .form-check-input:checked + .form-check-label {
        border-color: #0d6efd !important;
        background-color: #f8f9ff !important;
    }
    
    .card-radio .form-check-label {
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .card-radio .form-check-label:hover {
        background-color: #f8f9fa !important;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .user-item .form-check-input:checked + .form-check-label,
    .role-item .form-check-input:checked + .form-check-label {
        background-color: #f8f9fa;
        border-radius: 6px;
    }
    
    .form-check-label {
        transition: all 0.2s;
        padding: 0.5rem;
        border-radius: 6px;
    }
    
    .form-check-label:hover {
        background-color: #f8f9fa;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Select/Deselect all users
        document.getElementById('selectAllUsers').addEventListener('click', function() {
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            });
            updateSubmitButton();
        });
        
        document.getElementById('deselectAllUsers').addEventListener('click', function() {
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            });
            updateSubmitButton();
        });
        
        // Select/Deselect all roles
        document.getElementById('selectAllRoles').addEventListener('click', function() {
            document.querySelectorAll('.role-checkbox').forEach(checkbox => {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            });
            updateSubmitButton();
        });
        
        document.getElementById('deselectAllRoles').addEventListener('click', function() {
            document.querySelectorAll('.role-checkbox').forEach(checkbox => {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            });
            updateSubmitButton();
        });
        
        // User search functionality
        document.getElementById('userSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');
            
            userItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Role search functionality
        document.getElementById('roleSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const roleItems = document.querySelectorAll('.role-item');
            
            roleItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Update counters when checkboxes change
        document.querySelectorAll('.user-checkbox, .role-checkbox, #confirmAction').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateCounters();
                updateSubmitButton();
                updateSummary();
            });
        });
        
        // Radio button card selection
        document.querySelectorAll('.card-radio .form-check-input').forEach(radio => {
            radio.addEventListener('change', function() {
                updateSummary();
            });
        });
        
        // Form submission
        document.getElementById('bulkAssignForm').addEventListener('submit', function(e) {
            const selectedUsers = document.querySelectorAll('.user-checkbox:checked').length;
            const selectedRoles = document.querySelectorAll('.role-checkbox:checked').length;
            const isConfirmed = document.getElementById('confirmAction').checked;
            
            if (!isConfirmed) {
                e.preventDefault();
                alert('Per favore, conferma di voler procedere con l\'operazione selezionata.');
                return false;
            }
            
            if (selectedUsers === 0 || selectedRoles === 0) {
                e.preventDefault();
                alert('Seleziona almeno un utente e un ruolo per procedere.');
                return false;
            }
            
            const action = document.querySelector('input[name="action"]:checked').value;
            const actionText = action === 'assign' ? 'assegnare' : 'rimuovere';
            
            return confirm(`Sei sicuro di voler ${actionText} i ruoli selezionati agli utenti selezionati? Questa azione non può essere annullata.`);
        });
        
        // Update counters
        function updateCounters() {
            const selectedUsers = document.querySelectorAll('.user-checkbox:checked').length;
            const selectedRoles = document.querySelectorAll('.role-checkbox:checked').length;
            
            document.getElementById('selectedUsersCount').textContent = `${selectedUsers} selezionati`;
            document.getElementById('selectedRolesCount').textContent = `${selectedRoles} selezionati`;
            
            // Update badge colors based on selection
            document.getElementById('selectedUsersCount').className = `badge ${selectedUsers > 0 ? 'bg-primary' : 'bg-secondary'}`;
            document.getElementById('selectedRolesCount').className = `badge ${selectedRoles > 0 ? 'bg-primary' : 'bg-secondary'}`;
        }
        
        // Update submit button state
        function updateSubmitButton() {
            const selectedUsers = document.querySelectorAll('.user-checkbox:checked').length;
            const selectedRoles = document.querySelectorAll('.role-checkbox:checked').length;
            const submitButton = document.getElementById('submitButton');
            
            if (selectedUsers > 0 && selectedRoles > 0) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }
        
        // Update summary text
        function updateSummary() {
            const selectedUsers = document.querySelectorAll('.user-checkbox:checked').length;
            const selectedRoles = document.querySelectorAll('.role-checkbox:checked').length;
            const action = document.querySelector('input[name="action"]:checked').value;
            const actionText = action === 'assign' ? 'assegnare' : 'rimuovere';
            const actionIcon = action === 'assign' ? 'plus-circle' : 'minus-circle';
            
            let summaryText = '';
            
            if (selectedUsers === 0 && selectedRoles === 0) {
                summaryText = 'Seleziona utenti e ruoli per continuare';
            } else if (selectedUsers === 0) {
                summaryText = 'Seleziona almeno un utente';
            } else if (selectedRoles === 0) {
                summaryText = 'Seleziona almeno un ruolo';
            } else {
                summaryText = `
                    <i class="fas fa-${actionIcon} me-1"></i>
                    Pronto per ${actionText} <strong>${selectedRoles} ruoli</strong> a <strong>${selectedUsers} utenti</strong>
                `;
            }
            
            document.getElementById('summaryText').innerHTML = summaryText;
        }
        
        // Initialize
        updateCounters();
        updateSubmitButton();
        updateSummary();
    });
</script>
@endpush

@endsection
