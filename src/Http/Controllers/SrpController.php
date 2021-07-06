<?php

namespace Denngarr\Seat\SeatSrp\Http\Controllers;

use Denngarr\Seat\SeatSrp\Helpers\SrpManager;
use Denngarr\Seat\SeatSrp\Models\KillMail;
use Denngarr\Seat\SeatSrp\Validation\AddKillMail;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Seat\Eveapi\Jobs\Killmails\Detail;
use Seat\Eveapi\Models\Killmails\Killmail as EveKillmail;
use Seat\Eveapi\Models\Killmails\KillmailDetail;
use Seat\Web\Http\Controllers\Controller;

class SrpController extends Controller
{

    use SrpManager;

    public function srpGetRequests()
    {
        $kills = KillMail::where('user_id', auth()->user()->id)
                         ->orderby('created_at', 'desc')
                         ->take(20)
                         ->get();

        return view('srp::request', compact('kills'));
    }

    public function srpGetKillMail(Request $request)
    {

        // The submitted url is available at $request->km;
        $url_parts = explode('/', rtrim($request->km, "/ \t\n\r\0\x0B"));

        $token = $url_parts[5];
        $hash = $url_parts[6];

        $killmail = EveKillmail::firstOrCreate([
            'killmail_id' => $token,
        ], [
            'killmail_hash' => $hash,
        ]);

        if (! KillmailDetail::find($killmail->killmail_id))
                    Detail::dispatchNow($killmail->killmail_id, $killmail->killmail_hash);

        $totalKill = [];

        // $response = (new Client())->request('GET', $request->km);

        // $killMail = json_decode($response->getBody());
        $totalKill = array_merge($totalKill, $this->srpPopulateSlots($killmail));
        preg_match('/([a-z0-9]{35,42})/', $request->km, $tokens);
        $totalKill['killToken'] = $tokens[0];

        return response()->json($totalKill);
    }

    public function srpSaveKillMail(AddKillMail $request)
    {

        KillMail::create([
            'user_id'        => auth()->user()->id,
            'character_name' => $request->input('srpCharacterName'),
            'kill_id'        => $request->input('srpKillId'),
            'kill_token'     => $request->input('srpKillToken'),
            'approved'       => 0,
            'cost'           => $request->input('srpCost'),
            'type_id'        => $request->input('srpTypeId'),
            'ship_type'      => $request->input('srpShipType'),
        ]);

        if (! is_null($request->input('srpPingContent')) && $request->input('srpPingContent') != '')
            KillMail::addNote($request->input('srpKillId'), 'ping', $request->input('srpPingContent'));

        return redirect()->back()
                         ->with('success', trans('srp::srp.submitted'));
    }

    public function getInsurances($kill_id)
    {
        $killmail = KillMail::where('kill_id', $kill_id)->first();

        if (is_null($killmail))
            return response()->json(['msg' => sprintf('Unable to retried killmail %s', $kill_id)], 404);

        $data = [];

        foreach ($killmail->type->insurances as $insurance) {

            array_push($data, [
                'name' => $insurance->name,
                'cost' => $insurance->cost,
                'payout' => $insurance->payout,
                'refunded' => $insurance->refunded(),
                'remaining' => $insurance->remaining($killmail),
            ]);

        }

        return response()->json($data);
    }

    public function getPing($kill_id)
    {
        $killmail = KillMail::find($kill_id);

        if (is_null($killmail))
            return response()->json(['msg' => sprintf('Unable to retrieve kill %s', $kill_id)], 404);

        if (! is_null($killmail->ping()))
            return response()->json($killmail->ping());

        return response()->json(['msg' => sprintf('There are no ping information related to kill %s', $kill_id)], 204);
    }

    public function getReason($kill_id)
    {
        $killmail = KillMail::find($kill_id);

        if (is_null($killmail))
            return response()->json(['msg' => sprintf('Unable to retrieve kill %s', $kill_id)], 404);

        if (! is_null($killmail->reason()))
            return response()->json($killmail->reason());

        return response()->json(['msg' => sprintf('There is no reason information related to kill %s', $kill_id)], 204);
    }

    public function getAboutView()
    {
        return view('srp::about');
    }

    public function getInstructionsView()
    {
        return view('srp::instructions');
    }
}
