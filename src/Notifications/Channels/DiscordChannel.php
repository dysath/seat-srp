<?php

namespace Denngarr\Seat\SeatSrp\Notifications\Channels;

use Illuminate\Notifications\Notification;

class DiscordChannel
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['webhook'];
    }

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toDiscord($notifiable);
    }
}
