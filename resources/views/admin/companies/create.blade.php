@extends('admin.companies.layout')

@section('title', 'Create New Company')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.companies.store') }}" method="POST">
                @include('admin.companies._form')
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Company
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
