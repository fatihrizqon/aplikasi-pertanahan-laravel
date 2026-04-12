<?php

namespace App\Listeners;

use App\Events\Interfaces\INotificationEvent;
use Illuminate\Event\Attributes\AsEventListener;
use Illuminate\Support\Facades\Log;

#[AsEventListener]
class DispatchNotificationListener
{
    private static array $notified = [];

    public function handle(INotificationEvent $event): void
    {
        foreach ($event->targets() as $user) {
            $key = md5($user->id . serialize($event->notification()->toArray($user)));

            if (isset(self::$notified[$key])) continue;

            self::$notified[$key] = true;

            $user->notify($event->notification());
        }
    }
}
