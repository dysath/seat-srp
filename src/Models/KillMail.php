<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 20:42
 */

namespace Denngarr\Seat\SeatSrp\Models;


use Denngarr\Seat\SeatSrp\Models\Sde\InvType;
use Illuminate\Database\Eloquent\Model;
use Seat\Services\Models\Note;
use Seat\Services\Traits\NotableTrait;

class KillMail extends Model {

	use NotableTrait;

    public $timestamps = true;

    protected $primaryKey = 'kill_id';

    protected $table = 'seat_srp_srp';

    protected $fillable = [
            'user_id', 'kill_id', 'character_name', 'kill_token', 'approved', 'cost', 'type_id', 'ship_type', 'approver'
    ];

    public function type()
    {
        return $this->hasOne(InvType::class, 'typeID', 'type_id');
    }

    public function ping()
    {
    	return Note::where('object_type', __CLASS__)
		    ->where('object_id', $this->kill_id)
		    ->where('title', 'ping')
		    ->first();
    }

    public function reason()
    {
    	return Note::where('object_type', __CLASS__)
		    ->where('object_id', $this->kill_id)
		    ->where('title', 'reason')
		    ->first();
    }
}
