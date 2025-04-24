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
</style>
<div class="container-fluid py-4">
    @if(auth()->user()->role === 'admin')
    <!-- HR Admin View -->
    <div class="row hr-admin-view">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Leave Requests Management</h6>
                        <div class="d-flex">
                            <button class="btn btn-sm btn-outline-secondary me-2">
                                <i class="ni ni-archive-2"></i> Export
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                                    <i class="ni ni-filter"></i> Filters
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Pending</a></li>
                                    <li><a class="dropdown-item" href="#">Approved</a></li>
                                    <li><a class="dropdown-item" href="#">Rejected</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm mt-2 mb-0">Review and manage leave requests submitted by employees. You can approve or reject based on company policy and leave availability.</p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Employee</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Leave Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Start Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">End Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Days</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="../assets/img/team-2.jpg" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">John Michael</h6>
                                                <p class="text-xs text-secondary mb-0">IT Department</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-sm">Annual Leave</td>
                                    <td class="text-sm">15/06/2023</td>
                                    <td class="text-sm">18/06/2023</td>
                                    <td class="text-sm">4</td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                    </td>
                                    <td class="text-sm">
                                        <button class="btn btn-sm btn-success me-2">Approve</button>
                                        <button class="btn btn-sm btn-danger">Reject</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="../assets/img/team-1.jpg" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">Sarah Johnson</h6>
                                                <p class="text-xs text-secondary mb-0">HR Department</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-sm">Sick Leave</td>
                                    <td class="text-sm">20/06/2023</td>
                                    <td class="text-sm">21/06/2023</td>
                                    <td class="text-sm">2</td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-success">Approved</span>
                                    </td>
                                    <td class="text-sm">
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Approved</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 pt-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="javascript:;" tabindex="-1">
                                        <i class="ni ni-bold-left"></i>
                                    </a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="javascript:;">1</a></li>
                                <li class="page-item"><a class="page-link" href="javascript:;">2</a></li>
                                <li class="page-item"><a class="page-link" href="javascript:;">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:;">
                                        <i class="ni ni-bold-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif(auth()->user()->role === 'super_admin')
    <!-- Super Admin (Management) View -->
    <div class="row super-admin-view">
        <div class="col-xl-8">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>Leave Analytics Overview</h6>
                    <p class="text-sm">View and analyze leave reports at the organization level. You can monitor trends, statistics, and approve leaves as needed.</p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="leave-chart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Leave Statistics</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-single-02 text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Pending Requests</h6>
                                    <span class="text-xs">Total: <span class="font-weight-bold">12</span></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                <button class="btn btn-sm btn-outline-primary">Review</button>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-calendar-grid-58 text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Approved This Month</h6>
                                    <span class="text-xs">Total: <span class="font-weight-bold">24</span></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                <button class="btn btn-sm btn-outline-success">View</button>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-badge text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Department Breakdown</h6>
                                    <span class="text-xs">IT: 8, HR: 5, Finance: 3</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                <button class="btn btn-sm btn-outline-info">Details</button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4 super-admin-view">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Recent Leave Requests</h6>
                        <div class="d-flex">
                            <input type="date" class="form-control form-control-sm me-2">
                            <select class="form-select form-select-sm" style="width: 150px;">
                                <option>All Departments</option>
                                <option>IT</option>
                                <option>HR</option>
                                <option>Finance</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Employee</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dates</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Reason</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="../assets/img/team-3.jpg" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">Alex Smith</h6>
                                                <p class="text-xs text-secondary mb-0">IT Department</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-sm">Annual</td>
                                    <td class="text-sm">15-18 Jun</td>
                                    <td class="text-sm">Family vacation</td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success me-1">Approve</button>
                                        <button class="btn btn-sm btn-danger">Reject</button>
                                    </td>
                                </tr>
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
                    <p class="text-sm">Submit your leave request by filling in the required details. Your request will be reviewed by the HR team and approved soon.</p>
                </div>
                <div class="card-body p-3">
                    <form id="leaveRequestForm">
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
                                    <select class="form-select">
                                        <option>Annual Leave</option>
                                        <option>Sick Leave</option>
                                        <option>Emergency Leave</option>
                                        <option>Maternity/Paternity</option>
                                        <option>Unpaid Leave</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Reason (Optional)</label>
                                    <textarea class="form-control" rows="3" placeholder="Brief reason for your leave"></textarea>
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
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-check-bold text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Approved</h6>
                                    <span class="text-xs">15-18 June 2023</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                <span class="badge bg-gradient-success">Approved</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-time-alarm text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Pending</h6>
                                    <span class="text-xs">25-28 June 2023</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                <span class="badge bg-gradient-warning">Pending</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-fat-remove text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Rejected</h6>
                                    <span class="text-xs">5-7 July 2023</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                <span class="badge bg-gradient-danger">Rejected</span>
                            </div>
                        </li>
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
    
    // Form submission
    document.getElementById('leaveRequestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Leave request submitted successfully!');
        this.reset();
    });
</script>

<!-- Modals would go here -->

@endsection



