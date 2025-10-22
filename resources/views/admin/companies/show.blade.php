@extends('admin.companies.layout')

@section('title', $company->name)

@section('actions')
    <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a href="{{ route('admin.companies.index') }}" class="btn btn-light ms-2">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-2 fw-bold">ID:</div>
                <div class="col-md-10">{{ $company->id }}</div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-2 fw-bold">Name:</div>
                <div class="col-md-10">{{ $company->name }}</div>
            </div>
            
            @if($company->urlogo)
                <div class="row mb-4">
                    <div class="col-md-2 fw-bold">Logo:</div>
                    <div class="col-md-10">
                        <img src="{{ $company->urlogo }}" alt="Company Logo" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                </div>
            @endif
            
            <div class="row mb-4">
                <div class="col-md-2 fw-bold">Admin Email:</div>
                <div class="col-md-10">
                    <a href="mailto:{{ $company->email_admin }}">{{ $company->email_admin }}</a>
                </div>
            </div>
            
            @if($company->url_attivazione)
                <div class="row mb-4">
                    <div class="col-md-2 fw-bold">Activation URL:</div>
                    <div class="col-md-10">
                        <a href="{{ $company->url_attivazione }}" target="_blank">{{ $company->url_attivazione }}</a>
                    </div>
                </div>
            @endif
            
            <hr>
            <h5 class="mb-4">Database Connection</h5>
            
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Connection Type:</div>
                <div class="col-md-9 text-uppercase">{{ $company->db_connection }}</div>
            </div>
            
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Host:</div>
                <div class="col-md-9">{{ $company->db_host }}:{{ $company->db_port }}</div>
            </div>
            
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Database:</div>
                <div class="col-md-9">{{ $company->db_database }}</div>
            </div>
            
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Username:</div>
                <div class="col-md-9">{{ $company->db_username }}</div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-3 fw-bold">Secret Key:</div>
                <div class="col-md-9">
                    <div class="input-group">
                        <input type="password" class="form-control" value="{{ $company->db_secrete }}" id="secretKey" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSecret()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3 fw-bold">Created:</div>
                <div class="col-md-9">{{ $company->created_at->format('M d, Y H:i') }}</div>
            </div>
            
            @if($company->updated_at->ne($company->created_at))
                <div class="row">
                    <div class="col-md-3 fw-bold">Last Updated:</div>
                    <div class="col-md-9">{{ $company->updated_at->format('M d, Y H:i') }}</div>
                </div>
            @endif
        </div>
    </div>
    
    @push('scripts')
    <script>
        function toggleSecret() {
            const input = document.getElementById('secretKey');
            const button = event.currentTarget;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
    @endpush
@endsection
