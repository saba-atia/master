<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // use SoftDeletes;

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
        'department_id'
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

    
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    

    public function vacations()
{
    return $this->hasMany(Vacation::class);
}
public function approvedVacations()
{
    return $this->hasMany(Vacation::class, 'approved_by');
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
    if (!Schema::hasColumn('users', 'birthdate')) {
        return collect();
    }

    $start = now()->startOfWeek();
    $end = now()->endOfWeek();
    
    return self::whereNotNull('birthdate')
        ->whereMonth('birthdate', now()->month)
        ->whereDay('birthdate', '>=', $start->day)
        ->whereDay('birthdate', '<=', $end->day)
        ->orderByRaw('DAY(birthdate)')
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
    if (is_array($role)) {
        return in_array($this->role, $role);
    }
    return $this->role === $role;
}

// في ملف User.php
public function latestEvaluation()
{
    return $this->hasOne(Evaluation::class)->latest();
}
}

