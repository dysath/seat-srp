<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 29/12/2017
 * Time: 19:51.
 */

namespace Denngarr\Seat\SeatSrp\Commands;

use Denngarr\Seat\SeatSrp\Models\Sde\InvFlag;
use Illuminate\Console\Command;

class FlagShim extends Command
{

    protected $signature = 'srp:glue:flag';

    protected $description = 'Update the database with some flags not present in the SDE';

    public function handle()
    {
        // Frigate Escape Bay
        if(! InvFlag::where('flagID', 179)->exists()){
            InvFlag::create([
                'flagID' => 179,
                'flagName' => 'FrigateBay',
                'flagText' => 'Frigate Escape Bay',
                'orderID' => 0,
            ])->save();
        }

        // Core room
        if(! InvFlag::where('flagID', 180)->exists()){
            InvFlag::create([
                'flagID' => 180,
                'flagName' => 'CoreRoom',
                'flagText' => 'Core Room',
                'orderID' => 0,
            ])->save();
        }
    }
}
