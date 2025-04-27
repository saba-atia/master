@extends('dash.dash')
@section('title', 'Leave')
@section('contentdash')
<style>
    /* Custom styles for leave request page */
    .form-control-label {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .employee-view .alert {
        border-radius: 12px;
    }
    
    .employee-view .list-group-item {
        border-radius: 12px !important;
        margin-bottom: 0.75rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .hr-admin-view .table td:last-child {
        white-space: nowrap;
    }
    
    .super-admin-view .chart {
        min-height: 300px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .employee-view .card-body {
            padding: 1rem;
        }
        
        .hr-admin-view .table-responsive {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .super-admin-view .card-header h6 {
            font-size: 1rem;
        }
    }

    .badge-pending {
        background-color: #ffc107;
        color: #000;
    }
    .badge-approved {
        background-color: #28a745;
        color: #fff;
    }
    .badge-rejected {
        background-color: #dc3545;
        color: #fff;
    }
</style>

@if(auth()->user()->role === 'employee')
<!-- Employee View -->
@else
<!-- Admin/Super Admin View -->
@endif
<div class="container-fluid py-4">
    @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
    <!-- HR Admin / Super Admin View -->
    <div class="row hr-admin-view">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Leave Requests Management</h6>
                    </div>
                    <p class="text-sm mt-2 mb-0">Review and manage leave requests submitted by employees.</p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Employee</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Start Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">End Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Reason</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Created At</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Updated At</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Actions</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @foreach($leaves as $leave)
                                <tr>
                                    <td class="text-sm">{{ $leave->id }}</td>
                                    <td class="text-sm">{{ $leave->user->name }}</td>
                                    <td class="text-sm">{{ ucfirst($leave->type) }}</td>
                                    <td class="text-sm">{{ $leave->start_date->format('d/m/Y') }}</td>
                                    <td class="text-sm">{{ $leave->end_date->format('d/m/Y') }}</td>
                                    <td class="text-sm">{{ $leave->reason ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $leave->status }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td class="text-sm">{{ $leave->created_at->format('d/m/Y') }}</td>
                                    <td class="text-sm">{{ $leave->updated_at->format('d/m/Y') }}</td>
                                    <td class="text-sm">
                                        @if($leave->status === 'pending' && auth()->user()->role === 'super_admin')
                                        <form action="{{ route('leaves.updateStatus', $leave->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button …>Approve</button>
                                        </form>
                                        <form action="{{ route('leaves.updateStatus', $leave->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button …>Reject</button>
                                        </form>
                                        
                                        <form action="{{ route('leaves.updateStatus', $leave->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                        @else
                                        <span class="badge badge-{{ $leave->status }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- Employee View -->
    <div class="row employee-view">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Submit Leave Request</h6>
                    <p class="text-sm">Submit your leave request by filling in the required details.</p>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('leaves.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Employee Name</label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Leave Type</label>
                                    <select name="type" class="form-select" required>
                                        <option value="">Select Leave Type</option>
                                        <option value="annual">Annual Leave</option>
                                        <option value="sick">Sick Leave</option>
                                        <option value="emergency">Emergency Leave</option>
                                        <option value="maternity">Maternity Leave</option>
                                        <option value="paternity">Paternity Leave</option>
                                        <option value="unpaid">Unpaid Leave</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" id="startDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" id="endDate" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Reason (Optional)</label>
                                    <textarea name="reason" class="form-control" rows="3" placeholder="Brief reason for your leave"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>My Leave Status</h6>
                </div>
                <div class="card-body p-3">
                    <div class="alert alert-info">
                        <span class="text-sm">You have <strong>12</strong> days remaining for annual leave this year.</span>
                    </div>
                    <ul class="list-group">
                        @foreach($leaves as $leave)
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    @if($leave->status === 'approved')
                                    <i class="ni ni-check-bold text-white opacity-10"></i>
                                    @elseif($leave->status === 'pending')
                                    <i class="ni ni-time-alarm text-white opacity-10"></i>
                                    @else
                                    <i class="ni ni-fat-remove text-white opacity-10"></i>
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">{{ ucfirst($leave->type) }} Leave</h6>
                                    <span class="text-xs">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M Y') }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                <span class="badge badge-{{ $leave->status }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    // Script for date validation
    document.getElementById('endDate').addEventListener('change', function() {
        const startDate = new Date(document.getElementById('startDate').value);
        const endDate = new Date(this.value);
        
        if (startDate > endDate) {
            alert('End date must be after start date');
            this.value = '';
        }
    });
</script>
@endsection