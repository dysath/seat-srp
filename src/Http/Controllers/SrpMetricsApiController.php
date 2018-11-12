<?php

namespace Denngarr\Seat\SeatSrp\Http\Controllers;

use Seat\Api\Http\Controllers\Api\v2\ApiController;
use Denngarr\Seat\SeatSrp\Models\KillMail;
use Seat\Web\Models\User;
use Seat\Web\Models\Group;


/**
 * Class SrpMetricsApiController
 * @package Denngarr\Seat\SeatSrp\Http\Controllers
 */
class SrpMetricsApiController extends ApiController {

    private $srp_statuses = [
        'unprocessed' => [0],
        'rejected' => [-1],
        'approved' => [1],
        'paid' => [2],
        'all' => [-1,0,1,2]
    ];

    /**
     * @SWG\Get(
     *      path="/srp/metrics/summary/monthly/{status}/{limit}",
     *      tags={"SRP Monthly Summary"},
     *      summary="Get a summary of approved SRP Requests by month",
     *      description="Returns JSON object of counts of requests and sum of payouts by month.",
     *      security={
     *          {"SeAT Role": "bouncer:srp.settle"},
     *          {"ApiKeyAuth": {}}
     *      },
     *      @SWG\Parameter(
     *          name="status",
     *          description="SRP Processing Status",
     *          required=true,
     *          type="string",
     *          in="path"
     *      ),
     *     @SWG\Parameter(
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
     *                  type="array",
     *                  property="dt",
     *                  description="Date in YYYY-MM-DD format, always reverting to the first day of the month",
     *                  @SWG\Items(
     *                      type="string",
     *                      format="date"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  type="array",
     *                  property="payouts",
     *                  description="ISK Payouts for SRP Requests",
     *                  @SWG\Items(
     *                      type="number",
     *                      format="float"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  type="array",
     *                  property="requests",
     *                  description="Numbner of SRP Requests",
     *                  @SWG\Items(
     *                      type="integer"
     *                  )
     *              )
     *          )
     *      ),
     *      @SWG\Response(response=400, description="Bad request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *     )
     *
     * @param int $limit
     */
    public function getSummaryMonthly($status=null,$limit=null)
    {
        // return 404 if status is not recognized
        if(!array_key_exists($status, $this->srp_statuses)){
            return response([],404);
        }

        $raw = KillMail::whereIn('approved', $this->srp_statuses[$status])
            ->selectRaw('date_format(created_at, "%Y-%m-01") dt, sum(cost) payouts, count(kill_id) requests')
            ->groupBy('dt')
            ->orderByDesc('dt');

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
     *      path="/srp/metrics/summary/user/{status}/{group_id}/{limit}",
     *      tags={"SRP User Summary"},
     *      summary="Get a summary of approved SRP Requests for a specific User",
     *      description="Returns JSON object of counts of requests and sum of payouts by month and by Ship.",
     *      security={
     *          {"SeAT Role": "bouncer:srp.settle"},
     *          {"ApiKeyAuth": {}}
     *      },
     *     @SWG\Parameter(
     *          name="status",
     *          description="SRP Processing Status",
     *          required=true,
     *          type="string",
     *          in="path"
     *      ),
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
     *                  type="object",
     *                  property="summary",
     *                  description="Summary of User SRP Requests by Payouts/Requests",
     *                  @SWG\Property(
     *                      type="array",
     *                      property="dt",
     *                      description="Date in YYYY-MM-DD format, always reverting to the first day of the month",
     *                      @SWG\Items(
     *                          type="string",
     *                          format="date"
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      type="array",
     *                      property="payouts",
     *                      description="ISK Payouts for SRP Requests",
     *                      @SWG\Items(
     *                          type="number",
     *                          format="float"
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      type="array",
     *                      property="requests",
     *                      description="Numbner of SRP Requests",
     *                      @SWG\Items(
     *                          type="integer"
     *                      )
     *                  ),
     *              ),
     *              @SWG\Property(
     *                  type="object",
     *                  property="ships",
     *                  description="Summary of User SRP Requests by Ship",
     *                  @SWG\Property(
     *                      type="array",
     *                      property="ship",
     *                      description="List of Top Ships by SRP Payouts",
     *                      @SWG\Items(
     *                          type="string"
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      type="array",
     *                      property="payouts",
     *                      description="ISK Payouts for SRP Requests",
     *                      @SWG\Items(
     *                          type="number",
     *                          format="float"
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      type="array",
     *                      property="requests",
     *                      description="Numbner of SRP Requests",
     *                      @SWG\Items(
     *                          type="integer"
     *                      )
     *                  ),
     *              ),
     *          )
     *      ),
     *      @SWG\Response(response=400, description="Bad request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=404, description="Group Id not found"),
     *     )
     *
     * @param int $limit
     */
    public function getSummaryUser($status=null,$group_id, $limit=null)
    {
        // return 404 if status is not recognized
        if(!array_key_exists($status, $this->srp_statuses)){
            return response([],404);
        }

        $group = Group::where('id', $group_id)->first();
        if(!$group){
            return response([],404);
        }

        $user_ids = $group->users->pluck('id');
        $summary = KillMail::whereIn('approved', $this->srp_statuses[$status])
            ->whereIn('user_id', $user_ids)
            ->selectRaw('date_format(created_at, "%Y-%m-01") as dt, sum(cost) payouts, count(kill_id) requests')
            ->groupBy('dt')
            ->orderBy('dt', 'desc');
        $ships = KillMail::where('approved', true)
            ->whereIn('user_id', $user_ids)
            ->selectRaw('ship_type, sum(cost) payouts, count(kill_id) requests')
            ->groupBy('ship_type')
            ->orderByDesc('payouts');

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
                'ship' => $ships->pluck('ship_type'),
                'payouts' => $ships->pluck('payouts'),
                'requests' => $ships->pluck('requests')
            ]
        ];
    }

    /**
     * @SWG\Get(
     *      path="/srp/metrics/top/ship/{status}/{limit}",
     *      tags={"SRP Top Ship"},
     *      summary="Get the top SRP utilizers order by Cost",
     *      description="Returns JSON object of counts of requests and sum of payouts by Ship",
     *      security={
     *          {"SeAT Role": "bouncer:srp.settle"},
     *          {"ApiKeyAuth": {}}
     *      },
     *      @SWG\Parameter(
     *          name="status",
     *          description="SRP Processing Status",
     *          required=true,
     *          type="string",
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
     *                  type="array",
     *                  property="ships",
     *                  description="List of Top Ships by SRP Payouts",
     *                  @SWG\Items(
     *                      type="string"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  type="array",
     *                  property="payouts",
     *                  description="ISK Payouts for SRP Requests",
     *                  @SWG\Items(
     *                      type="number",
     *                      format="float"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  type="array",
     *                  property="requests",
     *                  description="Numbner of SRP Requests",
     *                  @SWG\Items(
     *                      type="integer"
     *                  )
     *              )
     *          )
     *      ),
     *      @SWG\Response(response=400, description="Bad request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *     )
     *
     * @param int $limit
     */
    public function getTopShip($status=null,$limit=null)
    {
        // return 404 if status is not recognized
        if(!array_key_exists($status, $this->srp_statuses)){
            return response([],404);
        }

        $raw = KillMail::whereIn('approved', $this->srp_statuses[$status])
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
     *      path="/srp/metrics/top/user/{status}/{limit}",
     *      tags={"SRP Top User"},
     *      summary="Get the top SRP utilizers order by Cost",
     *      description="Returns JSON object of counts of requests and sum of payouts by User",
     *      security={
     *          {"SeAT Role": "bouncer:srp.settle"},
     *          {"ApiKeyAuth": {}}
     *      },
     *      @SWG\Parameter(
     *          name="status",
     *          description="SRP Processing Status",
     *          required=true,
     *          type="string",
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
     *                  type="array",
     *                  property="dt",
     *                  description="Date in YYYY-MM-DD format, always reverting to the first day of the month",
     *                  @SWG\Items(
     *                      type="string",
     *                      format="date"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  type="array",
     *                  property="payouts",
     *                  description="ISK Payouts for SRP Requests",
     *                  @SWG\Items(
     *                      type="number",
     *                      format="float"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  type="array",
     *                  property="requests",
     *                  description="Numbner of SRP Requests",
     *                  @SWG\Items(
     *                      type="integer"
     *                  )
     *              )
     *          )
     *      ),
     *      @SWG\Response(response=400, description="Bad request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *     )
     *
     * @param int $limit
     */
    public function getTopUser($status=null,$limit=null)
    {
        // return 404 if status is not recognized
        if(!array_key_exists($status, $this->srp_statuses)){
            return response([],404);
        }

        $raw = KillMail::whereIn('approved', $this->srp_statuses[$status])
            ->join('users as u', 'user_id', 'u.id')
            ->join('user_settings as us', function($join){
                $join->on('u.group_id', '=', 'us.group_id')
                    ->where('us.name', 'main_character_id');
            })
            ->join('users as u2', 'us.value', '=', 'u2.id')
            ->selectRaw('u2.name as main, sum(cost) as payouts, count(kill_id) as requests')
            ->groupBy('main')
            ->orderByDesc('payouts');

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