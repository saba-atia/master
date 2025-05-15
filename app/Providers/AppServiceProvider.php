<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Carbon::macro('isBirthdayThisWeek', function() {
            return $this->isBirthday() || 
                   ($this->month == now()->month && 
                    $this->day >= now()->startOfWeek()->day && 
                    $this->day <= now()->endOfWeek()->day);
        });
    }
}
