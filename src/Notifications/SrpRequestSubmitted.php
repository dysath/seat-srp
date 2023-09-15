<?php

namespace Denngarr\Seat\SeatSrp\Notifications;

use Denngarr\Seat\SeatSrp\Notifications\Channels\DiscordChannel;
use Denngarr\Seat\SeatSrp\Notifications\Webhooks\Discord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Seat\Web\Models\User;

class SrpRequestSubmitted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {
        $uid = $notifiable->user_id;
        $user = User::where('id', $uid)->first();

        return (new Discord)->post('```New SRP Request Received:```' .
            "\t**Requested On:** $notifiable->created_at" .
            "\n\t**Requested By:** " . $user->name .
            "\n\t**Kill Mail for:** $notifiable->character_name" .
            "\n\t**Ship Type:** $notifiable->ship_type" .
            "\n\t**Cost:** " . number_format($notifiable->cost)
        );
    }
}
