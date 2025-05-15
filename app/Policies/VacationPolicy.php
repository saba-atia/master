<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Vacation;
use App\Models\User;

class VacationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vacation $vacation): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vacation $vacation)
{
    // الإدمن يمكنه الموافقة على أي طلب
    if ($user->role === 'admin' || $user->role === 'super_admin') {
        return true;
    }

    // رئيس القسم يمكنه الموافقة على طلبات موظفي قسمه فقط
    if ($user->role === 'department_manager' && 
        $vacation->user->department_id === $user->department_id) {
        return true;
    }

    return false;
}

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vacation $vacation): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vacation $vacation): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vacation $vacation): bool
    {
        //
    }
}
