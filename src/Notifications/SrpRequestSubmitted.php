<?php

namespace Denngarr\Seat\SeatSrp\Notifications;

use Seat\Web\Models\User;
use Denngarr\Seat\SeatSrp\Notifications\Channels\DiscordChannel;
use Denngarr\Seat\SeatSrp\Notifications\Webhooks\Discord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {
        $uid = $notifiable->user_id;
        $user = User::where('id', $uid)->first();

        return (new Discord)->post("```New SRP Request Received:```" .
            "\t**Requested On:** $notifiable->created_at" .
            "\n\t**Requested By:** " . $user->name .
            "\n\t**Kill Mail for:** $notifiable->character_name" .
            "\n\t**Ship Type:** $notifiable->ship_type" .
            "\n\t**Cost:** " . number_format($notifiable->cost)
        );
    }


}
