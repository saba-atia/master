<?php

namespace App\Providers;
use Illuminate\Support\Facades\Gate;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    // protected $policies = [
    //     Leave::class => LeavePolicy::class,

    // ];

    

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();
    
        Gate::define('manage-users', function ($user) {
    return in_array($user->role, ['super_admin', 'admin']);
});

Gate::define('view-reports', function ($user) {
    return in_array($user->role, ['super_admin', 'admin', 'department_manager']);
});

        
    }

}
