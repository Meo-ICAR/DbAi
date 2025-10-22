@csrf

<!-- Company Information Section -->
<div class="mb-8">
    <div class="mb-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Company Information</h3>
        <p class="mt-1 text-sm text-gray-500">Basic details about the company.</p>
    </div>
    
    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
        <!-- Company Name -->
        <div class="sm:col-span-3">
            <label for="name" class="block text-sm font-medium text-gray-700">Company Name <span class="text-red-500">*</span></label>
            <div class="mt-1">
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $company->name ?? '') }}" 
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="mt-2 text-sm text-red-600" id="name-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Admin Email -->
        <div class="sm:col-span-3">
            <label for="email_admin" class="block text-sm font-medium text-gray-700">Admin Email <span class="text-red-500">*</span></label>
            <div class="mt-1">
                <input type="email" 
                       id="email_admin" 
                       name="email_admin" 
                       value="{{ old('email_admin', $company->email_admin ?? '') }}" 
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('email_admin') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                       required>
                @error('email_admin')
                    <p class="mt-2 text-sm text-red-600" id="email_admin-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Logo URL -->
        <div class="sm:col-span-3">
            <label for="urlogo" class="block text-sm font-medium text-gray-700">Logo URL</label>
            <div class="mt-1 flex rounded-md shadow-sm">
                <input type="url" 
                       id="urlogo" 
                       name="urlogo" 
                       value="{{ old('urlogo', $company->urlogo ?? '') }}" 
                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('urlogo') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                @if(isset($company) && $company->urlogo)
                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                        <img src="{{ $company->urlogo }}" alt="Logo preview" class="h-6 w-6 rounded">
                    </span>
                @endif
            </div>
            @error('urlogo')
                <p class="mt-2 text-sm text-red-600" id="urlogo-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Activation URL -->
        <div class="sm:col-span-3">
            <label for="url_attivazione" class="block text-sm font-medium text-gray-700">Activation URL</label>
            <div class="mt-1">
                <input type="url" 
                       id="url_attivazione" 
                       name="url_attivazione" 
                       value="{{ old('url_attivazione', $company->url_attivazione ?? '') }}" 
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('url_attivazione') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                @error('url_attivazione')
                    <p class="mt-2 text-sm text-red-600" id="url_attivazione-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

<!-- Database Connection Section -->
<div class="pt-8 border-t border-gray-200">
    <div class="mb-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Database Connection</h3>
        <p class="mt-1 text-sm text-gray-500">Database connection details for this company.</p>
    </div>
    
    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
        <!-- Connection Type -->
        <div class="sm:col-span-2">
            <label for="db_connection" class="block text-sm font-medium text-gray-700">Connection Type <span class="text-red-500">*</span></label>
            <div class="mt-1">
                <select id="db_connection" 
                        name="db_connection" 
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('db_connection') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                        required>
                    <option value="" disabled {{ old('db_connection', $company->db_connection ?? '') ? '' : 'selected' }}>Select a connection type</option>
                    <option value="mysql" {{ old('db_connection', $company->db_connection ?? '') == 'mysql' ? 'selected' : '' }}>MySQL</option>
                    <option value="pgsql" {{ old('db_connection', $company->db_connection ?? '') == 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
                    <option value="sqlsrv" {{ old('db_connection', $company->db_connection ?? '') == 'sqlsrv' ? 'selected' : '' }}>SQL Server</option>
                </select>
                @error('db_connection')
                    <p class="mt-2 text-sm text-red-600" id="db_connection-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Host -->
        <div class="sm:col-span-2">
            <label for="db_host" class="block text-sm font-medium text-gray-700">Host <span class="text-red-500">*</span></label>
            <div class="mt-1">
                <input type="text" 
                       id="db_host" 
                       name="db_host" 
                       value="{{ old('db_host', $company->db_host ?? '127.0.0.1') }}" 
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('db_host') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                       required>
                @error('db_host')
                    <p class="mt-2 text-sm text-red-600" id="db_host-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Port -->
        <div class="sm:col-span-1">
            <label for="db_port" class="block text-sm font-medium text-gray-700">Port <span class="text-red-500">*</span></label>
            <div class="mt-1">
                <input type="number" 
                       id="db_port" 
                       name="db_port" 
                       value="{{ old('db_port', $company->db_port ?? '3306') }}" 
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('db_port') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                       required>
                @error('db_port')
                    <p class="mt-2 text-sm text-red-600" id="db_port-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Database Name -->
        <div class="sm:col-span-2">
            <label for="db_database" class="block text-sm font-medium text-gray-700">Database Name <span class="text-red-500">*</span></label>
            <div class="mt-1">
                <input type="text" 
                       id="db_database" 
                       name="db_database" 
                       value="{{ old('db_database', $company->db_database ?? '') }}" 
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('db_database') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                       required>
                @error('db_database')
                    <p class="mt-2 text-sm text-red-600" id="db_database-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Username -->
        <div class="sm:col-span-2">
            <label for="db_username" class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
            <div class="mt-1">
                <input type="text" 
                       id="db_username" 
                       name="db_username" 
                       value="{{ old('db_username', $company->db_username ?? '') }}" 
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('db_username') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                       required>
                @error('db_username')
                    <p class="mt-2 text-sm text-red-600" id="db_username-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Password -->
        <div class="sm:col-span-2">
            <label for="db_password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1 flex rounded-md shadow-sm">
                <input type="password" 
                       id="db_password" 
                       name="db_password" 
                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('db_password') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                       autocomplete="new-password"
                       placeholder="{{ $company ? '••••••••' : '' }}">
                <button type="button" 
                        onclick="togglePassword('db_password')"
                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    <svg id="eyeIcon-db_password" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            @error('db_password')
                <p class="mt-2 text-sm text-red-600" id="db_password-error">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
        </div>

        <!-- Secret Key -->
        <div class="sm:col-span-2">
            <label for="db_secrete" class="block text-sm font-medium text-gray-700">Secret Key <span class="text-red-500">*</span></label>
            <div class="mt-1 flex rounded-md shadow-sm">
                <input type="password" 
                       id="db_secrete" 
                       name="db_secrete" 
                       value="{{ old('db_secrete', $company->db_secrete ?? '') }}" 
                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('db_secrete') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                       required>
                <div class="flex">
                    <button type="button" 
                            onclick="togglePassword('db_secrete')"
                            class="inline-flex items-center px-3 py-2 border border-l-0 border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <svg id="eyeIcon-db_secrete" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    <button type="button" 
                            onclick="generateSecret()"
                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 rounded-r-md">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
            @error('db_secrete')
                <p class="mt-2 text-sm text-red-600" id="db_secrete-error">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Used for encryption/decryption of sensitive data</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById(`eyeIcon-${fieldId}`);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            `;
        } else {
            input.type = 'password';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        }
    }
    
    function generateSecret() {
        const secret = Math.random().toString(36).substring(2, 15) + 
                      Math.random().toString(36).substring(2, 15) +
                      Math.random().toString(36).substring(2, 15);
        const secretField = document.getElementById('db_secrete');
        secretField.value = secret;
        
        // Auto-show the generated secret
        if (secretField.type === 'password') {
            togglePassword('db_secrete');
        }
        
        // Copy to clipboard
        secretField.select();
        document.execCommand('copy');
        
        // Show copied tooltip
        const button = event.currentTarget;
        const originalText = button.innerHTML;
        button.innerHTML = 'Copied!';
        button.classList.add('text-green-600');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('text-green-600');
        }, 2000);
    }
    
    // Update port based on database type
    document.getElementById('db_connection').addEventListener('change', function() {
        const portMap = {
            'mysql': '3306',
            'pgsql': '5432',
            'sqlsrv': '1433'
        };
        
        const portInput = document.getElementById('db_port');
        if (portMap[this.value]) {
            portInput.value = portMap[this.value];
        }
    });
</script>
@endpush
