<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Broadcasting\Channel;
use App\Enum\NotificationCategoryEnum;
use Illuminate\Queue\SerializesModels;
use App\Notifications\GeneralNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use App\Events\Interfaces\INotificationEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserManagementEvent implements INotificationEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        private User $actor,
        User|Collection|array $targets,
        private string $action
    ) {
        $this->targets = collect($targets);
    }

    protected Collection $targets;

    public function targets(): iterable
    {
        return $this->targets;
    }

    public function notification(): Notification
    {
        return new GeneralNotification([
            'title'   => 'User Management',
            'message' => "{$this->actor->name} {$this->action} user",
            'category'=> NotificationCategoryEnum::USER->value,
        ]);
    }
}
