@extends('dash.dash')

@section('contentdash')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Employee Management</h1>
        
        @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Employee
            </a>
        @endif
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
                                    <div class="d-flex gap-2">
                                        @if(auth()->user()->role === 'super_admin' || 
                                            (auth()->user()->role === 'admin' && $employee->role !== 'super_admin') ||
                                            (auth()->user()->role === 'department_manager' && $employee->department_id === auth()->user()->department_id))
                                            <a href="{{ route('admin.employees.edit', $employee->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        
                                        @if(auth()->user()->role === 'super_admin' || 
                                            (auth()->user()->role === 'admin' && !in_array($employee->role, ['super_admin', 'admin'])) ||
                                            (auth()->user()->role === 'department_manager' && $employee->department_id === auth()->user()->department_id))
                                            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure?')">
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
@endsection