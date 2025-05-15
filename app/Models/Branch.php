<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
 protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude'
    ];    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    public function users()
    {
        return $this->hasMany(User::class);
    }

}