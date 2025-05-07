<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'birth_date' // أضف هذا
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date' // أضف هذا
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
    return $this->birth_date ? $this->birth_date->format('m-d') : null;
}
    
  
}

