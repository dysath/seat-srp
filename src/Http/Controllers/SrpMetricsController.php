<?PHP

namespace Denngarr\Seat\SeatSrp\Http\Controllers;

use DB;
use Denngarr\Seat\SeatSrp\Models\KillMail;
use Seat\Web\Http\Controllers\Controller;


class SrpMetricsController extends Controller {

    /**
     * Renders SRP Metric view by consuming data from SRP API for the charts and User list from the KillMail model.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex()
    {
        $users = KillMail::where('approved', true)
            ->join('users as u', 'user_id', 'u.id')
            ->join('user_settings as us', function($join){
                $join->on('u.group_id', '=', 'us.group_id')
                    ->where('us.name', '=', 'main_character_id');
            })
            ->join('users as u2', 'us.value', '=', 'u2.id')
            ->orderBy('u2.name')
            ->pluck('u2.name', 'u2.group_id');

        return view('srp::metrics', compact('users'));
    }
}