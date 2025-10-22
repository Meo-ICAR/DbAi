@extends('admin.companies.layout')

@section('title', 'Edit ' . $company->name)

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.companies.update', $company) }}" method="POST">
                @csrf
                @method('PUT')
                
                @include('admin.companies._form')
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-light">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
            
            <hr class="my-4">
            
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </div>
                <div class="card-body">
                    <h5 class="card-title">Delete Company</h5>
                    <p class="card-text">Once you delete a company, there is no going back. Please be certain.</p>
                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this company? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Company
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
