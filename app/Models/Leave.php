<?php

// app/Models/Leave.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
    ];

    protected $dates = ['start_date', 'end_date'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


