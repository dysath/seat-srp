<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 20:42.
 */

namespace Denngarr\Seat\SeatSrp\Models;

use Denngarr\Seat\SeatSrp\Models\Sde\InvType;
use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Sde\InvGroup;

class AdvRule extends Model
{

    public $timestamps = true;

    protected $primaryKey = 'id';

    protected $table = 'denngarr_seat_srp_advrule';

    protected $fillable = [
        'rule_type', 'type_id', 'group_id', 'price_source', 'base_value', 'hull_percent', 'fit_percent', 'cargo_percent', 'deduct_insurance', 'srp_price_cap',
    ];

    public function type()
    {
        return $this->hasOne(InvType::class, 'typeID', 'type_id');
    }

    public function group()
    {
        return $this->hasOne(InvGroup::class, 'groupID', 'group_id');
    }
}
