<?php

namespace App\Policies;

use App\Models\BadgesKit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BadgesKitPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BadgesKit $badgesKit): bool
    {
        return $user->can('view_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BadgesKit $badgesKit): bool
    {
        return $user->can('update_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BadgesKit $badgesKit): bool
    {
        return $user->can('delete_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, BadgesKit $badgesKit): bool
    {
        return $user->can('force_delete_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, BadgesKit $badgesKit): bool
    {
        return $user->can('restore_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, BadgesKit $badgesKit): bool
    {
        return $user->can('replicate_badges::kits::badges::kit');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_badges::kits::badges::kit');
    }
}
