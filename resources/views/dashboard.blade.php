@extends('dash.dash')

@section('contentdash')
<div class="container-fluid">
    @php
        // Helper functions for avatars
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

        // Calculate absence statistics
        $user = auth()->user();
        $today = today()->format('Y-m-d');

        // For employees
        if ($user->role === 'employee') {
            $absentDays = $user->attendances()
                ->where('status', 'like', '%Absent%')
                ->count();

            $approvedVacations = $user->vacations()
                ->where('status', 'Approved')
                ->count();

            $approvedLeaves = $user->leave()
                ->where('status', 'Approved')
                ->count();

            $todayBirthdays = \App\Models\User::whereMonth('birth_date', today()->month)
                ->whereDay('birth_date', today()->day)
                ->get();

            $latestEvaluation = $user->evaluations()
                ->latest()
                ->first();
        }

        // For admins/managers
        if (in_array($user->role, ['admin', 'super_admin', 'department_manager'])) {
            $activeEmployees = \App\Models\User::where('status', 'active')->count();
            $inactiveEmployees = \App\Models\User::where('status', 'inactive')->count();
            
            $presentToday = \App\Models\Attendance::whereDate('date', $today)
                ->where('status', 'like', '%Present%')
                ->count();

            $absentToday = \App\Models\User::whereDoesntHave('attendances', function($q) use ($today) {
                $q->whereDate('date', $today);
            })->count();

            $todayBirthdays = \App\Models\User::whereMonth('birth_date', today()->month)
                ->whereDay('birth_date', today()->day)
                ->get();

            // Vacation stats
            $vacationStats = [
                'total' => \App\Models\Vacation::count(),
                'approved' => \App\Models\Vacation::where('status', 'Approved')->count(),
                'pending' => \App\Models\Vacation::where('status', 'Pending')->count(),
                'rejected' => \App\Models\Vacation::where('status', 'Rejected')->count(),
                'approved_percentage' => \App\Models\Vacation::count() > 0 ? 
                    round(\App\Models\Vacation::where('status', 'Approved')->count() / \App\Models\Vacation::count() * 100) : 0
            ];

            // Leave stats
            $leaveStats = [
                'total' => \App\Models\Leave::count(),
                'approved' => \App\Models\Leave::where('status', 'Approved')->count(),
                'pending' => \App\Models\Leave::where('status', 'Pending')->count(),
                'rejected' => \App\Models\Leave::where('status', 'Rejected')->count(),
                'approved_percentage' => \App\Models\Leave::count() > 0 ? 
                    round(\App\Models\Leave::where('status', 'Approved')->count() / \App\Models\Leave::count() * 100) : 0
            ];

            // Absence stats
            $absenceStats = [
                'total' => \App\Models\Attendance::where('status', 'like', '%Absent%')->count(),
                'justified' => \App\Models\Attendance::where('status', 'like', '%Absent%')
                    ->count(),
                'percentage' => \App\Models\Attendance::where('status', 'like', '%Absent%')->count() > 0 ?
                    round(\App\Models\Attendance::where('status', 'like', '%Absent%')
                        ->whereNotNull('justification')
                        ->count() / \App\Models\Attendance::where('status', 'like', '%Absent%')->count() * 100) : 0
            ];

            // Recent activities
         $recentActivities = [];

            // Department specific data for managers
            if ($user->role === 'department_manager') {
                $departmentId = $user->department_id;
                
                // Department attendance data
                $deptAttendanceData = [
                    'present' => \App\Models\Attendance::whereHas('user', function($q) use ($departmentId) {
                            $q->where('department_id', $departmentId);
                        })
                        ->whereMonth('date', today()->month)
                        ->where('status', 'like', '%Present%')
                        ->count(),
                    'absent' => \App\Models\Attendance::whereHas('user', function($q) use ($departmentId) {
                            $q->where('department_id', $departmentId);
                        })
                        ->whereMonth('date', today()->month)
                        ->where('status', 'like', '%Absent%')
                        ->count(),
                    'late' => \App\Models\Attendance::whereHas('user', function($q) use ($departmentId) {
                            $q->where('department_id', $departmentId);
                        })
                        ->whereMonth('date', today()->month)
                        ->where('status', 'like', '%Late%')
                        ->count(),
                    'on_leave' => \App\Models\Attendance::whereHas('user', function($q) use ($departmentId) {
                            $q->where('department_id', $departmentId);
                        })
                        ->whereMonth('date', today()->month)
                        ->where('status', 'like', '%Leave%')
                        ->count()
                ];

                // Department leave types
                $deptLeaveTypes = [
                    'sick_leave' => \App\Models\Leave::whereHas('user', function($q) use ($departmentId) {
                            $q->where('department_id', $departmentId);
                        })
                        ->where('type', 'Sick Leave')
                        ->count(),
                    'personal_leave' => \App\Models\Leave::whereHas('user', function($q) use ($departmentId) {
                            $q->where('department_id', $departmentId);
                        })
                        ->where('type', 'Personal Leave')
                        ->count(),
                    'vacation' => \App\Models\Leave::whereHas('user', function($q) use ($departmentId) {
                            $q->where('department_id', $departmentId);
                        })
                        ->where('type', 'Vacation')
                        ->count(),
                    'unexcused' => \App\Models\Attendance::whereHas('user', function($q) use ($departmentId) {
                            $q->where('department_id', $departmentId);
                        })
                        ->where('status', 'like', '%Absent%')
                        ->count()
                ];
            }
        }
    @endphp

    @if(auth()->user()->role === 'employee')
        <!-- Employee Dashboard -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">Employee Dashboard</h3>
            </div>
            
            <!-- Stats Cards -->
            <div class="col-md-4 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Absent Days</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $absentDays ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Approved Vacations</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedVacations ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-umbrella-beach fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @if($user->role === 'employee')
    <div class="col-md-4 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Approved Leaves</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $approvedLeaves ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sign-out-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

            @if(count($todayBirthdays ?? []) > 0)
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 bg-gradient-primary">
                        <h6 class="m-0 font-weight-bold text-white">🎉 Today's Birthdays</h6>
                    </div>
                    <div class="card-body">
                        @foreach($todayBirthdays as $birthday)
                        <div class="alert alert-info">
                            🎂 Today is <strong>{{ $birthday->name }}</strong>'s birthday!
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if($latestEvaluation ?? false)
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">⭐ Your Performance</h6>
                    </div>
                    <div class="card-body">
                        @include('partials.evaluation-chart', ['evaluation' => $latestEvaluation])
                    </div>
                </div>
            </div>
            @else
            <div class="col-12">
                <div class="alert alert-warning">
                    No evaluation available yet.
                </div>
            </div>
            @endif
        </div>

    @elseif(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager']))
        <!-- Admin/Manager Dashboard -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }} Dashboard</h3>
            </div>
            
            <!-- Stats Cards -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Active Employees</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeEmployees ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Inactive Employees </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inactiveEmployees ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-slash fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

      <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Present Today ({{ $presentToday ?? 0 }})
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        @foreach($presentEmployees->take(3) as $employee)
                            <div class="d-flex align-items-center mb-1">
                                <div class="avatar-sm me-2">
                                    @if($employee->photo_url)
                                        <img src="{{ asset('storage/'.$employee->photo_url) }}" 
                                             class="rounded-circle" width="24" height="24"
                                             onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.png') }}'">
                                    @else
                                        <div class="avatar-initials rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                             style="width:24px;height:24px;font-size:10px">
                                            {{ substr($employee->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <span class="text-truncate" style="max-width: 100px">{{ $employee->name }}</span>
                            <small class="text-muted ms-auto">
    @if($employee->attendances->first() && $employee->attendances->first()->check_in)
        {{ $employee->attendances->first()->check_in->format('h:i A') }}
    @else
        N/A
    @endif
</small>
                            </div>
                        @endforeach
                        @if($presentToday > 3)
                            <div class="text-center mt-2">
                                <small class="text-muted">+{{ $presentToday - 3 }} more</small>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-user-check fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent py-1 px-3">
            <a href="{{ route('attendance.index') }}" class="small text-success stretched-link">
                View all attendance records
            </a>
        </div>
    </div>
</div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Absent Today</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $absentToday ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-times fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($todayBirthdays ?? []) > 0)
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 bg-gradient-primary">
                        <h6 class="m-0 font-weight-bold text-white">🎉 Today's Birthdays</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($todayBirthdays as $birthday)
                            <div class="col-md-4 mb-3">
                                <div class="card border-left-info shadow">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <img class="img-profile rounded-circle mb-2" 
                                                 src="{{ $birthday->avatar_url ?? asset('img/default-avatar.png') }}" 
                                                 style="width: 60px; height: 60px;">
                                            <h6>{{ $birthday->name }}</h6>
                                            <p class="text-muted small">{{ $birthday->position }}</p>
                                            <button class="btn btn-sm btn-outline-primary send-wish" 
                                                    data-user-id="{{ $birthday->id }}">
                                                <i class="fas fa-gift"></i> Send Wish
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Statistics Overview Section -->
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">📊 Statistics Overview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card border-left-success h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">
                                            <i class="fas fa-umbrella-beach"></i> Vacations
                                        </h5>
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-auto">
                                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $vacationStats['total'] ?? 0 }}</div>
                                            </div>
                                            <div class="col">
                                                <div class="progress progress-sm mr-2">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $vacationStats['approved_percentage'] ?? 0 }}%" 
                                                        aria-valuenow="{{ $vacationStats['approved_percentage'] ?? 0 }}" 
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs">
                                            <span class="text-success mr-2">
                                                <i class="fas fa-check"></i> {{ $vacationStats['approved'] ?? 0 }} Approved
                                            </span>
                                            <span class="text-warning mr-2">
                                                <i class="fas fa-clock"></i> {{ $vacationStats['pending'] ?? 0 }} Pending
                                            </span>
                                            <span class="text-danger">
                                                <i class="fas fa-times"></i> {{ $vacationStats['rejected'] ?? 0 }} Rejected
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <div class="card border-left-warning h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-warning">
                                            <i class="fas fa-sign-out-alt"></i> Leaves
                                        </h5>
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-auto">
                                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $leaveStats['total'] ?? 0 }}</div>
                                            </div>
                                            <div class="col">
                                                <div class="progress progress-sm mr-2">
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: {{ $leaveStats['approved_percentage'] ?? 0 }}%" 
                                                        aria-valuenow="{{ $leaveStats['approved_percentage'] ?? 0 }}" 
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs">
                                            <span class="text-success mr-2">
                                                <i class="fas fa-check"></i> {{ $leaveStats['approved'] ?? 0 }} Approved
                                            </span>
                                            <span class="text-warning mr-2">
                                                <i class="fas fa-clock"></i> {{ $leaveStats['pending'] ?? 0 }} Pending
                                            </span>
                                            <span class="text-danger">
                                                <i class="fas fa-times"></i> {{ $leaveStats['rejected'] ?? 0 }} Rejected
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>

            @if(auth()->user()->role === 'department_manager' && isset($deptAttendanceData) && isset($deptLeaveTypes))
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 bg-gradient-info">
                        <h6 class="m-0 font-weight-bold text-white">🏢 Department Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-center mb-3">Department Attendance (This Month)</h5>
                                <canvas id="deptAttendanceChart" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-center mb-3">Absence Reasons</h5>
                                <canvas id="deptAbsenceChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Absences Section -->
           

            <!-- Recent Activities Section -->
          
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    $('.send-wish').click(function() {
        const userId = $(this).data('user-id');
        $.post('/birthdays/wish/' + userId, {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            toastr.success('Birthday wish sent successfully!');
        }).fail(function() {
            toastr.error('Failed to send birthday wish');
        });
    });

    @if(auth()->user()->role === 'department_manager' && isset($deptAttendanceData) && isset($deptLeaveTypes))
    // Department Attendance Chart
    new Chart(document.getElementById('deptAttendanceChart'), {
        type: 'bar',
        data: {
            labels: ['Present', 'Absent', 'Late', 'On Leave'],
            datasets: [{
                label: 'Employees',
                data: [
                    {{ $deptAttendanceData['present'] }},
                    {{ $deptAttendanceData['absent'] }},
                    {{ $deptAttendanceData['late'] }},
                    {{ $deptAttendanceData['on_leave'] }}
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(23, 162, 184, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Attendance Distribution'
                }
            }
        }
    });

    // Department Absence Chart
    new Chart(document.getElementById('deptAbsenceChart'), {
        type: 'pie',
        data: {
            labels: ['Sick Leave', 'Personal Leave', 'Vacation', 'Unexcused'],
            datasets: [{
                data: [
                    {{ $deptLeaveTypes['sick_leave'] ?? 0 }},
                    {{ $deptLeaveTypes['personal_leave'] ?? 0 }},
                    {{ $deptLeaveTypes['vacation'] ?? 0 }},
                    {{ $deptLeaveTypes['unexcused'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 99, 132, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Absence Reasons'
                }
            }
        }
    });
    @endif
});
</script>

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

    .avatar-initials {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
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

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .bg-gradient-danger {
        background: linear-gradient(87deg, #f5365c 0, #f56036 100%) !important;
    }

    .bg-gradient-info {
        background: linear-gradient(87deg, #11cdef 0, #1171ef 100%) !important;
    }

    @media (max-width: 767.98px) {
        .avatar-table {
            width: 36px;
            height: 36px;
        }
    }
</style>
@endsection