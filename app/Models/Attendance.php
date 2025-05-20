<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 
        'check_in', 
        'check_out', 
        'date',
        'status', 
        'working_hours',
        'leave_id',
        'required_hours'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date'
    ];
    
    protected $appends = ['status_icon', 'day_status', 'is_on_leave'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

public function calculateWorkingHours()
{
    if ($this->check_in && $this->check_out) {
        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);
        
        $totalSeconds = $checkOut->diffInSeconds($checkIn);
        $workingHours = $totalSeconds / 3600;
        
        $this->working_hours = round($workingHours, 2);
        $this->status = 'Completed';
        $this->save();
        
        return $this->working_hours;
    }
    
    return 0;
}

    public function getCheckInTimeAttribute()
    {
        return $this->check_in ? $this->check_in->format('H:i:s') : '--';
    }

    public function getCheckOutTimeAttribute()
    {
        return $this->check_out ? $this->check_out->format('H:i:s') : '--';
    }

    public function getStatusIconAttribute()
    {
        $icons = [
            'present' => '✔',
            'absent' => '✖',
            'late' => '⏰',
            'on_leave' => '🌴',
            'half_day' => '½'
        ];
        
        return $icons[$this->status] ?? '';
    }

    public function getIsOnLeaveAttribute()
    {
        if ($this->status !== 'on_leave') {
            return false;
        }

        if ($this->leave) {
            $now = now();
            return $now->between(
                $this->leave->start_time,
                $this->leave->end_time
            );
        }

        return true;
    }

    public function getDayStatusAttribute()
    {
        return [
            'status' => $this->status,
            'icon' => $this->status_icon,
            'check_in' => $this->check_in_time,
            'check_out' => $this->check_out_time,
            'is_on_leave' => $this->is_on_leave
        ];
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeOnLeave($query)
    {
        return $query->where('status', 'on_leave');
    }

    public function scopeAbsent($query, $date = null)
    {
        $date = $date ?: today();
        $presentUserIds = self::whereDate('date', $date)
                          ->where('status', '!=', 'on_leave')
                          ->pluck('user_id');
        
        return User::whereNotIn('id', $presentUserIds);
    }

    public function isLate(): bool
    {
        if (!$this->check_in || $this->status === 'on_leave') {
            return false;
        }
        
        $lateThreshold = Carbon::createFromTime(9, 0, 0); // 9 AM
        return $this->check_in->gt($lateThreshold);
    }

    public function updateLeaveStatus()
    {
        if ($this->is_on_leave && $this->status !== 'on_leave') {
            $this->update(['status' => 'on_leave']);
        } elseif (!$this->is_on_leave && $this->status === 'on_leave') {
            $this->update(['status' => 'present']);
        }
    }
    
}