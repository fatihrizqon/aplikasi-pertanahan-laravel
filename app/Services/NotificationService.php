<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use App\Events\UserManagementEvent;
use Illuminate\Support\Facades\Notification;
use App\Services\Interfaces\INotificationService;

class NotificationService implements INotificationService
{
    private static array $dispatched = [];

    public function userManagement(User $actor, iterable $targets, string $action): void
    {
        $key = md5($actor->id . serialize($targets->pluck('id')) . $action);
        if (isset(self::$dispatched[$key])) return;
        self::$dispatched[$key] = true;

        event(new UserManagementEvent(
            actor: $actor,
            targets: $targets,
            action: $action
        ));
    }
}
