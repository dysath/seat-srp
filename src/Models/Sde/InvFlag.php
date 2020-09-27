<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 29/12/2017
 * Time: 15:13
 */

namespace Denngarr\Seat\SeatSrp\Models\Sde;


use Illuminate\Database\Eloquent\Model;

class InvFlag extends Model {

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = ['flagID', 'flagName', 'flagText', 'orderID'];

    protected $table = 'invFlags';

    protected $primaryKey = 'flagID';

}
