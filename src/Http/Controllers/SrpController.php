<?PHP

namespace Denngarr\Seat\SeatSrp\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use GuzzleHttp\Client;
use Seat\Web\Http\Controllers\Controller;
use Seat\Eveapi\Models\Character\CharacterSheet;
use Denngarr\Seat\SeatSrp\Models\KillMail;
use Denngarr\Seat\SeatSrp\Validation\AddKillMail;


class SrpController extends Controller {

    private $priceList = [];

    public function __construct() {
    }

    public function srpGetRequests() {
        $kills = KillMail::where('user_id', auth()->user()->id)->orderby('created_at', 'desc')->take(20)->get();

        return view('srp::request', compact('kills'));
    }

    public function srpGetKillMail(Request $request) {
        $totalKill = [];

        $response = (new Client())
                ->request('GET', $request->km);

	$killMail = json_decode($response->getBody());
	$totalKill = array_merge($totalKill, $this->srpPopulateSlots($killMail));
        preg_match('/([a-z0-9]{35,42})/', $request->km, $tokens);
        $totalKill['killToken'] = $tokens[0];
	return json_encode($totalKill);
    }

    public function srpPopulateSlots($killMail) {
        $slots['cargo'] = [];
        $slots['dronebay'] = [];
        foreach ($killMail->victim->items as $item) {
            $itemSearch = DB::table('invTypes')->where('typeID', $item->item_type_id)->get();
            array_push($this->priceList, $itemSearch[0]->typeName);
            $slotName = DB::table('invFlags')->where('flagID', $item->flag)->get();
            if ($slotName[0]->flagName === "Cargo") {
		$slots['cargo'][$itemSearch[0]->typeID]['name'] = $itemSearch[0]->typeName;
                if (!isset($slots['cargo'][$itemSearch[0]->typeID]['qty'])) 
                    $slots['cargo'][$itemSearch[0]->typeID]['qty'] = 0;
                if (isset($item->quantity_destroyed))
                    $slots['cargo'][$itemSearch[0]->typeID]['qty'] = $item->quantity_destroyed;
                if (isset($item->quantity_dropped))
                    $slots['cargo'][$itemSearch[0]->typeID]['qty'] += $item->quantity_dropped;
            }
            elseif ($slotName[0]->flagName === "DroneBay") {
                $slots['dronebay'][$itemSearch[0]->typeID]['name'] = $itemSearch[0]->typeName;
                if (!isset($slots['dronebay'][$itemSearch[0]->typeID]['qty'])) 
                    $slots['dronebay'][$itemSearch[0]->typeID]['qty'] = 0;
                if (isset($item->quantity_destroyed))
                    $slots['dronebay'][$itemSearch[0]->typeID]['qty'] = $item->quantity_destroyed;
                if (isset($item->quantity_dropped))
                    $slots['dronebay'][$itemSearch[0]->typeID]['qty'] += $item->quantity_dropped;
            }
            else {
                if (!(preg_match('/(Charge|Script|[SML])$/', $itemSearch[0]->typeName))) {
                    $slots[$slotName[0]->flagName]['id'] = $itemSearch[0]->typeID;
                    $slots[$slotName[0]->flagName]['name'] = $itemSearch[0]->typeName;
                    if (!isset($slots[$slotName[0]->flagName]['qty'])) 
                        $slots[$slotName[0]->flagName]['qty'] = 0;
                    if (isset($item->quantity_destroyed))
                        $slots[$slotName[0]->flagName]['qty'] = $item->quantity_destroyed;
                    if (isset($item->quantity_dropped))
                        $slots[$slotName[0]->flagName]['qty'] += $item->quantity_dropped;
                }
            }
        }

        $itemSearch = DB::table('invTypes')->where('typeID', $killMail->victim->ship_type_id)->get();
        $slots['shipType'] = $itemSearch[0]->typeName;
        array_push($this->priceList, $itemSearch[0]->typeName);
        $prices = $this->srpGetPrice();

        $pilot = CharacterSheet::where('characterID', $killMail->victim->character_id)->first();
        if (isset($pilot->name))
            $slots['characterName'] = $pilot->name;
        else
            $slots['characterName'] = $killMail->victim->character_id;
        $slots['killId'] = $killMail->killmail_id;
        $slots['price'] = $prices->appraisal->totals->sell;

        return $slots;
    }

    private function srpGetPrice() {

        $partsList = implode("\n", $this->priceList);
        
        $response = (new Client())
            ->request('POST', 'http://evepraisal.com/appraisal.json?market=jita', [
                'multipart' => [
                    [
                        'name' => 'uploadappraisal',
                        'contents' => $partsList,
                        'filename' => 'notme',
                        'headers' => [
                            'Content-Type' => 'text/plain'
                        ]
                    ],
                ]
            ]);
        return json_decode($response->getBody()->getContents());
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
            'ship_type'      => $request->input('srpShipType')
        ]);

        return redirect()->back()
            ->with('success', trans('srp::request'));
    }
}
