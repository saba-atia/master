<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Evaluation;
use App\Models\Absence;



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // use SoftDeletes;
    use Notifiable, SoftDeletes;
        protected $dates = ['deleted_at'];


    /**
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'photo_url',
        'emergency_contact',
        'birth_date',
        'department_id',
        'status',
    ];
    
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date' 
    ];

    public const ROLE_EMPLOYEE        = 'employee';
    public const ROLE_ADMIN           = 'admin';
    public const ROLE_SUPER_ADMIN     = 'super_admin';
    public const ROLE_DEPARTMENT_MANAGER = 'department_manager';  

    /**
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     *
     */

     
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leave()
    {
        return $this->hasMany(Leave::class);
    }

    public function isAdmin()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
    }

    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdminOrSuperAdmin()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
    }

    public function isDepartmentManager()
    {
        return $this->role === self::ROLE_DEPARTMENT_MANAGER;
    }
    
    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function isDepartmentHead()
    {
        return $this->role === 'department_head';
    }

    
 

    public function vacations()
{
    return $this->hasMany(Vacation::class);
}

public function canApproveVacation(Vacation $vacation): bool
{
    if ($this->isSuperAdmin()) {
        return true;
    }

    if ($this->isAdmin()) {
        return in_array($vacation->user->role, [self::ROLE_ADMIN, self::ROLE_DEPARTMENT_MANAGER]);
    }

    if ($this->isDepartmentManager()) {
        return $vacation->user->department_id === $this->department_id && 
               $vacation->user_id !== $this->id;
    }

    return false;
}

public function canApproveLeave($leave): bool
{
    if ($this->isDepartmentManager() && $leave->status === 'pending') {
        return true;
    }

    if ($this->isAdmin() && $leave->status === 'pending_admin') {
        return true;
    }

    if ($this->isSuperAdmin() && $leave->status === 'pending_super_admin') {
        return true;
    }

    return false;
}
public function isRegularEmployee()
{
    return $this->role === self::ROLE_EMPLOYEE;
}

    
public function getBirthdayAttribute()
{
    if (!$this->birth_date) {
        return null;
    }
    return $this->birth_date->format('Y-m-d');
}

    
  
public function greetings()
{
    return $this->hasMany(Greeting::class, 'receiver_id');
}

  public function getAvatarUrlAttribute()
    {
        if (!$this->photo) {
            return null;
        }
        
        return Storage::disk('public')->exists($this->photo) 
            ? Storage::url($this->photo)
            : null;
    }

public static function birthdaysThisWeek()
{
    if (!Schema::hasColumn('users', 'birth_date')) {
        return collect();
    }

    $start = now()->startOfWeek();
    $end = now()->endOfWeek();
    
    return self::whereNotNull('birth_date')
        ->whereMonth('birth_date', now()->month)
        ->whereDay('birth_date', '>=', $start->day)
        ->whereDay('birth_date', '<=', $end->day)
        ->orderByRaw('DAY(birth_date)')
        ->get();
}
public function isBirthdayToday()
{
    if (!$this->birth_date) {
        return false;
    }
    return $this->birth_date->isBirthday();
}
public function birthdayWishes()
{
    return $this->hasMany(BirthdayWish::class)->with('sender');
}
public function receivedWishes()
{
    return $this->hasMany(BirthdayWish::class, 'receiver_id');
}

public function sentWishes()
{
    return $this->hasMany(BirthdayWish::class, 'sender_id');
}
public function getAvatarColorAttribute()
{
    $hash = md5($this->name);
    return sprintf('#%s', substr($hash, 0, 6));
}

public function getInitialsAttribute()
{
    $names = explode(' ', $this->name);
    $initials = '';
    foreach ($names as $n) {
        $initials .= strtoupper(substr($n, 0, 1));
    }
    return substr($initials, 0, 2);
}

public function evaluations()
{
    return $this->hasMany(Evaluation::class);
}
public function hasRole($role)
{
    return $this->role === $role;
}
public function hasAnyRole(array $roles)
{
    return in_array($this->role, $roles);
}

// في ملف User.php
public function latestEvaluation()
{
    return $this->hasOne(Evaluation::class)->latest();
}

public function currentLeave()
{
    return $this->hasOne(Leave::class)
        ->where('status', 'approved')
        ->where('start_time', '<=', now())
        ->where('end_time', '>=', now());
}
 public function getAvatarUrl()
    {
        if (!$this->avatar_url) {
            return asset('images/default-avatar.png');
        }

        if (str_starts_with($this->avatar_url, 'http')) {
            return $this->avatar_url;
        }

        return asset('storage/'.$this->avatar_url);
    }

    // Add these scopes to your User model
public function scopeActive($query)
{
    return $query->where('status', 'active');
}

public function scopeInactive($query)
{
    return $query->where('status', 'inactive');
}

public function scopePresentToday($query)
{
    return $query->whereHas('attendances', function($q) {
        $q->whereDate('date', today())->whereNotNull('check_in');
    });
}

public function scopeTodayBirthdays($query)
{
    return $query->whereMonth('birth_date', today()->month)
                ->whereDay('birth_date', today()->day);
}


public function approvedVacations()
{
    return $this->hasMany(Vacation::class)->where('status', 'approved');
}

public function approvedLeaves()
{
    return $this->hasMany(Leave::class)->where('status', 'approved');
}
public function absences()
{
    return $this->hasMany(Absence::class);
}
public function absentDaysThisMonth()
{
    // Implement your logic to calculate absent days this month
    // For example:
    return $this->absences()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count();
}

protected function getAdminStats(User $user, array $filters = [])
{
    $userQuery = User::when(!empty($filters), function($q) use ($filters) {
        $q->where($filters);
    });

    return [
        'employees' => [
            'total' => $userQuery->count(),
            'active' => $userQuery->where('status', 'active')->count(),
            'inactive' => User::onlyTrashed()
                ->when(!empty($filters), function($q) use ($filters) {
                    $q->where($filters);
                })->count()
        ],
        'attendance' => [
            'present' => $this->getPresentTodayCount($filters),
            'absent' => $this->getAbsentTodayCount($filters),
            'late' => $this->getLateTodayCount($filters)
        ],
        'vacations' => $this->getVacationStats($filters),
        'leave' => $this->getLeaveStats($filters),
        'absences' => $this->getAbsenceStats($filters)
    ];
}

public function latestAttendance()
{
    return $this->hasOne(Attendance::class)->latestOfMany();
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
}

