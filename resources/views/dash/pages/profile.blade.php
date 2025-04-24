@extends('dash.dash')
@section('title', 'Profile')
@section('contentdash')
<style>
    /* Profile Picture Styles */
    .profile-picture-container {
        position: relative;
        width: fit-content;
        margin: 0 auto;
    }
    
    .profile-picture {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    .profile-picture-lg {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }
    
    .profile-picture-edit {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: var(--primary-color);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .profile-picture-edit:hover {
        background: var(--primary-hover);
        transform: scale(1.1);
    }
    
    /* Stat Cards */
    .stat-card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    /* Progress Bars */
    .progress {
        height: 8px;
        border-radius: 4px;
    }
    
    /* Role-specific adjustments */
    .hr-admin-view .form-control, 
    .hr-admin-view .form-select {
        background-color: #f8f9fa;
    }
    
    .employee-view .alert {
        border-radius: 12px;
        background-color: rgba(17, 205, 239, 0.1);
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .profile-picture {
            width: 100px;
            height: 100px;
        }
        
        .profile-picture-lg {
            width: 120px;
            height: 120px;
        }
    }
    
    @media (max-width: 768px) {
        .profile-card {
            margin-bottom: 1.5rem;
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
                        <h6>Employee Profile Management</h6>
                        <button class="btn btn-sm btn-primary" id="saveChangesBtn">
                            <i class="ni ni-check-bold"></i> Save Changes
                        </button>
                    </div>
                    <p class="text-sm mt-2 mb-0">View and update employee information. You can modify personal data, employment status, and upload profile pictures. You can also view the employee's leave and absence records.</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card profile-card">
                              
                                    <h5 class="mt-3 mb-1" id="employeeName">John Michael</h5>
                                    <p class="text-sm text-muted mb-2" id="employeeJob">Senior Developer</p>
                                    <span class="badge bg-gradient-success" id="employeeStatus">Active</span>
                                    
                                    <div class="d-flex justify-content-center mt-3">
                                        <div class="px-3 text-center">
                                            <h6 class="mb-0">24</h6>
                                            <p class="text-xs">Leave Days</p>
                                        </div>
                                        <div class="px-3 text-center">
                                            <h6 class="mb-0">3.8</h6>
                                            <p class="text-xs">Avg. Rating</p>
                                        </div>
                                        <div class="px-3 text-center">
                                            <h6 class="mb-0">5</h6>
                                            <p class="text-xs">Projects</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6>Quick Actions</h6>
                                </div>
                                <div class="card-body p-3">
                                    <button class="btn btn-sm btn-outline-primary w-100 mb-2">View Leave History</button>
                                    <button class="btn btn-sm btn-outline-secondary w-100 mb-2">Generate Report</button>
                                    <button class="btn btn-sm btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#changeStatusModal">Change Status</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Personal Information</h6>
                                </div>
                                <div class="card-body pt-0">
                                    <form id="employeeForm">
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Full Name</label>
                                                    <input type="text" class="form-control" value="John Michael" id="fullName">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Email</label>
                                                    <input type="email" class="form-control" value="john@company.com" id="email">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Job Title</label>
                                                    <input type="text" class="form-control" value="Senior Developer" id="jobTitle">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Department</label>
                                                    <select class="form-select" id="department">
                                                        <option>IT</option>
                                                        <option>HR</option>
                                                        <option>Finance</option>
                                                        <option>Operations</option>
                                                        <option>Marketing</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Hire Date</label>
                                                    <input type="date" class="form-control" value="2018-05-15" id="hireDate">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Employment Status</label>
                                                    <select class="form-select" id="employmentStatus">
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                        <option value="on_leave">On Leave</option>
                                                        <option value="probation">Probation</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="form-control-label">HR Notes</label>
                                                    <textarea class="form-control" rows="3" id="hrNotes">Excellent performer with strong technical skills. Promoted last year.</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="card mt-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6>Leave & Absence Records</h6>
                                    <button class="btn btn-sm btn-outline-primary">View All</button>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dates</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Days</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-sm">Annual Leave</td>
                                                    <td class="text-sm">15-18 Jun 2023</td>
                                                    <td class="text-sm">4</td>
                                                    <td><span class="badge bg-gradient-success">Approved</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-sm">Sick Leave</td>
                                                    <td class="text-sm">5 Jul 2023</td>
                                                    <td class="text-sm">1</td>
                                                    <td><span class="badge bg-gradient-success">Approved</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-sm">Emergency</td>
                                                    <td class="text-sm">12 Aug 2023</td>
                                                    <td class="text-sm">1</td>
                                                    <td><span class="badge bg-gradient-warning">Pending</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif(auth()->user()->role === 'super_admin')
    <!-- Super Admin (Management) View -->
    <div class="row super-admin-view">
        <div class="col-lg-4">
            <div class="card profile-card">
                <div class="card-body text-center">
                    <h4 id="employeeName">John Michael</h4>
                    <p class="text-lg text-muted mb-2" id="employeeJob">Senior Developer - IT Department</p>
                    <span class="badge bg-gradient-success mb-3" id="employeeStatus">Active</span>
                    
                    <div class="d-flex justify-content-center">
                        <div class="px-3 text-center">
                            <h5 class="mb-0">24</h5>
                            <p class="text-sm">Leave Days</p>
                        </div>
                        <div class="px-3 text-center">
                            <h5 class="mb-0">3.8</h5>
                            <p class="text-sm">Avg. Rating</p>
                        </div>
                        <div class="px-3 text-center">
                            <h5 class="mb-0">5</h5>
                            <p class="text-sm">Projects</p>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="text-start">
                        <div class="d-flex mb-2">
                            <div style="width: 120px;"><strong>Email:</strong></div>
                            <div>john@company.com</div>
                        </div>
                        <div class="d-flex mb-2">
                            <div style="width: 120px;"><strong>Hire Date:</strong></div>
                            <div>May 15, 2018</div>
                        </div>
                        <div class="d-flex mb-2">
                            <div style="width: 120px;"><strong>Tenure:</strong></div>
                            <div>5 years 3 months</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h6>Quick Stats</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Attendance Rate</span>
                        <span class="fw-bold">96.4%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-gradient-success" role="progressbar" style="width: 96.4%;"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4 mb-3">
                        <span>Project Completion</span>
                        <span class="fw-bold">88%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-gradient-info" role="progressbar" style="width: 88%;"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4 mb-3">
                        <span>On-Time Delivery</span>
                        <span class="fw-bold">92%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-gradient-primary" role="progressbar" style="width: 92%;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Employee Detailed Overview</h6>
                    <p class="text-sm">View detailed employee information at the organizational level. You can track all requests and leaves, plus performance and project-related statistics.</p>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card stat-card mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-0">Current Projects</h6>
                                            <h3 class="mb-0">3</h3>
                                        </div>
                                        <div class="icon icon-shape bg-gradient-primary text-center rounded-circle">
                                            <i class="ni ni-briefcase-24 text-white opacity-10"></i>
                                        </div>
                                    </div>
                                    <p class="text-sm mt-3 mb-0">2 projects on track, 1 delayed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card stat-card mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-0">Leave Balance</h6>
                                            <h3 class="mb-0">12/24</h3>
                                        </div>
                                        <div class="icon icon-shape bg-gradient-success text-center rounded-circle">
                                            <i class="ni ni-calendar-grid-58 text-white opacity-10"></i>
                                        </div>
                                    </div>
                                    <p class="text-sm mt-3 mb-0">50% of annual leave used</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Performance Metrics</h6>
                            <select class="form-select form-select-sm" style="width: 150px;">
                                <option>Last 6 Months</option>
                                <option>Last Year</option>
                                <option>All Time</option>
                            </select>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="performance-chart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Recent Leaves</h6>
                                </div>
                                <div class="card-body p-3">
                                    <ul class="list-group">
                                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                                    <i class="ni ni-calendar-grid-58 text-white opacity-10"></i>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Annual Leave</h6>
                                                    <span class="text-xs">15-18 Jun 2023 (4 days)</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                                <span class="badge bg-gradient-success">Approved</span>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                                    <i class="ni ni-single-02 text-white opacity-10"></i>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Sick Leave</h6>
                                                    <span class="text-xs">5 Jul 2023 (1 day)</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                                <span class="badge bg-gradient-success">Approved</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Assigned Projects</h6>
                                </div>
                                <div class="card-body p-3">
                                    <ul class="list-group">
                                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                                    <i class="ni ni-laptop text-white opacity-10"></i>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">CRM System Upgrade</h6>
                                                    <span class="text-xs">Due: 15 Oct 2023</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                                <span class="badge bg-gradient-info">In Progress</span>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                                    <i class="ni ni-mobile-button text-white opacity-10"></i>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Mobile App Redesign</h6>
                                                    <span class="text-xs">Due: 30 Nov 2023</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                                <span class="badge bg-gradient-success">On Track</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- Employee View -->
    <div class="row employee-view">
        <div class="col-lg-4">
            <div class="card profile-card">
                <div class="card-body text-center">
                  
                    <h4 class="mt-3 mb-1">{{ auth()->user()->name }}</h4>
                    <p class="text-lg text-muted mb-2">Senior Developer - IT Department</p>
                    <span class="badge bg-gradient-success mb-3">Active</span>
                    
                    <div class="d-flex justify-content-center">
                        <div class="px-3 text-center">
                            <h5 class="mb-0">12</h5>
                            <p class="text-sm">Remaining Leave</p>
                        </div>
                        <div class="px-3 text-center">
                            <h5 class="mb-0">5</h5>
                            <p class="text-sm">Years</p>
                        </div>
                        <div class="px-3 text-center">
                            <h5 class="mb-0">3.8</h5>
                            <p class="text-sm">Rating</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h6>Quick Info</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex mb-3">
                        <div style="width: 100px;"><strong>Email:</strong></div>
                        <div>{{ auth()->user()->email }}</div>
                    </div>
                    <div class="d-flex mb-3">
                        <div style="width: 100px;"><strong>Job Title:</strong></div>
                        <div>Senior Developer</div>
                    </div>
                    <div class="d-flex mb-3">
                        <div style="width: 100px;"><strong>Department:</strong></div>
                        <div>IT</div>
                    </div>
                    <div class="d-flex mb-3">
                        <div style="width: 100px;"><strong>Hire Date:</strong></div>
                        <div>May 15, 2018</div>
                    </div>
                    <div class="d-flex">
                        <div style="width: 100px;"><strong>Status:</strong></div>
                        <div><span class="badge bg-gradient-success">Active</span></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>My Profile</h6>
                    <p class="text-sm">View your personal information and employment status. You can update your profile picture and track your leave and absence history.</p>
                </div>
                <div class="card-body p-3">
                    <div class="alert alert-info">
                        <span class="text-sm">You have <strong>12</strong> days remaining for annual leave this year.</span>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Recent Leave Requests</h6>
                            <button class="btn btn-sm btn-outline-primary">Request Leave</button>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dates</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Days</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-sm">Annual Leave</td>
                                            <td class="text-sm">15-18 Jun 2023</td>
                                            <td class="text-sm">4</td>
                                            <td><span class="badge bg-gradient-success">Approved</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm">Sick Leave</td>
                                            <td class="text-sm">5 Jul 2023</td>
                                            <td class="text-sm">1</td>
                                            <td><span class="badge bg-gradient-success">Approved</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm">Emergency</td>
                                            <td class="text-sm">12 Aug 2023</td>
                                            <td class="text-sm">1</td>
                                            <td><span class="badge bg-gradient-warning">Pending</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6>Attendance Summary</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h1 class="text-success">98%</h1>
                                    <p class="text-sm mb-0">Attendance Rate</p>
                                </div>
                                <div class="col-md-3">
                                    <h1 class="text-danger">2</h1>
                                    <p class="text-sm mb-0">Absences</p>
                                </div>
                                <div class="col-md-3">
                                    <h1 class="text-warning">1</h1>
                                    <p class="text-sm mb-0">Late Arrivals</p>
                                </div>
                                <div class="col-md-3">
                                    <h1 class="text-info">0</h1>
                                    <p class="text-sm mb-0">Early Departures</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Change Status Modal (for HR Admin) -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Employment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusChangeForm">
                    <div class="form-group">
                        <label class="form-control-label">Current Status</label>
                        <input type="text" class="form-control mb-3" value="Active" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">New Status</label>
                        <select class="form-select" id="newStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="on_leave">On Leave</option>
                            <option value="probation">Probation</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-control-label">Reason for Change</label>
                        <textarea class="form-control" rows="3" id="statusChangeReason"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Update Status</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Profile picture upload for HR Admin
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('profileImage').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Profile picture upload for Employee
    document.getElementById('imageUploadEmployee').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('profileImage').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Save changes for HR Admin
    document.getElementById('saveChangesBtn').addEventListener('click', function() {
        alert('Employee information updated successfully!');
    });
    
    // Change status modal for HR Admin
    document.getElementById('confirmStatusChange').addEventListener('click', function() {
        const newStatus = document.getElementById('newStatus').value;
        const reason = document.getElementById('statusChangeReason').value;
        
        if (!reason) {
            alert('Please provide a reason for status change');
            return;
        }
        
        // Update status in UI
        const statusBadge = document.getElementById('employeeStatus');
        statusBadge.textContent = document.getElementById('newStatus').options[document.getElementById('newStatus').selectedIndex].text;
        
        // Change badge color based on status
        if (newStatus === 'active') {
            statusBadge.className = 'badge bg-gradient-success';
        } else if (newStatus === 'inactive') {
            statusBadge.className = 'badge bg-gradient-danger';
        } else if (newStatus === 'on_leave') {
            statusBadge.className = 'badge bg-gradient-info';
        } else if (newStatus === 'probation') {
            statusBadge.className = 'badge bg-gradient-warning';
        }
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('changeStatusModal')).hide();
        alert('Employment status updated successfully!');
    });
    
    // Initialize performance chart for Super Admin
    if (document.getElementById('performance-chart')) {
        const ctx = document.getElementById('performance-chart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Performance Score',
                    data: [3.5, 3.7, 3.8, 3.9, 4.0, 3.8],
                    borderColor: '#5e72e4',
                    backgroundColor: 'rgba(94, 114, 228, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        min: 3,
                        max: 5
                    }
                }
            }
        });
    }
</script>
@endsection


