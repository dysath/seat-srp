<?php

namespace Denngarr\Seat\SeatSrp\Helpers;

use Denngarr\Seat\SeatSrp\Models\AdvRule;
use Denngarr\Seat\SeatSrp\Models\Eve\Insurance;
use Denngarr\Seat\SeatSrp\Models\Sde\InvFlag;
use Exception;
use GuzzleHttp\Client;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Killmails\Killmail;
use stdClass;

trait SrpManager
{

    public static $FIT_FLAGS = [11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 87,
        92, 93, 94, 95, 96, 97, 98, 99, 125, 126, 127, 128, 129, 130, 131, 132, 158, 159, 160, 161, 162, 163, ];
    public static $CARGO_FLAGS = [5, 90, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 148, 149, 151, 155, 176, 177, 179];

    private function srpPopulateSlots(Killmail $killMail): array
    {
        $priceList = [];
        $slots = [
            'killId' => 0,
            'price' => 0.0,
            'shipType' => null,
            'characterName' => null,
            'cargo' => [],
            'dronebay' => [],
        ];

        foreach ($killMail->victim->items as $item) {
            $searchedItem = $item;
            $slotName = InvFlag::find($item->pivot->flag);
            if (! is_object($searchedItem)) {
            } else {
                array_push($priceList, $searchedItem->typeName);
                // dd($item->pivot);
                switch ($slotName->flagName) {
                    case 'Cargo':
                        $slots['cargo'][$searchedItem->typeID]['name'] = $searchedItem->typeName;
                        if (! isset($slots['cargo'][$searchedItem->typeID]['qty']))
                            $slots['cargo'][$searchedItem->typeID]['qty'] = 0;
                        if (! is_null($item->pivot->quantity_destroyed))
                            $slots['cargo'][$searchedItem->typeID]['qty'] += $item->pivot->quantity_destroyed;
                        if (! is_null($item->pivot->quantity_dropped))
                            $slots['cargo'][$searchedItem->typeID]['qty'] += $item->pivot->quantity_dropped;
                        break;
                    case 'DroneBay':
                        $slots['dronebay'][$searchedItem->typeID]['name'] = $searchedItem->typeName;
                        if (! isset($slots['dronebay'][$searchedItem->typeID]['qty']))
                            $slots['dronebay'][$searchedItem->typeID]['qty'] = 0;
                        if (! is_null($item->pivot->quantity_destroyed))
                            $slots['dronebay'][$searchedItem->typeID]['qty'] += $item->pivot->quantity_destroyed;
                        if (! is_null($item->pivot->quantity_dropped))
                            $slots['dronebay'][$searchedItem->typeID]['qty'] += $item->pivot->quantity_dropped;
                        break;
                    default:
                        if (! (preg_match('/(Charge|Script|[SML])$/', $searchedItem->typeName))) {
                            $slots[$slotName->flagName]['id'] = $searchedItem->typeID;
                            $slots[$slotName->flagName]['name'] = $searchedItem->typeName;
                            if (! isset($slots[$slotName->flagName]['qty']))
                                $slots[$slotName->flagName]['qty'] = 0;
                            if (! is_null($item->pivot->quantity_destroyed))
                                $slots[$slotName->flagName]['qty'] += $item->pivot->quantity_destroyed;
                            if (! is_null($item->pivot->quantity_dropped))
                                $slots[$slotName->flagName]['qty'] += $item->pivot->quantity_dropped;
                        }
                        break;
                }
            }
        }

        $searchedItem = $killMail->victim->ship;
        $slots['typeId'] = $killMail->victim->ship->typeID;
        $slots['shipType'] = $searchedItem->typeName;
        array_push($priceList, $searchedItem->typeName);
        $prices = $this->srpGetPrice($killMail, $priceList);

        $pilot = CharacterInfo::find($killMail->victim->character_id);

        $slots['characterName'] = $killMail->victim->character_id;
        if (! is_null($pilot))
            $slots['characterName'] = $pilot->name;

        $slots['killId'] = $killMail->killmail_id;
        $slots['price'] = $prices;

        return $slots;
    }

    private function srpGetPrice(Killmail $killmail, array $priceList): array
    {
        // Switching logic between advanced and simple rules
        // Try advanced first, becasue if the setting hasnt been set it will be empty.
        if (setting('denngarr_seat_srp_advanced_srp', true) == '1') {
            return $this->srpGetAdvancedPrice($killmail, $priceList);
        }

        return $this->srpGetSimplePrice($priceList);
    }

    private function srpGetAdvancedPrice(Killmail $killmail, array $priceList): array
    {
        // Start by checking if there is a type rule that matches the ship
        $rule = AdvRule::where('type_id', $killmail->victim->ship_type_id)->first();
        if (is_null($rule)) {
            $rule = AdvRule::where('group_id', $killmail->victim->ship->groupID)->first();
            if (is_null($rule)) {
                return  $this->srpGetDefaultRulePrice($killmail, $priceList);
            }
        }

        return $this->srpGetRulePrice($rule, $killmail, $priceList);
    }

    private function srpGetRulePrice(AdvRule $rule, Killmail $killmail, array $priceList): array
    {

        $source = $rule->price_source;
        $base_value = $rule->base_value;
        $hull_percent = $rule->hull_percent / 100;
        $fit_percent = $rule->fit_percent / 100;
        $cargo_percent = $rule->cargo_percent / 100;
        $deduct_insurance = $rule->deduct_insurance;
        $price_cap = $rule->srp_price_cap;

        $deduct_insurance = $deduct_insurance == '1' ? true : false;

        $prices = [];

        // Moot point for now.... But will expand later
        switch ($source) {
            case 'evepraisal':
                $prices = $this->srpGetAppraisal($priceList)->appraisal->items;
                break;
            default:
                // TODO handle this nicer
                throw new Exception('BAD PRICE SOURCE');
                break;
        }

        $prices = collect($prices); // Handy to query the collection

        // Hull Price
        $hp = $prices->where('typeID', $killmail->victim->ship_type_id)->first();
        $hp = is_null($hp) ? 0 : ($hp->prices->sell->percentile * $hull_percent);

        // Fit Price (fit flags are any between 11 and 34, plus 87 for drone bay)
        // Cargo Price (Cargo is flag = 5, fleet hangar is 155)
        $fp = 0;
        $cp = 0;
        foreach ($killmail->victim->items as $item) {
            // Fitted Item
            if ((($item->pivot->flag >= 11) && ($item->pivot->flag <= 34)) || ($item->pivot->flag == 87)) {
                $p = $prices->where('typeID', $item->typeID)->first();
                $fp += is_null($p) ? 0 : $p->prices->sell->percentile;
                continue;
            }

            // Cargo Item
            if (($item->pivot->flag == 5) || ($item->pivot->flag == 155)) {
                $p = $prices->where('typeID', $item->typeID)->first();
                $cp += is_null($p) ? 0 : $p->prices->sell->percentile;
                continue;
            }
        }
        $fp = $fp * $fit_percent;
        $cp = $cp * $cargo_percent;

        $total = $hp + $fp + $cp + $base_value;

        if($deduct_insurance) {
            $ins = Insurance::where('type_id', $killmail->victim->ship_type_id)->where('Name', 'Platinum')->first();
            if(! is_null($ins)){
                $total = $total + $ins->cost - $ins->payout;
            }
        }

        $total = round($total, 2);

        //apply price cap
        if($price_cap!==null && $total > $price_cap){
            $total = $price_cap;
        }

        return [
            'price' => $total,
            'rule' => $rule->rule_type,
            'source' => $source,
            'base_value' => $base_value,
            'hull_percent' => $hull_percent,
            'fit_percent' => $fit_percent,
            'cargo_percent' => $cargo_percent,
            'deduct_insurance' => $deduct_insurance,
        ];
    }

    private function srpGetDefaultRulePrice(Killmail $killmail, array $priceList): array
    {

        $source = 'evepraisal';
        $base_value = setting('denngarr_seat_srp_advrule_def_base', true) ? setting('denngarr_seat_srp_advrule_def_base', true) : 0;
        $hull_percent = setting('denngarr_seat_srp_advrule_def_hull', true) ? setting('denngarr_seat_srp_advrule_def_hull', true) / 100 : 0;
        $fit_percent = setting('denngarr_seat_srp_advrule_def_fit', true) ? setting('denngarr_seat_srp_advrule_def_fit', true) / 100 : 0;
        $cargo_percent = setting('denngarr_seat_srp_advrule_def_cargo', true) ? setting('denngarr_seat_srp_advrule_def_cargo', true) / 100 : 0;
        $deduct_insurance = setting('denngarr_seat_srp_advrule_def_ins', true) ? setting('denngarr_seat_srp_advrule_def_ins', true) : 0;
        $price_cap = setting('denngarr_seat_srp_advrule_def_price_cap', true) ? intval(setting('denngarr_seat_srp_advrule_def_price_cap', true)) : null;

        $deduct_insurance = $deduct_insurance == '1' ? true : false;

        $prices = [];

        // Moot point for now.... But will expand later
        switch ($source) {
            case 'evepraisal':
                $prices = $this->srpGetAppraisal($priceList)->appraisal->items;
        }

        $prices = collect($prices); // Handy to query the collection

        // Hull Price
        $hp = $prices->where('typeID', $killmail->victim->ship_type_id)->first();
        $hp = is_null($hp) ? 0 : ($hp->prices->sell->percentile * $hull_percent);

        // Fit Price (fit flags are any between 11 and 34, plus 87 for drone bay)
        // Cargo Price (Cargo is flag = 5, fleet hangar is 155)
        $fp = 0;
        $cp = 0;
        foreach ($killmail->victim->items as $item) {
            // Fitted Item
            if (in_array($item->pivot->flag, SrpManager::$FIT_FLAGS)) {
                $p = $prices->where('typeID', $item->typeID)->first();
                $fp += is_null($p) ? 0 : $p->prices->sell->percentile;
                continue;
            }

            // Cargo Item
            if (in_array($item->pivot->flag, SrpManager::$CARGO_FLAGS)) {
                $p = $prices->where('typeID', $item->typeID)->first();
                $cp += is_null($p) ? 0 : $p->prices->sell->percentile;
                continue;
            }
        }
        $fp = $fp * $fit_percent;
        $cp = $cp * $cargo_percent;

        $total = round($hp + $fp + $cp + $base_value, 2);

        if($deduct_insurance) {
            $ins = Insurance::where('type_id', $killmail->victim->ship_type_id)->where('Name', 'Platinum')->first();
            if(! is_null($ins)){
                $total = $total + $ins->cost - $ins->payout;
            }
        }

        //apply price cap
        if($price_cap!==null && $total > $price_cap){
            $total = $price_cap;
        }

        return [
            'price' => $total,
            'rule' => 'default',
            'source' => $source,
            'base_value' => $base_value,
            'hull_percent' => $hull_percent,
            'fit_percent' => $fit_percent,
            'cargo_percent' => $cargo_percent,
            'deduct_insurance' => $deduct_insurance,
        ];
    }

    private function srpGetSimplePrice(array $priceList): array
    {
        return ['price' => $this->srpGetAppraisal($priceList)->appraisal->totals->sell, 'rule' => 'simple'];
    }

    /*
     * TODO - Move to nicer evepraisal method.
     */
    private function srpGetAppraisal(array $priceList): stdClass
    {

        $partsList = implode("\n", $priceList);

        $response = (new Client())
            ->request('POST', 'https://aoeve.net/appraisal.json?market=jita', [
                'multipart' => [
                    [
                        'name' => 'uploadappraisal',
                        'contents' => $partsList,
                        'filename' => 'notme',
                        'headers' => [
                            'Content-Type' => 'text/plain',
                        ],
                    ],
                ],
            ]);

        return json_decode($response->getBody()->getContents());
    }
}
