<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 20:42
 */

namespace Denngarr\Seat\SeatSrp\Models;


use Denngarr\Seat\SeatSrp\Models\Sde\InvType;
use Illuminate\Database\Eloquent\Model;

class KillMail extends Model {

    public $timestamps = true;

    protected $primaryKey = 'kill_id';

    protected $table = 'seat_srp_srp';

    protected $fillable = [
            'user_id', 'kill_id', 'character_name', 'kill_token', 'approved', 'cost', 'type_id', 'ship_type',
    ];

    public function type()
    {
        return $this->hasOne(InvType::class, 'typeID', 'type_id');
    }
}
