<?php

namespace Denngarr\Seat\SeatSrp\Notifications\Channels;

use Illuminate\Notifications\Notification;

class DiscordChannel
{
    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['webhook'];
    }

    public function send($notifiable, Notification $notification): void
    {
        $message = $notification->toDiscord($notifiable);
    }
}
