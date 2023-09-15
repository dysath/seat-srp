<?php

namespace Denngarr\Seat\SeatSrp\Http\Controllers;

use Denngarr\Seat\SeatSrp\Models\KillMail;
use Seat\Web\Http\Controllers\Controller;

class SrpMetricsController extends Controller
{

    private array $srp_statuses = [
        'unprocessed' => [0],
        'rejected' => [-1],
        'approved' => [1],
        'paid' => [2],
        'all' => [-1, 0, 1, 2],
    ];

    /**
     * Renders SRP Metric view by consuming data from SRP API for the charts and User list from the KillMail model.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getIndex(string $srp_status = 'all')
    {
        if(! $srp_status || ! array_key_exists($srp_status, $this->srp_statuses)){
            return back()->withErrors(
                'SRP Status of `' . $srp_status . '` is invalid.'
            );
        }

        $users = KillMail::whereIn('approved', $this->srp_statuses[$srp_status])
            ->join('users as u', 'user_id', 'u.id')
            ->orderBy('u.name')
            ->pluck('u.name', 'u.id');

        return view('srp::metrics', ['users' => $users, 'srp_status' => $srp_status]);
    }
}
