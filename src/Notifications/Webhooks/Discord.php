<?php

namespace Denngarr\Seat\SeatSrp\Notifications\Webhooks;

use GuzzleHttp\Client as Requests;

class Discord
{
    public function post(string $content): array
    {
        $url = setting('denngarr_seat_srp_webhook_url', true);
        if(! $url){
            return [500, 'SRP DISCORD WEBHOOK URL is not defined in SRP Settings'];
        }
        $srp_role_mention = setting('denngarr_seat_srp_mention_role', true);
        $headers = [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'content' => ($srp_role_mention ? $srp_role_mention . $content : $content),
            ],
        ];
        $request = new Requests();
        $response = $request->post($url, $headers);

        return [$response->getStatusCode(), $response];
    }
}
