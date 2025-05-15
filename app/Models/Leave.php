<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'user_id',
        'department_id',
        'type',
        'start_time',
        'end_time',
        'duration_hours',
        'status', // pending, department_approved, approved, rejected
        'reason',
        'approved_by',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    // نطاقات الاستعلام
    public function scopePending($query) {
        return $query->where('status', 'pending');
    }
    
    public function scopeDepartmentApproved($query) {
        return $query->where('status', 'department_approved');
    }
    
    public function scopeApproved($query) {
        return $query->where('status', 'approved');
    }
}