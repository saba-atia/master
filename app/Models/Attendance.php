<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = ['user_id', 'check_in', 'check_out', 'date'];
    
    protected $dates = ['check_in', 'check_out', 'date'];
    
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getWorkingHoursAttribute()
    {
        if ($this->check_in && $this->check_out) {
            return round($this->check_out->diffInMinutes($this->check_in) / 60, 2);
        }
        return null;
    }
    
    public function getStatusAttribute()
    {
        if (!$this->check_in) {
            return 'Pending';
        }
        
        if (!$this->check_out) {
            return 'In Progress';
        }
        
        $workingHours = $this->working_hours;
        
        // إذا أكمل 8 ساعات أو أكثر
        if ($workingHours >= 8) {
            return 'Completed';
        }
        
        // إذا أكمل أقل من 8 ساعات
        return 'Not Completed';
    }
}