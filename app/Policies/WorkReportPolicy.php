<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkReport;

class WorkReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkReport $workReport): bool
    {
        return $user->isAdmin() || $workReport->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkReport $workReport): bool
    {
        return $user->isAdmin() || $workReport->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkReport $workReport): bool
    {
        return $user->isAdmin() || $workReport->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkReport $workReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkReport $workReport): bool
    {
        return false;
    }
}
