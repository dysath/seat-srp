<?php

namespace Denngarr\Seat\SeatSrp\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Denngarr\Seat\SeatSrp\Models\KillMail;
use Seat\Web\Models\User;
use Seat\Web\Models\Group;


class SrpMetricsApiController extends Controller {

    /**
     * @SWG\Get(
     *      path="/srp/metrics/api/summary/monthly/{limit}",
     *      tags={"SRP Monthly Summary"},
     *      summary="Get a summary of approved SRP Requests by month",
     *      description="Returns JSON object of counts of requests and sum of payouts by month.",
     *      security={
     *          {"SeAT Role": "bouncer:srp.settle"}
     *      },
     *      @SWG\Parameter(
     *          name="limit",
     *          description="record limit",
     *          required=false,
     *          type="integer",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="json",
     *                  property="data",
     *                  @SWG\Items(ref="#/summary/monthly")
     *              )
     *          )
     *      ),
     *      @SWG\Response(response=400, description="Bad request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *     )
     *
     * @param int $limit
     */
    public function getSummaryMonthly($limit=null)
    {
        $raw = KillMail::where('approved', true)
            ->selectRaw('date_format(created_at, "%Y-%m-01") dt, sum(cost) payouts, count(kill_id) requests')
            ->groupBy('dt')
            ->orderBy('dt', 'desc');

        if($limit){
            $raw = $raw->take($limit);
        }

        return [
            'dt' => $raw->pluck('dt'),
            'payouts' => $raw->pluck('payouts'),
            'requests' => $raw->pluck('requests')
        ];
    }

    /**
     * @SWG\Get(
     *      path="/srp/metrics/api/summary/user/{$group_id}/{limit}",
     *      tags={"SRP User Summary"},
     *      summary="Get a summary of approved SRP Requests for a specific User",
     *      description="Returns JSON object of counts of requests and sum of payouts by month and by Ship.",
     *      security={
     *          {"SeAT Role": "bouncer:srp.settle"}
     *      },
     *     @SWG\Parameter(
     *          name="group_id",
     *          description="SeAT User Group Id",
     *          required=true,
     *          type="integer",
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          description="record limit",
     *          required=false,
     *          type="integer",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="json",
     *                  property="data",
     *                  @SWG\Items(ref="#/summary/user")
     *              )
     *          )
     *      ),
     *      @SWG\Response(response=400, description="Bad request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *     )
     *
     * @param int $limit
     */
    public function getSummaryUser($group_id, $limit=null)
    {
        $group = Group::where('id', $group_id)->first();
        if(!$group){
            return response([],404);
        }

        $user_ids = $group->users->pluck('id');
        $summary = KillMail::where('approved', true)
            ->whereIn('user_id', $user_ids)
            ->selectRaw('date_format(created_at, "%Y-%m-01") as dt, sum(cost) payouts, count(kill_id) requests')
            ->groupBy('dt')
            ->orderBy('dt', 'desc');
        $ships = KillMail::where('approved', true)
            ->whereIn('user_id', $user_ids)
            ->selectRaw('ship_type, sum(cost) payouts, count(kill_id) requests')
            ->groupBy('ship_type')
            ->orderBy('payouts', 'desc');

        if($limit){
            $summary = $summary->take($limit);
            $ships = $ships->take($limit);
        }

        return [
            'summary' => [
                'dt' => $summary->pluck('dt'),
                'payouts' => $summary->pluck('payouts'),
                'requests' => $summary->pluck('requests'),
            ],
            'ships' => [
                'ship' => $ships->pluck('ship'),
                'payouts' => $ships->pluck('payouts'),
                'requests' => $ships->pluck('requests')
            ]
        ];
    }

    /**
     * @SWG\Get(
     *      path="/srp/metrics/api/top/ship/{limit}",
     *      tags={"SRP Top Ship"},
     *      summary="Get the top SRP utilizers order by Cost",
     *      description="Returns JSON object of counts of requests and sum of payouts by Ship",
     *      security={
     *          {"SeAT Role": "bouncer:srp.settle"}
     *      },
     *      @SWG\Parameter(
     *          name="limit",
     *          description="record limit",
     *          required=false,
     *          type="integer",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="json",
     *                  property="data",
     *                  @SWG\Items(ref="#/top/ship")
     *              )
     *          )
     *      ),
     *      @SWG\Response(response=400, description="Bad request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *     )
     *
     * @param int $limit
     */
    public function getTopShip($limit=null)
    {
        $raw = KillMail::where('approved', true)
            ->selectRaw('ship_type, count(kill_id) requests, sum(cost) payouts')
            ->groupBy('ship_type')
            ->orderByDesc('payouts');

        if ($limit){
            $raw = $raw->take($limit);
        }

        return [
            'ships' => $raw->pluck('ship_type'),
            'requests' => $raw->pluck('requests'),
            'payouts' => $raw->pluck('payouts')
        ];
    }

    /**
     * @SWG\Get(
     *      path="/srp/metrics/api/top/user/{limit}",
     *      tags={"SRP Top User"},
     *      summary="Get the top SRP utilizers order by Cost",
     *      description="Returns JSON object of counts of requests and sum of payouts by User",
     *      security={
     *          {"SeAT Role": "bouncer:srp.settle"}
     *      },
     *      @SWG\Parameter(
     *          name="limit",
     *          description="record limit",
     *          required=false,
     *          type="integer",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="json",
     *                  property="data",
     *                  @SWG\Items(ref="#/top/user")
     *              )
     *          )
     *      ),
     *      @SWG\Response(response=400, description="Bad request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *     )
     *
     * @param int $limit
     */
    public function getTopUser($limit=null)
    {
        $raw = KillMail::join('users as u', 'user_id', 'u.id')
            ->join('user_settings as us', function($join){
                $join->on('u.group_id', '=', 'us.group_id')
                    ->where('us.name', 'main_character_id');
            })
            ->join('users as u2', 'us.value', '=', 'u2.id')
            ->selectRaw('u2.name as main, sum(cost) as payouts, count(kill_id) as requests')
            ->groupBy('main')
            ->orderBy('payouts', 'desc');

        if($limit){
            $raw = $raw->take($limit);
        }

        return [
            'main' => $raw->pluck('main'),
            'payouts' => $raw->pluck('payouts'),
            'requests' => $raw->pluck('requests')
        ];
    }
}