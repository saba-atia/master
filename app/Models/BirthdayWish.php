<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BirthdayWish extends Model
{
    protected $fillable = ['user_id', 'receiver_id','sender_id', 'message'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
   public function sender()
{
    return $this->belongsTo(User::class, 'sender_id')->withDefault([
        'name' => 'Deleted User',
        'profile_photo_path' => null
    ]);
}
     public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id'); // أو 'user_id'
    }
}