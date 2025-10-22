@csrf

<div class="row mb-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Company Name *</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" 
               id="name" name="name" 
               value="{{ old('name', $company->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="email_admin" class="form-label">Admin Email *</label>
        <input type="email" class="form-control @error('email_admin') is-invalid @enderror" 
               id="email_admin" name="email_admin" 
               value="{{ old('email_admin', $company->email_admin ?? '') }}" required>
        @error('email_admin')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="urlogo" class="form-label">Logo URL</label>
        <input type="url" class="form-control @error('urlogo') is-invalid @enderror" 
               id="urlogo" name="urlogo" 
               value="{{ old('urlogo', $company->urlogo ?? '') }}">
        @error('urlogo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="url_attivazione" class="form-label">Activation URL</label>
        <input type="url" class="form-control @error('url_attivazione') is-invalid @enderror" 
               id="url_attivazione" name="url_attivazione" 
               value="{{ old('url_attivazione', $company->url_attivazione ?? '') }}">
        @error('url_attivazione')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr class="my-4">
<h5>Database Connection</h5>

<div class="row mb-3">
    <div class="col-md-4">
        <label for="db_connection" class="form-label">Connection Type *</label>
        <select class="form-select @error('db_connection') is-invalid @enderror" 
                id="db_connection" name="db_connection" required>
            <option value="mysql" {{ old('db_connection', $company->db_connection ?? '') == 'mysql' ? 'selected' : '' }}>MySQL</option>
            <option value="pgsql" {{ old('db_connection', $company->db_connection ?? '') == 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
            <option value="sqlsrv" {{ old('db_connection', $company->db_connection ?? '') == 'sqlsrv' ? 'selected' : '' }}>SQL Server</option>
        </select>
        @error('db_connection')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="db_host" class="form-label">Host *</label>
        <input type="text" class="form-control @error('db_host') is-invalid @enderror" 
               id="db_host" name="db_host" 
               value="{{ old('db_host', $company->db_host ?? '127.0.0.1') }}" required>
        @error('db_host')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-2">
        <label for="db_port" class="form-label">Port *</label>
        <input type="number" class="form-control @error('db_port') is-invalid @enderror" 
               id="db_port" name="db_port" 
               value="{{ old('db_port', $company->db_port ?? '3306') }}" required>
        @error('db_port')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-2">
        <label for="db_database" class="form-label">Database *</label>
        <input type="text" class="form-control @error('db_database') is-invalid @enderror" 
               id="db_database" name="db_database" 
               value="{{ old('db_database', $company->db_database ?? '') }}" required>
        @error('db_database')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label for="db_username" class="form-label">Username *</label>
        <input type="text" class="form-control @error('db_username') is-invalid @enderror" 
               id="db_username" name="db_username" 
               value="{{ old('db_username', $company->db_username ?? '') }}" required>
        @error('db_username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="db_password" class="form-label">Password *</label>
        <input type="password" class="form-control @error('db_password') is-invalid @enderror" 
               id="db_password" name="db_password" 
               value="{{ old('db_password', $company->db_password ?? '') }}" 
               autocomplete="new-password" required>
        @error('db_password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="db_secrete" class="form-label">Secret Key *</label>
        <div class="input-group">
            <input type="text" class="form-control @error('db_secrete') is-invalid @enderror" 
                   id="db_secrete" name="db_secrete" 
                   value="{{ old('db_secrete', $company->db_secrete ?? Str::random(32)) }}" required>
            <button type="button" class="btn btn-outline-secondary" onclick="generateSecret()">
                <i class="fas fa-sync-alt"></i>
            </button>
            @error('db_secrete')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="form-text text-muted">Used for encryption/decryption of sensitive data</small>
    </div>
</div>

@push('scripts')
<script>
    function generateSecret() {
        const secret = Math.random().toString(36).substring(2, 15) + 
                      Math.random().toString(36).substring(2, 15);
        document.getElementById('db_secrete').value = secret;
    }
</script>
@endpush
