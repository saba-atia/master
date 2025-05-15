<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Notifications\BirthdayNotification;

class CheckBirthdays extends Command
{
    protected $signature = 'birthday:check';
    protected $description = 'Check for user birthdays and send notifications';
    
    public function handle()
    {
        $today = now()->format('m-d');
        $birthdayUsers = User::whereNotNull('birthdate')
            ->whereRaw("DATE_FORMAT(birthdate, '%m-%d') = ?", [$today])
            ->get();
        
        foreach ($birthdayUsers as $user) {
            $user->notify(new BirthdayNotification());
        }
        
        $this->info('Birthday check completed. Found '.$birthdayUsers->count().' users with birthdays today.');
    }
}