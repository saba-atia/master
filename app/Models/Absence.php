<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'user_id', 'date', 'reason', 'status' // Add other relevant fields
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}