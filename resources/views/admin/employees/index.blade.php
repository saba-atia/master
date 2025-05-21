@extends('dash.dash')
@section('title', 'Employee Management')
@section('contentdash')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Employee Management</h1>
        
        <div>
            @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
<a href="{{ route('admin.employees.inactive') }}" class="btn btn-outline-secondary me-2">
    <i class="fas fa-user-slash"></i> Inactive Employees ({{ $inactiveCount }})
</a>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Employee
                </a>
            @endif
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge 
                                        @if($employee->role === 'super_admin') bg-danger
                                        @elseif($employee->role === 'admin') bg-warning
                                        @elseif($employee->role === 'department_manager') bg-info
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $employee->role)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($employee->status === 'active') bg-success
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(auth()->user()->role === 'super_admin' || 
                                            (auth()->user()->role === 'admin' && $employee->role !== 'super_admin') ||
                                            (auth()->user()->role === 'department_manager' && $employee->department_id === auth()->user()->department_id))
                                            <a href="{{ route('admin.employees.edit', $employee->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            @if($employee->status === 'active')
                                                <form action="{{ route('admin.employees.deactivate', $employee->id) }}" method="POST" class="deactivate-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-user-slash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        
                                        @if(auth()->user()->role === 'super_admin' || 
                                            (auth()->user()->role === 'admin' && !in_array($employee->role, ['super_admin', 'admin'])))
                                            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    const deactivateForms = document.querySelectorAll('.deactivate-form');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the employee!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    
    deactivateForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Deactivate Employee?',
                text: "The employee will lose access to the system",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, deactivate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection