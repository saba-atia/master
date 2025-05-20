@extends('layouts.admin')

@section('title', 'Inactive Employees')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-white border-bottom-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="mb-0">
                        <i class="fas fa-user-slash text-secondary me-2"></i>Inactive Employees
                    </h3>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-inline-block me-3">
                        <span class="badge bg-danger rounded-pill p-2">
                            <i class="fas fa-users me-1"></i>
                            <span id="inactive-count">{{ $inactiveEmployees->count() ?? 0 }}</span> Employees
                        </span>
                    </div>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-users me-1"></i> View Active Employees
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body px-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Employee</th>
                            <th>Contact</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Deactivated On</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inactiveEmployees as $employee)
                            <tr class="position-relative">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-secondary">
                                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-gray-800"><del>{{ $employee->name }}</del></h6>
                                            <small class="text-muted">{{ $employee->employee_id ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $employee->email }}</div>
                                    <small class="text-muted">{{ $employee->phone ?? 'No phone' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $employee->department->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($employee->role === 'super_admin') bg-danger
                                        @elseif($employee->role === 'admin') bg-warning text-dark
                                        @elseif($employee->role === 'department_manager') bg-info
                                        @else bg-secondary
                                        @endif">
                                        {{ ucwords(str_replace('_', ' ', $employee->role)) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $employee->updated_at->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ $employee->updated_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end">
                                        <form action="{{ route('admin.employees.activate', $employee->id) }}" method="POST" class="activate-form me-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Reactivate">
                                                <i class="fas fa-user-check me-1"></i> Activate
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Permanent Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-user-check fa-3x mb-3 text-muted"></i>
                                        <h5 class="text-muted">No Inactive Employees</h5>
                                        <p class="text-muted">All employees are currently active</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($inactiveEmployees->hasPages())
                <div class="card-footer px-3 border-top-0">
                    {{ $inactiveEmployees->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Activate confirmation with SweetAlert
    document.querySelectorAll('.activate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Activate Employee?',
                text: "This will restore the employee's access to the system",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, activate!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Delete confirmation with SweetAlert
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Permanently Delete Employee?',
                text: "This action cannot be undone. All associated data will be lost!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush