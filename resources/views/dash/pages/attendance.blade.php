@extends('dash.dash')
@section('title', 'Attendance')

@section('contentdash')
<style>
    /* Base Styles */
    :root {
        --primary-color: #5e72e4;
        --primary-hover: #4a5bd1;
        --success-color: #2dce89;
        --success-hover: #24a46d;
        --danger-color: #f5365c;
        --danger-hover: #e11e43;
        --warning-color: #fb6340;
        --warning-hover: #f95028;
        --info-color: #11cdef;
        --info-hover: #0da5c0;
        --dark-color: #212529;
        --light-color: #f8f9fa;
        --border-radius: 12px;
        --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* Card Improvements */
    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        margin-bottom: 1.5rem;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
        border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
    }

    /* Table Enhancements */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-bottom: 0;
        width: 100%;
    }

    .table thead th {
        background-color: var(--light-color);
        border-bottom: none;
        color: var(--dark-color);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
        white-space: nowrap;
    }

    .table tbody tr {
        background-color: white;
        border-radius: var(--border-radius);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: var(--transition);
    }

    .table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
    }

    .table tbody td {
        vertical-align: middle;
        padding: 1.25rem 1.5rem;
        border-top: none;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }

    /* Button Styles */
    .btn {
        font-weight: 600;
        letter-spacing: 0.3px;
        padding: 0.65rem 1.25rem;
        border-radius: var(--border-radius);
        transition: var(--transition);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
    }

    .btn-lg {
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(94, 114, 228, 0.25);
    }

    .btn-success {
        background-color: var(--success-color);
    }

    .btn-success:hover {
        background-color: var(--success-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(45, 206, 137, 0.25);
    }

    .btn-danger {
        background-color: var(--danger-color);
    }

    .btn-danger:hover {
        background-color: var(--danger-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 54, 92, 0.25);
    }

    /* Calendar Component */
    .attendance-calendar {
        font-family: 'Inter', sans-serif;
        margin-top: 1.5rem;
    }

    .calendar-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--dark-color);
        padding: 0.5rem;
        background-color: var(--light-color);
        border-radius: var(--border-radius);
    }

    .calendar-body {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.75rem;
    }

    .date {
        aspect-ratio: 1;
        border-radius: var(--border-radius);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        background-color: white;
        position: relative;
        transition: var(--transition);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .date:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        z-index: 1;
    }

    .date.weekend {
        background-color: rgba(233, 236, 239, 0.5);
    }

    .date.empty {
        visibility: hidden;
        pointer-events: none;
    }

    .day-number {
        font-size: 0.9rem;
        align-self: flex-start;
        color: #555;
        font-weight: 500;
    }

    .status {
        font-size: 1.1rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }

    .status.present {
        color: var(--success-color);
        background-color: rgba(45, 206, 137, 0.15);
    }

    .status.absent {
        color: var(--danger-color);
        background-color: rgba(245, 54, 92, 0.15);
    }

    .status.late {
        color: var(--warning-color);
        background-color: rgba(251, 99, 64, 0.15);
    }

    .status.on-leave {
        color: var(--info-color);
        background-color: rgba(17, 205, 239, 0.15);
    }

    /* Badge Styles */
    .badge {
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
    }

    .badge-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.65rem;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, var(--success-color), #1aae6f);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, var(--danger-color), #d31f4e);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, var(--warning-color), #f4420e);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, var(--info-color), #0b9bb8);
    }

    /* Employee View Specific */
    .employee-view .today-status-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .employee-view .status-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 2rem;
    }

    .employee-view .present .status-icon {
        background-color: rgba(45, 206, 137, 0.15);
        color: var(--success-color);
    }

    /* Animations */
    .pulse-effect {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(45, 206, 137, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(45, 206, 137, 0); }
        100% { box-shadow: 0 0 0 0 rgba(45, 206, 137, 0); }
    }

    .floating {
        animation: floating 3s ease-in-out infinite;
    }

    @keyframes floating {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .calendar-header,
        .calendar-body {
            grid-template-columns: repeat(7, minmax(40px, 1fr));
            gap: 0.5rem;
        }
        
        .date {
            padding: 0.25rem;
        }
        
        .day-number {
            font-size: 0.7rem;
        }
        
        .status {
            width: 20px;
            height: 20px;
            font-size: 0.8rem;
        }
        
        .table thead th,
        .table tbody td {
            padding: 0.75rem;
            font-size: 0.85rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 576px) {
        .card-header {
            padding: 1rem;
        }
        
        .table tbody td {
            padding: 0.75rem;
        }
        
        .calendar-body {
            gap: 0.3rem;
        }
    }
</style>

<div class="container-fluid py-4">
    @if(auth()->user()->role === 'admin')
    <!-- Admin View -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-2">Employee Attendance Records</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                                <i class="ni ni-fat-add me-1"></i> Add Record
                            </button>
                            <button class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                                <i class="ni ni-archive-2 me-1"></i> Export
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3 g-2">
                        <div class="col-md-3">
                            <label class="form-label text-sm mb-1">Filter by Date</label>
                            <input type="date" class="form-control form-control-sm" id="dateFilter">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-sm mb-1">Department</label>
                            <select class="form-select form-select-sm" id="departmentFilter">
                                <option value="">All Departments</option>
                                <option>HR</option>
                                <option>Finance</option>
                                <option>IT</option>
                                <option>Operations</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-sm mb-1">Status</label>
                            <select class="form-select form-select-sm" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option>Present</option>
                                <option>Absent</option>
                                <option>Late</option>
                                <option>On Leave</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-success btn-sm w-100">
                                <i class="ni ni-ui-04 me-1"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Employee</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Check In</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Check Out</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Hours</th>
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
                                    <td class="text-sm">15/06/2023</td>
                                    <td class="text-sm">08:45 AM</td>
                                    <td class="text-sm">05:30 PM</td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-success">Present</span>
                                    </td>
                                    <td class="text-sm">8.75 hours</td>
                                    <td class="text-sm">
                                        <a href="javascript:;" class="text-primary me-2" data-bs-toggle="tooltip" title="Edit">
                                            <i class="ni ni-ruler-pencil"></i>
                                        </a>
                                        <a href="javascript:;" class="text-secondary" data-bs-toggle="tooltip" title="Add Note">
                                            <i class="ni ni-single-copy-04"></i>
                                        </a>
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
                                    <td class="text-sm">15/06/2023</td>
                                    <td class="text-sm">09:15 AM</td>
                                    <td class="text-sm">-</td>
                                    <td>
                                        <span class="badge badge-sm bg-gradient-warning">Late</span>
                                    </td>
                                    <td class="text-sm">-</td>
                                    <td class="text-sm">
                                        <a href="javascript:;" class="text-primary me-2">
                                            <i class="ni ni-ruler-pencil"></i>
                                        </a>
                                        <a href="javascript:;" class="text-secondary">
                                            <i class="ni ni-single-copy-04"></i>
                                        </a>
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
    <!-- Super Admin View -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>Monthly Attendance Overview</h6>
                    <p class="text-sm">
                        <i class="ni ni-calendar-grid-58"></i>
                        <span class="font-weight-bold">June 2023</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="attendance-chart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Department Statistics</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-single-02 text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">IT Department</h6>
                                    <span class="text-xs">Attendance: <span class="font-weight-bold">94.5%</span></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                32/34
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-single-02 text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">HR Department</h6>
                                    <span class="text-xs">Attendance: <span class="font-weight-bold">91.2%</span></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                27/30
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-single-02 text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Finance</h6>
                                    <span class="text-xs">Attendance: <span class="font-weight-bold">89.7%</span></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                25/28
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="ni ni-single-02 text-white opacity-10"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Operations</h6>
                                    <span class="text-xs">Attendance: <span class="font-weight-bold">96.1%</span></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                                45/47
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Attendance Trends</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="trend-chart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Absence Reasons</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="absence-chart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- Employee View -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>My Attendance Calendar</h6>
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-2">
                                <i class="ni ni-bold-left"></i>
                            </button>
                            <span class="font-weight-bold">June 2023</span>
                            <button class="btn btn-sm btn-outline-primary ms-2">
                                <i class="ni ni-bold-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="attendance-calendar">
                        <div class="calendar-header">
                            <div class="day">Sun</div>
                            <div class="day">Mon</div>
                            <div class="day">Tue</div>
                            <div class="day">Wed</div>
                            <div class="day">Thu</div>
                            <div class="day">Fri</div>
                            <div class="day">Sat</div>
                        </div>
                        <div class="calendar-body">
                            <!-- Calendar days will be populated here -->
                            <div class="date empty"></div>
                            <div class="date empty"></div>
                            <div class="date empty"></div>
                            <div class="date">
                                <span class="day-number">1</span>
                                <div class="status present">✔</div>
                            </div>
                            <div class="date">
                                <span class="day-number">2</span>
                                <div class="status present">✔</div>
                            </div>
                            <div class="date weekend">
                                <span class="day-number">3</span>
                            </div>
                            <div class="date weekend">
                                <span class="day-number">4</span>
                            </div>
                            <div class="date">
                                <span class="day-number">5</span>
                                <div class="status late">⏰</div>
                            </div>
                            <div class="date">
                                <span class="day-number">6</span>
                                <div class="status absent">✖</div>
                            </div>
                            <!-- Continue for all days in month -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Today's Status</h6>
                </div>
                <div class="card-body p-3 d-flex flex-column">
                    <div class="text-center my-4">
                        <div class="icon icon-shape icon-lg bg-gradient-success shadow text-center rounded-circle mb-3">
                            <i class="ni ni-watch-time text-white opacity-10"></i>
                        </div>
                        <h5 class="text-success">Currently: Present</h5>
                        <p class="text-sm">Last check-in: 08:45 AM</p>
                    </div>
                    
                    <div class="mt-auto">
                        <button class="btn btn-lg btn-success w-100 mb-3" id="checkInBtn">
                            <i class="ni ni-check-bold me-2"></i> Check In
                        </button>
                        <button class="btn btn-lg btn-danger w-100" id="checkOutBtn" disabled>
                            <i class="ni ni-send me-2"></i> Check Out
                        </button>
                    </div>
                    
                    <div class="mt-4">
                        <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#requestCorrectionModal">
                            <i class="ni ni-ruler-pencil me-2"></i> Request Correction
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>My Monthly Summary</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h1 class="text-success">18</h1>
                            <p class="text-sm mb-0">Present Days</p>
                        </div>
                        <div class="col-md-3">
                            <h1 class="text-danger">2</h1>
                            <p class="text-sm mb-0">Absent Days</p>
                        </div>
                        <div class="col-md-3">
                            <h1 class="text-warning">1</h1>
                            <p class="text-sm mb-0">Late Arrivals</p>
                        </div>
                        <div class="col-md-3">
                            <h1 class="text-info">94.7%</h1>
                            <p class="text-sm mb-0">Attendance Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modals would go here -->
@endsection