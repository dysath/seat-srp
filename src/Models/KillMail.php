<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 01/12/2017
 * Time: 20:42.
 */

namespace Denngarr\Seat\SeatSrp\Models;

use Denngarr\Seat\SeatSrp\Models\Sde\InvType;
use Denngarr\Seat\SeatSrp\Notifications\SrpRequestSubmitted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Seat\Eveapi\Models\Killmails\Killmail as EveKillmail;
use Seat\Services\Models\Note;
use Seat\Services\Traits\NotableTrait;
use Seat\Web\Models\User;

class KillMail extends Model
{

    use NotableTrait;
    use Notifiable;

    public $timestamps = true;

    protected $primaryKey = 'kill_id';

    protected $table = 'seat_srp_srp';

    protected $fillable = [
        'user_id', 'kill_id', 'character_name', 'kill_token', 'approved', 'cost', 'type_id', 'ship_type', 'approver',
    ];

    protected static function boot()
    {
        parent::boot();

        self::created(function ($model): void {
            if(setting('denngarr_seat_srp_webhook_url', true) != ''){
                $model->notify(new SrpRequestSubmitted());
            }
        });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function type()
    {
        return $this->hasOne(InvType::class, 'typeID', 'type_id');
    }

    public function ping()
    {
        return Note::where('object_type', self::class)
            ->where('object_id', $this->kill_id)
            ->where('title', 'ping')
            ->first();
    }

    public function reason()
    {
        return Note::where('object_type', self::class)
            ->where('object_id', $this->kill_id)
            ->where('title', 'reason')
            ->first();
    }

    public function details()
    {
        return $this->hasOne(EveKillmail::class, 'killmail_id', 'kill_id');
    }
}
