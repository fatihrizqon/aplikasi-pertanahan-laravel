<?php

namespace App\Events\Interfaces;

use Illuminate\Notifications\Notification;

interface INotificationEvent
{
    public function targets(): iterable;
    public function notification(): Notification;
}
