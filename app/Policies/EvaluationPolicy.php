<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Auth\Access\HandlesAuthorization;

class EvaluationPolicy
{
    use HandlesAuthorization;

   public function viewAny(User $user)
{
   return $user->role === 'super_admin' || 
           $user->role === 'admin' || 
           $user->role === 'department_manager';
}

    public function view(User $user, Evaluation $evaluation)
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return true;
        }

        // Department manager can only view evaluations of their department members or their own
        return $user->hasRole('department_manager') && 
               ($evaluation->user_id == $user->id || 
                $evaluation->user->department_id == $user->department_id);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }
    public function before(User $user)
{
    if ($user->role === 'super_admin') {
        return true;
    }
}

    public function update(User $user, Evaluation $evaluation)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Admin can update any evaluation
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Evaluation $evaluation)
    {
        return $user->hasRole('super_admin');
    }

    public function emailEvaluations(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
        
    }
    
}