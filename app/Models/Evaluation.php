<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
    protected $fillable = [
    'user_id',
    'evaluation_date',
    'punctuality',
    'work_quality',
    'teamwork',
    'notes'
];

public function user()
{
    return $this->belongsTo(User::class);
}

// حساب المتوسط
public function getAverageAttribute()
{
    return ($this->punctuality + $this->work_quality + $this->teamwork) / 3;
}

protected $casts = [
    'evaluation_date' => 'date',
    // other casts...
];

// في ملف Evaluation.php
public function calculatePunctuality()
{
    $employee = $this->user;
    $attendanceRecords = $employee->attendances()
        ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
        ->get();

    $totalDays = $attendanceRecords->count();
    $onTimeDays = $attendanceRecords->filter(function ($record) {
        return $record->arrival_time <= $record->shift->start_time; // افترض أن لديك نموذج Shift
    })->count();

    return $totalDays > 0 ? round(($onTimeDays / $totalDays) * 10, 1) : 0; // تقييم من 1 إلى 10
}
}
