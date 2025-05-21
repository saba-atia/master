@extends('dash.dash')
@section('title', 'attendance')
@section('contentdash')

       @php
        $user = auth()->user();
        
        // Attendance filter params
        $attendance_from = request('attendance_from');
        $attendance_to = request('attendance_to');
        $attendance_department_id = request('attendance_department_id');
        
        // Absent filter params
        $absent_from = request('absent_from');
        $absent_to = request('absent_to');
        $absent_department_id = request('absent_department_id');
        $absent_name = request('absent_name');

        // Query for attendance records
        if ($user->isAdminOrSuperAdmin()) {
            $attendanceQuery = \App\Models\Attendance::with(['user', 'user.department'])
                ->whereHas('user', function($q) {
                    $q->where('status', 'active'); // فقط الموظفين النشطين
                });
        } else {
            $attendanceQuery = $user->attendances()->with(['user.department']);
        }

        if ($attendance_from && $attendance_to) {
            $attendanceQuery = $attendanceQuery->whereBetween('date', [$attendance_from, $attendance_to]);
        }

        if ($attendance_department_id) {
            $attendanceQuery = $attendanceQuery->whereHas('user', function ($q) use ($attendance_department_id) {
                $q->where('department_id', $attendance_department_id)
                  ->where('status', 'active'); // إضافة شرط النشاط هنا أيضاً
            });
        }

        $attendances = $attendanceQuery->latest()->get();

        // Query for absent employees (only for admins)
        $absentEmployees = [];
        if ($user->isAdminOrSuperAdmin()) {
            $absentDateFilter = $absent_from && $absent_to ? [$absent_from, $absent_to] : [today(), today()];

            $absentQuery = \App\Models\User::where('status', 'active') // فقط الموظفين النشطين
                ->whereDoesntHave('attendances', function ($q) use ($absentDateFilter) {
                    $q->whereBetween('date', $absentDateFilter);
                });

            if ($absent_name) {
                $absentQuery->where('name', 'like', '%'.$absent_name.'%');
            }

            if ($absent_department_id) {
                $absentQuery->where('department_id', $absent_department_id);
            }

            $absentEmployees = $absentQuery->get();
        }

        // الدوال المساعدة
        if (!function_exists('stringToColor')) {
            function stringToColor($string) {
                $hash = md5($string);
                return sprintf('#%s', substr($hash, 0, 6));
            }
        }

        if (!function_exists('getInitials')) {
            function getInitials($name) {
                $words = explode(' ', $name);
                $initials = '';

                if (count($words) >= 2) {
                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                } else {
                    $initials = strtoupper(substr($name, 0, 2));
                }

                return $initials;
            }
        }
    @endphp

    <div class="container-fluid py-2 py-md-4 px-0 px-md-3">
        <div class="row">
            <div class="col-12">
                <div class="card my-2 my-md-4">
                    <!-- Card Header -->
                    <div class="card-header p-0 position-relative mt-n4 mx-2 mx-md-3 z-index-2">
                        <div class="bg-gradient-info shadow-info border-radius-lg pt-3 pt-md-4 pb-2 pb-md-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-2 mb-md-0">
                                    <i class="material-icons opacity-10 me-2">schedule</i>
                                    @if (auth()->user()->isAdminOrSuperAdmin())
                                        All Employees Attendance Records
                                    @else
                                        My Attendance Records
                                    @endif
                                </h6>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Filters Section -->
                    @if (auth()->user()->isAdminOrSuperAdmin())
                        <div class="card-body px-2 px-md-4 pb-2">
                            <form method="GET" class="row g-3">
                                <input type="hidden" name="attendance_filter" value="1">
                                <div class="col-md-3">
                                    <label for="attendance_from" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="attendance_from" name="attendance_from"
                                        value="{{ $attendance_from }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="attendance_to" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="attendance_to" name="attendance_to"
                                        value="{{ $attendance_to }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="attendance_department_id" class="form-label">Department</label>
                                    <select class="form-select" id="attendance_department_id" name="attendance_department_id">
                                        <option value="">All Departments</option>
                                        @foreach (\App\Models\Department::all() as $department)
                                            <option value="{{ $department->id }}"
                                                {{ $attendance_department_id == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary mb-3">Filter Attendance</button>
                                    @if ($attendance_from || $attendance_to || $attendance_department_id)
                                        <a href="{{ route('attendance.index') }}"
                                            class="btn btn-outline-secondary mb-3 ms-2">Reset</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Punch In/Out Section -->
                    <div class="card-body px-2 px-md-4 pb-2">
                        <div class="action-container text-center py-2 py-md-3 rounded bg-light mb-3 mb-md-4">
                            @php
                                $todayAttendance = auth()->user()->attendances()->whereDate('date', today())->first();
                            @endphp

                            @if (!$todayAttendance)
                                <form id="punchInForm" action="{{ route('attendance.store') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-md btn-lg shadow-sm w-100">
                                        <i class="material-icons me-1 me-md-2">fingerprint</i>
                                        <span class="fw-bold">Check In</span>
                                        <small class="d-block mt-1">{{ now()->format('h:i A, M d') }}</small>
                                    </button>
                                </form>
                            @elseif(!$todayAttendance->check_out)
                                <form id="punchOutForm" action="{{ route('attendance.store') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-md btn-lg shadow-sm w-100">
                                        <i class="material-icons me-1 me-md-2"></i>
                                        <span class="fw-bold">Check Out</span>
                                        <small class="d-block mt-1">{{ now()->format('h:i A, M d') }}</small>
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-info mb-0">
                                    <i class="material-icons me-2"></i>
                                    @if($todayAttendance->status == 'Completed')
                                        You've completed today's attendance
                                    @elseif($todayAttendance->status == 'In Progress')
                                        You've checked in but not checked out yet
                                    @else
                                        Your attendance status: {{ $todayAttendance->status }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Attendance Records Table -->
                        <div class="table-responsive mb-4">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        @if (auth()->user()->isAdminOrSuperAdmin())
                                            <th class="d-none d-lg-table-cell">Employee</th>
                                        @endif
                                        <th>Date</th>
                                        <th class="d-none d-sm-table-cell">Check In</th>
                                        <th class="d-none d-sm-table-cell">Check Out</th>
                                        <th>Status</th>
                                        @if (auth()->user()->isAdminOrSuperAdmin())
                                            <th class="d-none d-md-table-cell">Department</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($attendances->count() > 0)
                                        @foreach ($attendances as $attendance)
                                            <tr>
                                                @if (auth()->user()->isAdminOrSuperAdmin())
                                                   <td class="d-none d-lg-table-cell">
    <div class="d-flex align-items-center">
        <div class="avatar-wrapper position-relative me-3">
            @if ($attendance->user && $attendance->user->photo_url && file_exists(public_path('storage/' . $attendance->user->photo_url)))
                @php
                    $photoPath = Str::startsWith($attendance->user->photo_url, 'storage/') 
                        ? $attendance->user->photo_url 
                        : 'storage/' . $attendance->user->photo_url;
                @endphp
                <img src="{{ asset($photoPath) }}" 
                     alt="{{ $attendance->user->name }}"
                     class="rounded-circle img-thumbnail avatar-table shadow"
                     onerror="this.onerror=null;this.src='{{ asset('assets/img/default-avatar.jpg') }}'">
            @else
                <div class="avatar-initials rounded-circle shadow" 
                     style="background-color: {{ stringToColor($attendance->user->name ?? '') }}; 
                            width: 40px; height: 40px;
                            display: flex; align-items: center; justify-content: center;
                            color: white; font-weight: bold;">
                    {{ getInitials($attendance->user->name ?? '') }}
                </div>
            @endif
        </div>
        <div>
            <h6 class="mb-0 text-sm">{{ $attendance->user ? $attendance->user->name : '--' }}</h6>
            <p class="text-xs text-secondary mb-0">{{ $attendance->user->email ?? '--' }}</p>
        </div>
    </div>
</td>
                                                @endif
                                                <td>{{ $attendance->date->format('M d') }}</td>
                                                <td class="d-none d-sm-table-cell">
                                                    {{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '--' }}
                                                </td>
                                                <td class="d-none d-sm-table-cell">
                                                    {{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '--' }}
                                                </td>
                                                <td>
                                                    <span class="status-badge 
                                                        @if (str_contains($attendance->status, 'Completed')) status-completed
                                                        @elseif(str_contains($attendance->status, 'In Progress')) status-in-progress
                                                        @else status-missing @endif">
                                                        {{ $attendance->status }}
                                                    </span>
                                                </td>
                                                @if (auth()->user()->isAdminOrSuperAdmin())
                                                    <td class="d-none d-md-table-cell">
                                                        {{ $attendance->user->department->name ?? '--' }}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ auth()->user()->isAdminOrSuperAdmin() ? 6 : 4 }}"
                                                class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="material-icons opacity-10 mb-2"
                                                        style="font-size: 2.5rem;">hourglass_empty</i>
                                                    <span class="text-muted">No attendance records found</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Absent Employees Table (Only for Admins) -->
                        @if (auth()->user()->isAdminOrSuperAdmin())
                            <div class="mt-5">
                                <div class="card">
                                    <div class="card-header bg-gradient-danger">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="text-white mb-0">
                                                <i class="material-icons opacity-10 me-2"></i>
                                                Absent Employees ({{ $absentEmployees->count() }})
                                                @if ($absent_from || $absent_to)
                                                    <small class="d-block mt-1">{{ $absent_from ? $absent_from . ' to ' . $absent_to : 'Today' }}</small>
                                                @endif
                                            </h6>
                                            <div>
                                                <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" 
                                                        data-bs-target="#absentFilterCollapse" aria-expanded="false" 
                                                        aria-controls="absentFilterCollapse">
                                                    <i class="material-icons"></i> Filter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- فلتر خاص بالغائبين -->
                                    <div class="collapse" id="absentFilterCollapse">
                                        <div class="card-body pt-0">
                                            <form method="GET" class="row g-3">
                                                <input type="hidden" name="absent_filter" value="1">
                                                <div class="col-md-4">
                                                    <label for="absent_from" class="form-label">From Date</label>
                                                    <input type="date" class="form-control" id="absent_from" name="absent_from"
                                                        value="{{ $absent_from }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="absent_to" class="form-label">To Date</label>
                                                    <input type="date" class="form-control" id="absent_to" name="absent_to"
                                                        value="{{ $absent_to }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="absent_department_id" class="form-label">Department</label>
                                                    <select class="form-select" id="absent_department_id" name="absent_department_id">
                                                        <option value="">All Departments</option>
                                                        @foreach (\App\Models\Department::all() as $department)
                                                            <option value="{{ $department->id }}"
                                                                {{ $absent_department_id == $department->id ? 'selected' : '' }}>
                                                                {{ $department->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="absent_name" class="form-label">Employee Name</label>
                                                    <input type="text" class="form-control" id="absent_name" name="absent_name" 
                                                           value="{{ $absent_name }}" placeholder="Search by name...">
                                                </div>
                                                <div class="col-12 d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                                                    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">Reset</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table align-items-center mb-0">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder">#</th>
                                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder">Employee</th>
                                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder d-none d-md-table-cell">Department</th>
                                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($absentEmployees as $index => $employee)
                                                        <tr>
                                                            <td class="ps-4">
                                                                <span class="text-xs font-weight-bold">{{ $index + 1 }}</span>
                                                            </td>
                                                            <td>
    <div class="d-flex align-items-center px-3">
        <div class="avatar-wrapper position-relative me-3">
            @if ($employee->photo_url)
                @php
                    $photoPath = Str::startsWith($employee->photo_url, 'storage/') 
                        ? $employee->photo_url 
                        : 'storage/' . $employee->photo_url;
                @endphp
                <img src="{{ asset($photoPath) }}" 
                     alt="{{ $employee->name }}"
                     class="rounded-circle img-thumbnail avatar-table shadow"
                     onerror="this.onerror=null;this.src='{{ asset('assets/img/default-avatar.jpg') }}'">
            @else
                <div class="avatar-initials rounded-circle shadow" 
                     style="background-color: {{ stringToColor($employee->name) }}; 
                            width: 40px; height: 40px;
                            display: flex; align-items: center; justify-content: center;
                            color: white; font-weight: bold;">
                    {{ getInitials($employee->name) }}
                </div>
            @endif
        </div>
        <div>
            <h6 class="mb-0 text-sm">{{ $employee->name }}</h6>
            <p class="text-xs text-secondary mb-0">{{ $employee->email }}</p>
        </div>
    </div>
</td>
                                                            <td class="d-none d-md-table-cell">
                                                                <span class="text-xs font-weight-bold">{{ $employee->department->name ?? '--' }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-sm bg-gradient-danger">Absent</span>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-4">
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <i class="material-icons opacity-10 mb-2"
                                                                        style="font-size: 2.5rem;"></i>
                                                                    <span class="text-muted">All employees have attendance records</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-wrapper {
            position: relative;
            width: fit-content;
        }

        .avatar-table {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 2px solid #fff;
            transition: transform 0.3s ease;
        }

        .verified-badge-sm {
            position: absolute;
            bottom: -2px;
            right: -2px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            font-size: 0.5rem;
        }

        .table tbody tr:hover .avatar-table {
            transform: scale(1.1);
        }

        .status-badge {
            display: inline-block;
            padding: 0.3em 0.6em;
            font-size: 0.7em;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .status-completed {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-in-progress {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .status-missing {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .table tbody tr:hover {
            background-color: rgba(115, 103, 240, 0.04);
        }

        @media (max-width: 767.98px) {
            .avatar-table {
                width: 36px;
                height: 36px;
            }

            .verified-badge-sm {
                width: 14px;
                height: 14px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Collapse filter section if filters are applied
            @if($absent_name || $absent_department_id || $absent_from || $absent_to)
                document.getElementById('absentFilterCollapse').classList.add('show');
            @endif
        });
    </script>
@endsection