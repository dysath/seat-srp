<?php

namespace Denngarr\Seat\SeatSrp\Notifications;

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
        return (new Discord)->post("```New SRP Request Received:```" .
            "\t**Requested On:** $notifiable->created_at" .
            "\n\t**Requested By:** " . $notifiable::join('users as u', 'user_id', 'u.id')
                ->join('user_settings as us', function($join){
                    $join->on('u.group_id', '=', 'us.group_id')
                        ->where('us.name', 'main_character_id');
                })
                ->join('users as u2', 'us.value', '=', 'u2.id')
                ->select('u2.name')
                ->first()->name .
            "\n\t**Kill Mail for:** $notifiable->character_name" .
            "\n\t**Ship Type:** $notifiable->ship_type" .
            "\n\t**Cost:** " . number_format($notifiable->cost)
        );
    }


}
