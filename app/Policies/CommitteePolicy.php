<?php

namespace App\Policies;

use App\Models\Committee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommitteePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_committees::committee');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Committee $committee): bool
    {
        return $user->can('view_committees::committee');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_committees::committee');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Committee $committee): bool
    {
        return $user->can('update_committees::committee');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Committee $committee): bool
    {
        return $user->can('delete_committees::committee');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_committees::committee');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Committee $committee): bool
    {
        return $user->can('force_delete_committees::committee');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_committees::committee');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Committee $committee): bool
    {
        return $user->can('restore_committees::committee');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_committees::committee');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Committee $committee): bool
    {
        return $user->can('replicate_committees::committee');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_committees::committee');
    }
}
