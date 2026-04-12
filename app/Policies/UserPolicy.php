<?php

namespace App\Policies;

use App\Models\User;
use App\Enum\RolesEnum;
use App\Traits\OwnershipTrait;

class UserPolicy
{
    use OwnershipTrait;

    /**
     * SuperAdmin bypass everything
     */
    public function before(User $user, string $ability)
    {
        if ($user->hasRole(RolesEnum::SuperAdmin->value)) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_user');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('view_user') && $this->isOwner($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->can('create_user');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('update_user') && $this->isOwner($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('delete_user') && $this->isOwner($user, $model);
    }

    public function lock(User $user, User $model): bool
    {
        return $user->can('activate_user') && $this->isOwner($user, $model);
    }

    public function export(User $user): bool
    {
        return $user->can('export_user');
    }

    public function import(User $user): bool
    {
        return $user->can('import_user');
    }

    public function restore(User $user, User $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
