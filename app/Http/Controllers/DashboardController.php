<?php

namespace App\Http\Controllers;

use App\Models\{
    User,
    Attendance,
    Vacation,
    Leave,
    Evaluation,
    Department,
    Activity
};
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Absence;


class DashboardController extends Controller
{
    // Cache expiration time in minutes
     const CACHE_EXPIRE = 30; // دقائق

    public function index()
    {
        $user = auth()->user();
        $cacheKey = 'dashboard_' . $user->id;
            $departmentId = $user->department_id;


        // إذا كان الكاش موجوداً، استرجاعه مباشرة
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // إنشاء البيانات
        $view = $user->role === 'employee'
            ? $this->employeeDashboard($user)
            : $this->adminDashboard($user);

        // تخزين محتوى العرض بدلاً من كائن View
        $content = $view->render();

        // تخزين المحتوى في الكاش
        Cache::put($cacheKey, $content, self::CACHE_EXPIRE);

        return $view;
    }
   protected function employeeDashboard(User $user)
    {
        $data = [
            'absentDays' => $this->getEmployeeAbsentDays($user),
            'approvedVacations' => $this->getEmployeeApprovedVacations($user),
            'approvedLeaves' => $this->getEmployeeApprovedLeaves($user),
            'todayBirthdays' => $this->getTodayBirthdays(),
            'latestEvaluation' => $this->getLatestEvaluation($user),
            'attendanceStatus' => $this->getTodayAttendanceStatus($user)
        ];

        return view('dashboard', $data);
    }


protected function adminDashboard(User $user)
    {
        $departmentFilter = $user->role === 'department_manager' 
            ? ['department_id' => $user->department_id] 
            : [];

        $data = [
            'stats' => $this->getAdminStats($user, $departmentFilter),
            'presentEmployees' => $this->getPresentEmployees($departmentFilter),
            'todayBirthdays' => $this->getTodayBirthdays($departmentFilter),
            'recentActivities' => $this->getRecentActivities(),
            'departmentData' => $user->role === 'department_manager' 
                ? $this->getDepartmentData($user->department_id) 
                : null
        ];

        return view('dashboard', $data);
    }

    /**********************
     * Employee Dashboard Helpers
     **********************/
    protected function getEmployeeAbsentDays(User $user)
    {
        return $user->attendances()
            ->where('status', 'like', '%Absent%')
            ->whereMonth('date', now()->month)
            ->count();
    }

    protected function getEmployeeApprovedVacations(User $user)
    {
        return $user->vacations()
            ->where('status', 'approved')
            ->whereYear('created_at', now()->year)
            ->count();
    }

    protected function getEmployeeApprovedLeaves(User $user)
    {
        return method_exists($user, 'leave') 
            ? $user->leave()
                ->where('status', 'approved')
                ->whereYear('created_at', now()->year)
                ->count()
            : 0;
    }

    protected function getTodayAttendanceStatus(User $user)
    {
        return $user->attendances()
            ->whereDate('date', today())
            ->first();
    }

    /**********************
     * Admin Dashboard Helpers
     **********************/
    protected function getAdminStats(User $user, array $filters = [])
    {
        $userQuery = User::when(!empty($filters), function($q) use ($filters) {
            $q->where($filters);
        });

        return [
            'employees' => [
                'total' => $userQuery->count(),
                'active' => $userQuery->where('status', 'active')->count(),
                'inactive' => User::onlyTrashed()->when(!empty($filters), function($q) use ($filters) {
                    $q->where($filters);
                })->count()
            ],
            'attendance' => [
                'present' => $this->getPresentTodayCount($filters),
                'absent' => $this->getAbsentTodayCount($filters),
                'late' => $this->getLateTodayCount($filters)
            ],
            'vacations' => $this->getVacationStats($filters),
            'leaves' => $this->getLeaveStats($filters),
            'absences' => $this->getAbsenceStats($filters)
        ];
    }

protected function getPresentEmployees(array $filters = [])
{
    return User::with(['attendances' => function($q) {
            $q->whereDate('date', today())
              ->whereNotNull('check_in')
              ->orderByDesc('check_in')
              ->limit(1);
        }])
        ->whereHas('attendances', function($q) {
            $q->whereDate('date', today())
              ->whereNotNull('check_in');
        })
        ->when(!empty($filters), function($q) use ($filters) {
            $q->where($filters);
        })
        ->select('id', 'name', 'photo_url', 'department_id')
        ->orderBy('name')
        ->take(10)
        ->get()
        ->map(function($user) {
            $user->latest_attendance = $user->attendances->first();
            return $user;
        });
}
protected function getTodayAttendanceStats($departmentId)
{
    return Attendance::whereHas('user', function($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })
        ->whereDate('date', today())
        ->selectRaw('status, count(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status');
}
    protected function getPresentTodayCount(array $filters = [])
    {
        return User::whereHas('attendances', function($q) {
            $q->whereDate('date', today())
              ->whereNotNull('check_in');
        })
        ->when(!empty($filters), function($q) use ($filters) {
            $q->where($filters);
        })
        ->count();
    }

    protected function getVacationStats(array $filters = [])
    {
        $query = Vacation::when(!empty($filters), function($q) use ($filters) {
            $q->whereHas('user', function($q) use ($filters) {
                $q->where($filters);
            });
        });

        return $this->getStatusStats($query);
    }

    protected function getLeaveStats(array $filters = [])
    {
        if (!Schema::hasTable('leave')) {
            return $this->emptyStats();
        }

        $query = Leave::when(!empty($filters), function($q) use ($filters) {
            $q->whereHas('user', function($q) use ($filters) {
                $q->where($filters);
            });
        });

        return $this->getStatusStats($query);
    }

    protected function getStatusStats($query)
    {
        $total = $query->count();
        $approved = $query->where('status', 'approved')->count();

        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $query->where('status', 'pending')->count(),
            'rejected' => $query->where('status', 'rejected')->count(),
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100) : 0
        ];
    }

    /**********************
     * Shared Helpers
     **********************/
    protected function getTodayBirthdays(array $filters = [])
    {
        return User::when(!empty($filters), function($q) use ($filters) {
            $q->where($filters);
        })
        ->whereMonth('birth_date', today()->month)
        ->whereDay('birth_date', today()->day)
        ->select('id', 'name', 'photo_url')
        ->get();
    }

 protected function getLatestEvaluation(User $user)
{
    return $user->evaluations()
        ->with(['evaluator' => function($query) {
            $query->select('id', 'name', 'photo_url');
        }])
        ->latest()
        ->first();
}

    protected function getRecentActivities()
    {
        return Activity::with('user')
            ->latest()
            ->take(5)
            ->get();
    }

    protected function getDepartmentData($departmentId)
    {
        return [
            'attendance' => [
                'monthly' => $this->getMonthlyAttendanceStats($departmentId),
                'today' => $this->getTodayAttendanceStats($departmentId)
            ],
            'leave_types' => $this->getDepartmentLeaveTypes($departmentId)
        ];
    }

    protected function getMonthlyAttendanceStats($departmentId)
    {
        return Attendance::whereHas('user', fn($q) => $q->where('department_id', $departmentId))
            ->selectRaw('status, count(*) as count')
            ->whereMonth('date', today()->month)
            ->groupBy('status')
            ->pluck('count', 'status');
    }

    protected function emptyStats()
    {
        return [
            'total' => 0,
            'approved' => 0,
            'pending' => 0,
            'rejected' => 0,
            'approval_rate' => 0
        ];
    }
    protected function getAbsentTodayCount(array $filters = [])
{
    return User::whereDoesntHave('attendances', function($q) {
            $q->whereDate('date', today());
        })
        ->when(!empty($filters), function($q) use ($filters) {
            $q->where($filters);
        })
        ->count();
}

protected function getLateTodayCount(array $filters = [])
{
    return Attendance::whereHas('user', function($q) use ($filters) {
            $q->when(!empty($filters), function($q) use ($filters) {
                $q->where($filters);
            });
        })
        ->whereDate('date', today())
        ->where('status', 'late')
        ->count();
}
protected function getAbsenceStats(array $filters = [])
{
    // التحقق أولاً من وجود جدول الغياب
    if (!Schema::hasTable('absences')) {
        return $this->emptyStats();
    }

    $query = Absence::when(!empty($filters), function($q) use ($filters) {
        $q->whereHas('user', function($q) use ($filters) {
            $q->where($filters);
        });
    });

    return [
        'total' => $query->count(),
        'justified' => $query->where('status', 'approved')->count(),
        'unjustified' => $query->where('status', '!=', 'approved')->count(),
        'percentage' => $query->count() > 0 
            ? round(($query->where('status', 'approved'))->count() / $query->count() * 100)
            : 0
    ];
}

protected function getDepartmentLeaveTypes($departmentId)
{
    // التحقق أولاً من وجود جدول leaves
    if (!Schema::hasTable('leaves')) {
        return [];
    }

    return Leave::whereHas('user', function($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })
        ->selectRaw('type, count(*) as count')
        ->groupBy('type')
        ->pluck('count', 'type')
        ->toArray();
}
}