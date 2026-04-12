<?php

namespace App\Traits;

use App\Models\User;
use App\Enum\RolesEnum;

trait OwnershipTrait
{
    protected function isOwner(User $user, $model): bool
    {
        if ($user->hasRole(RolesEnum::SuperAdmin->value)) {
            return true;
        }

        if ($user->hasRole(RolesEnum::Admin->value)) {
            return $model->created_by === $user->id;
        }

        return false;
    }
}
