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

        $todayBirthdays = \App\Models\User::where('status', 'active')
            ->whereMonth('birth_date', today()->month)
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
        
        // Count present employees today (distinct users)
       $presentToday = \App\Models\Attendance::whereDate('date', $today)
    ->where('status', 'like', '%Present%')
    ->whereHas('user', function($q) {
        $q->where('status', 'active');
    })
    ->distinct('user_id')
    ->count('user_id');

        $absentToday = \App\Models\User::where('status', 'active')
            ->whereDoesntHave('attendances', function($q) use ($today) {
                $q->whereDate('date', $today);
            })->count();

        $todayBirthdays = \App\Models\User::where('status', 'active')
            ->whereMonth('birth_date', today()->month)
            ->whereDay('birth_date', today()->day)
            ->get();

        // Vacation stats - only for active employees
        $vacationStats = [
            'total' => \App\Models\Vacation::whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'approved' => \App\Models\Vacation::where('status', 'Approved')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'pending' => \App\Models\Vacation::where('status', 'Pending')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'rejected' => \App\Models\Vacation::where('status', 'Rejected')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'approved_percentage' => \App\Models\Vacation::whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count() > 0 ? 
                round(\App\Models\Vacation::where('status', 'Approved')
                    ->whereHas('user', function($q) {
                        $q->where('status', 'active');
                    })->count() / \App\Models\Vacation::whereHas('user', function($q) {
                        $q->where('status', 'active');
                    })->count() * 100) : 0
        ];

        // Leave stats - only for active employees
        $leaveStats = [
            'total' => \App\Models\Leave::whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'approved' => \App\Models\Leave::where('status', 'Approved')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'pending' => \App\Models\Leave::where('status', 'Pending')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'rejected' => \App\Models\Leave::where('status', 'Rejected')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'approved_percentage' => \App\Models\Leave::whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count() > 0 ? 
                round(\App\Models\Leave::where('status', 'Approved')
                    ->whereHas('user', function($q) {
                        $q->where('status', 'active');
                    })->count() / \App\Models\Leave::whereHas('user', function($q) {
                        $q->where('status', 'active');
                    })->count() * 100) : 0
        ];

        // Absence stats - only for active employees
        $absenceStats = [
            'total' => \App\Models\Attendance::where('status', 'like', '%Absent%')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'justified' => \App\Models\Attendance::where('status', 'like', '%Absent%')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'percentage' => \App\Models\Attendance::where('status', 'like', '%Absent%')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                })->count() > 0 ?
                round(\App\Models\Attendance::where('status', 'like', '%Absent%')
                    ->whereNotNull('justification')
                    ->whereHas('user', function($q) {
                        $q->where('status', 'active');
                    })->count() / \App\Models\Attendance::where('status', 'like', '%Absent%')
                        ->whereHas('user', function($q) {
                            $q->where('status', 'active');
                        })->count() * 100) : 0
        ];

        // Department specific data for managers
        if ($user->role === 'department_manager') {
            $departmentId = $user->department_id;
            
            // Department attendance data - only active employees
            $deptAttendanceData = [
               $presentToday = \App\Models\User::where('status', 'active')
    ->whereDoesntHave('attendances', function($q) use ($today) {
        $q->whereDate('date', $today)
          ->where('status', 'like', '%Absent%');
    })
    ->count(),
                'absent' => \App\Models\Attendance::whereHas('user', function($q) use ($departmentId) {
                        $q->where('department_id', $departmentId)
                          ->where('status', 'active');
                    })
                    ->whereMonth('date', today()->month)
                    ->where('status', 'like', '%Absent%')
                    ->count(),
                'late' => \App\Models\Attendance::whereHas('user', function($q) use ($departmentId) {
                        $q->where('department_id', $departmentId)
                          ->where('status', 'active');
                    })
                    ->whereMonth('date', today()->month)
                    ->where('status', 'like', '%Late%')
                    ->count(),
                'on_leave' => \App\Models\Attendance::whereHas('user', function($q) use ($departmentId) {
                        $q->where('department_id', $departmentId)
                          ->where('status', 'active');
                    })
                    ->whereMonth('date', today()->month)
                    ->where('status', 'like', '%Leave%')
                    ->count()
            ];

            // Department leave types - only active employees
            $deptLeaveTypes = [
                'sick_leave' => \App\Models\Leave::whereHas('user', function($q) use ($departmentId) {
                        $q->where('department_id', $departmentId)
                          ->where('status', 'active');
                    })
                    ->where('type', 'Sick Leave')
                    ->count(),
                'personal_leave' => \App\Models\Leave::whereHas('user', function($q) use ($departmentId) {
                        $q->where('department_id', $departmentId)
                          ->where('status', 'active');
                    })
                    ->where('type', 'Personal Leave')
                    ->count(),
                'vacation' => \App\Models\Leave::whereHas('user', function($q) use ($departmentId) {
                        $q->where('department_id', $departmentId)
                          ->where('status', 'active');
                    })
                    ->where('type', 'Vacation')
                    ->count(),
                'unexcused' => \App\Models\Attendance::whereHas('user', function($q) use ($departmentId) {
                        $q->where('department_id', $departmentId)
                          ->where('status', 'active');
                    })
                    ->where('status', 'like', '%Absent%')
                    ->count()
            ];
        }
    }
@endphp

@php
    // احصل على تقييمات المستخدم الحالي
    $userEvaluations = auth()->user()->evaluations()->orderBy('evaluation_date', 'desc')->get();
    $latestEvaluation = $userEvaluations->first();
    
    // احسب المتوسطات إذا كان هناك تقييمات
    if($userEvaluations->count() > 0) {
        $avgPunctuality = $userEvaluations->avg('punctuality');
        $avgWorkQuality = $userEvaluations->avg('work_quality');
        $avgTeamwork = $userEvaluations->avg('teamwork');
        $avgOverall = $userEvaluations->avg('average');
    }
@endphp

    @if(auth()->user()->role === 'employee')
        <!-- Employee Dashboard -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">Employee Dashboard</h3>
            </div>
            
            <!-- Stats Cards - New Design -->
            <div class="col-md-4 mb-4">
                <div class="stat-card stat-card-danger h-100">
                    <div class="card-body">
                        <i class="fas fa-calendar-times stat-icon"></i>
                        <div class="stat-title">Absent Days</div>
                        <div class="stat-value">{{ $absentDays ?? 0 }}</div>
                        <div class="stat-change">
                            <i class="fas fa-info-circle text-danger mr-1"></i>
                            <span>Total absences</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="stat-card stat-card-success h-100">
                    <div class="card-body">
                        <i class="fas fa-umbrella-beach stat-icon"></i>
                        <div class="stat-title">Approved Vacations</div>
                        <div class="stat-value">{{ $approvedVacations ?? 0 }}</div>
                        <div class="stat-change">
                            <i class="fas fa-check-circle text-success mr-1"></i>
                            <span>Approved requests</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="stat-card stat-card-warning h-100">
                    <div class="card-body">
                        <i class="fas fa-sign-out-alt stat-icon"></i>
                        <div class="stat-title">Approved Leaves</div>
                        <div class="stat-value">{{ $approvedLeaves ?? 0 }}</div>
                        <div class="stat-change">
                            <i class="fas fa-clock text-warning mr-1"></i>
                            <span>Total leaves</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($todayBirthdays ?? []) > 0)
            <div class="col-12 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header py-3 bg-gradient-primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-birthday-cake mr-2"></i> Today's Birthdays
                            </h6>
                            <span class="badge badge-light">{{ count($todayBirthdays) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($todayBirthdays as $birthday)
                            <div class="col-md-3 mb-4">
                                <div class="birthday-card h-100">
                                    <div class="profile-img-container">
                                        <img class="profile-img" 
                                             src="{{ $birthday->avatar_url ?? asset('img/default-avatar.png') }}" 
                                             alt="{{ $birthday->name }}">
                                    </div>
                                    <div class="card-body">
                                        <h5 class="user-name">{{ $birthday->name }}</h5>
                                        <p class="user-position">{{ $birthday->position }}</p>
                                        <div class="birthday-info">
                                            <i class="fas fa-birthday-cake"></i>
                                            {{ \Carbon\Carbon::parse($birthday->birth_date)->format('M d') }}
                                        </div>
                                        <button class="btn btn-primary btn-sm send-wish" 
                                                data-user-id="{{ $birthday->id }}">
                                            <i class="fas fa-gift mr-1"></i> Send Wish
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

          <!-- Evaluation Section for All Users -->
<div class="col-12 mb-4">
    <div class="card shadow evaluation-card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-star mr-2"></i> 
                @if(auth()->user()->role === 'employee')
                    Your Performance Evaluation
                @else
                    My Performance Overview
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if($userEvaluations->count() > 0)
                <div class="row">
                    <!-- Radar Chart -->
                    <div class="col-md-6">
                        <canvas id="evaluationRadarChart" height="250"></canvas>
                    </div>
                    
                    <!-- Progress Chart -->
                    <div class="col-md-6">
                        <canvas id="evaluationProgressChart" height="250"></canvas>
                    </div>
                </div>
                
                <!-- Rating Details -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="rating-box bg-success text-white p-3 rounded text-center">
                            <h6>Punctuality</h6>
                            <h3>{{ number_format($latestEvaluation->punctuality, 1) }}/10</h3>
                            <div class="progress mt-2" style="height: 10px;">
                                <div class="progress-bar bg-light" 
                                     style="width: {{ $latestEvaluation->punctuality * 10 }}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="rating-box bg-info text-white p-3 rounded text-center">
                            <h6>Work Quality</h6>
                            <h3>{{ number_format($latestEvaluation->work_quality, 1) }}/10</h3>
                            <div class="progress mt-2" style="height: 10px;">
                                <div class="progress-bar bg-light" 
                                     style="width: {{ $latestEvaluation->work_quality * 10 }}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="rating-box bg-primary text-white p-3 rounded text-center">
                            <h6>Teamwork</h6>
                            <h3>{{ number_format($latestEvaluation->teamwork, 1) }}/10</h3>
                            <div class="progress mt-2" style="height: 10px;">
                                <div class="progress-bar bg-light" 
                                     style="width: {{ $latestEvaluation->teamwork * 10 }}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="rating-box bg-dark text-white p-3 rounded text-center">
                            <h6>Overall</h6>
                            <h3>{{ number_format($latestEvaluation->average, 1) }}/10</h3>
                            <div class="progress mt-2" style="height: 10px;">
                                <div class="progress-bar bg-light" 
                                     style="width: {{ $latestEvaluation->average * 10 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Content Based on Role -->
                @if(auth()->user()->role === 'employee')
                <div class="mt-4 p-3 bg-light rounded">
                    <h6><i class="fas fa-comment mr-2"></i> Manager's Feedback</h6>
                    <p class="mb-0">{{ $latestEvaluation->notes ?? 'No comments provided' }}</p>
                </div>
                @elseif(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager']))
                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h6><i class="fas fa-chart-bar mr-2"></i> Evaluation Statistics</h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Total Evaluations
                                            <span class="badge bg-primary rounded-pill">{{ $userEvaluations->count() }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Average Rating
                                            <span class="badge bg-success rounded-pill">{{ number_format($avgOverall, 1) }}/10</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Last Evaluation
                                            <span>{{ $latestEvaluation->evaluation_date->format('M d, Y') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6><i class="fas fa-bullseye mr-2"></i> Performance Goals</h6>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ min($avgOverall * 10, 100) }}%" 
                                             aria-valuenow="{{ $avgOverall * 10 }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ number_format($avgOverall * 10, 1) }}%
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0">
                                        @if($avgOverall >= 8)
                                            Excellent performance! Keep it up.
                                        @elseif($avgOverall >= 6)
                                            Good performance, with room for improvement.
                                        @else
                                            Needs improvement in several areas.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-2"></i> 
                    @if(auth()->user()->role === 'employee')
                        You don't have any evaluations yet.
                    @else
                        No evaluation records available yet.
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

    @elseif(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager']))
        <!-- Admin/Manager Dashboard -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }} Dashboard</h3>
            </div>
            
            <!-- Stats Cards - New Design -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card stat-card-primary h-100">
                    <div class="card-body">
                        <i class="fas fa-users stat-icon"></i>
                        <div class="stat-title">Active Employees</div>
                        <div class="stat-value">{{ $activeEmployees ?? 0 }}</div>
                        <div class="stat-change">
                            <i class="fas fa-info-circle text-primary mr-1"></i>
                            <span>Currently working</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card stat-card-secondary h-100">
                    <div class="card-body">
                        <i class="fas fa-user-slash stat-icon"></i>
                        <div class="stat-title">Inactive Employees</div>
                        <div class="stat-value">{{ $inactiveEmployees ?? 0 }}</div>
                        <div class="stat-change">
                            <i class="fas fa-info-circle text-secondary mr-1"></i>
                            <span>Not active</span>
                        </div>
                    </div>
                </div>
            </div>

           

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card stat-card-danger h-100">
                    <div class="card-body">
                        <i class="fas fa-user-times stat-icon"></i>
                        <div class="stat-title">Absent Today</div>
                        <div class="stat-value">{{ $absentToday ?? 0 }}</div>
                        <div class="stat-change">
                            <i class="fas fa-exclamation-circle text-danger mr-1"></i>
                            <span>Missing</span>
                        </div>
                    </div>
                </div>
            </div>

      @if(isset($todayBirthdays) && count($todayBirthdays) > 0)
<div class="col-12 mb-4">
    <div class="card shadow border-0">
        <div class="card-header py-3 bg-gradient-primary">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-birthday-cake mr-2"></i> birthdays Today
                </h6>
                <span class="badge badge-light">{{ count($todayBirthdays) }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($todayBirthdays as $birthday)
                <div class="col-md-3 mb-4">
                    <div class="birthday-card h-100">
                        <div class="card-body text-center">
                            <h5 class="user-name mb-2">{{ $birthday->name }}</h5>
                            <div class="birthday-info mb-3">
                                <i class="fas fa-birthday-cake"></i>
                                @if(isset($birthday->birth_date))
                                    {{ \Carbon\Carbon::parse($birthday->birth_date)->format('d M') }}
                                @endif
                            </div>
                            <a href="{{ route('birthdays.index') }}" class="btn btn-primary btn-sm btn-block send-wish-btn">
                                <i class="fas fa-gift mr-1"></i> Send Wish
                            </a>
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
                    <div class="card-header py-3 bg-gradient-info">
                        <h6 class="m-0 font-weight-bold text-white">📊 Statistics Overview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="stat-card stat-card-success h-100">
                                    <div class="card-body">
                                        <i class="fas fa-umbrella-beach stat-icon"></i>
                                        <div class="stat-title">Vacations</div>
                                        <div class="stat-value">{{ $vacationStats['total'] ?? 0 }}</div>
                                        <div class="stat-change">
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

                            <div class="col-md-6 mb-4">
                                <div class="stat-card stat-card-warning h-100">
                                    <div class="card-body">
                                        <i class="fas fa-sign-out-alt stat-icon"></i>
                                        <div class="stat-title">Leaves</div>
                                        <div class="stat-value">{{ $leaveStats['total'] ?? 0 }}</div>
                                        <div class="stat-change">
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
            </div>

            @if(auth()->user()->role === 'department_manager' && isset($deptAttendanceData) && isset($deptLeaveTypes))
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 bg-gradient-primary">
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
    @if($user->evaluations->count() > 0)
// Evaluation Radar Chart
new Chart(document.getElementById('evaluationRadarChart'), {
    type: 'radar',
    data: {
        labels: ['Punctuality', 'Work Quality', 'Teamwork'],
        datasets: [{
            label: 'Your Skills',
            data: [
                {{ $user->latestEvaluation->punctuality }},
                {{ $user->latestEvaluation->work_quality }},
                {{ $user->latestEvaluation->teamwork }}
            ],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
            pointRadius: 4
        }]
    },
    options: {
        scales: {
            r: {
                angleLines: { display: true },
                suggestedMin: 0,
                suggestedMax: 10
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
});

// Evaluation Progress Chart
const evaluationDates = @json($user->evaluations->pluck('evaluation_date')->map(function($date) {
    return \Carbon\Carbon::parse($date)->format('M Y');
}));
const evaluationAverages = @json($user->evaluations->pluck('average'));

new Chart(document.getElementById('evaluationProgressChart'), {
    type: 'line',
    data: {
        labels: evaluationDates,
        datasets: [{
            label: 'Performance Trend',
            data: evaluationAverages,
            fill: false,
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.1,
            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
            pointRadius: 5
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: 10
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
});
@endif
});
</script>


@endsection

@section('styles')
<style>
    /* تحسينات متقدمة للبطاقات */
    :root {
        --primary-color: #4e73df;
        --secondary-color: #858796;
        --success-color: #1cc88a;
        --info-color: #36b9cc;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
        --light-color: #f8f9fc;
        --dark-color: #5a5c69;
        --card-shadow-hover: 0 15px 30px rgba(0, 0, 0, 0.12);
        --card-border-radius: 12px;
        --card-padding: 1.75rem;
        --card-header-padding: 1.25rem 1.75rem;
        --card-transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
    }

    /* بطاقة إحصائيات محسنة */
    .stat-card {
        border: none;
        border-radius: var(--card-border-radius);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
        transition: var(--card-transition);
        overflow: hidden;
        position: relative;
        z-index: 1;
        background: white;
        margin-bottom: 1.75rem;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--card-accent-color), transparent);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--card-shadow-hover);
    }

    .stat-card .card-body {
        padding: var(--card-padding);
        position: relative;
    }

    .stat-card .stat-icon {
        position: absolute;
        right: 1.75rem;
        top: 1.75rem;
        font-size: 2.5rem;
        opacity: 0.15;
        color: var(--card-accent-color);
    }

    .stat-card .stat-title {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .stat-card .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--card-accent-color);
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .stat-card .stat-change {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }

    /* ألوان البطاقات */
    .stat-card-primary {
        --card-accent-color: var(--primary-color);
    }

    .stat-card-secondary {
        --card-accent-color: var(--secondary-color);
    }

    .stat-card-success {
        --card-accent-color: var(--success-color);
    }

    .stat-card-info {
        --card-accent-color: var(--info-color);
    }

    .stat-card-warning {
        --card-accent-color: var(--warning-color);
    }

    .stat-card-danger {
        --card-accent-color: var(--danger-color);
    }

    /* بطاقة أعياد الميلاد المحسنة */
  .birthday-card {
    border-radius: 10px;
    transition: all 0.3s ease;
    background: #ffffff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.birthday-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

    .birthday-card .profile-img-container {
        width: 100%;
        height: 100px;
        overflow: hidden;
        position: relative;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .birthday-card .profile-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        position: absolute;
        bottom: -40px;
        left: 50%;
        transform: translateX(-50%);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .birthday-card:hover .profile-img {
        transform: translateX(-50%) scale(1.05);
    }

    .birthday-card .card-body {
        padding: 3rem 1.5rem 1.5rem;
        text-align: center;
    }

    .birthday-card .user-name {
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
        color: #343a40;
    }

    .birthday-card .user-position {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .birthday-card .birthday-info {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .birthday-card .birthday-info i {
        margin-right: 0.5rem;
    }

    .user-name {
    font-weight: 600;
    color: #2d3748;
    font-size: 1.1rem;
}

.birthday-info {
    color: #6b7280;
    font-size: 0.9rem;
}


.send-wish-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: auto;
    min-width: 120px;
}

.send-wish-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
}

    /* بطاقة التقييم المحسنة */
    .evaluation-card {
        border: none;
        border-radius: var(--card-border-radius);
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        transition: var(--card-transition);
        background: white;
    }

    .evaluation-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--card-shadow-hover);
    }

    .evaluation-card .card-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        padding: var(--card-header-padding);
        border-bottom: none;
    }

    .evaluation-card .overall-rating {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        font-weight: 700;
        font-size: 1.25rem;
    }

    .evaluation-card .rating-item {
        padding: 1rem;
        border-radius: 8px;
        background: rgba(0, 0, 0, 0.02);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .evaluation-card .rating-item:hover {
        background: rgba(0, 0, 0, 0.05);
        transform: translateX(5px);
    }

    .evaluation-card .rating-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .evaluation-card .rating-title i {
        margin-right: 0.5rem;
        font-size: 1.1rem;
    }

    .evaluation-card .progress {
        height: 10px;
        border-radius: 5px;
    }

    .evaluation-card .rating-value {
        font-weight: 700;
        margin-left: 0.5rem;
    }

    .notes-box {
        border-left: 0.25rem solid var(--info-color);
        background-color: rgba(54, 185, 204, 0.05);
        padding: 1rem;
        border-radius: 0.25rem;
    }

    /* تأثيرات إضافية */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .birthday-card:hover .profile-img {
        animation: float 3s ease-in-out infinite;
    }

    /* تجاوبية */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1.25rem;
        }
        
        .stat-card .stat-icon {
            font-size: 2rem;
            top: 1.25rem;
            right: 1.25rem;
        }
        
        .stat-card .stat-value {
            font-size: 1.5rem;
        }
        
        .birthday-card .profile-img {
            width: 70px;
            height: 70px;
            bottom: -35px;
        }
        
        .birthday-card .card-body {
            padding: 2.5rem 1rem 1rem;
        }
    }
</style>
@endsection