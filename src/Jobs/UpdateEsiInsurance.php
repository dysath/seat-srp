<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 29/12/2017
 * Time: 19:57.
 */

namespace Denngarr\Seat\SeatSrp\Jobs;

use Denngarr\Seat\SeatSrp\Models\Eve\Insurance;
use Seat\Eveapi\Jobs\EsiBase;

class UpdateEsiInsurance extends EsiBase
{

    /**
     * @var string
     */
    protected $method = 'get';

    /**
     * @var string
     */
    protected $endpoint = '/insurance/prices/';

    /**
     * @var int
     */
    protected $version = 'v1';

    /**
     * @var array
     */
    protected $tags = ['insurance'];

    public function handle(): void {

        $insurance = $this->retrieve();

        foreach ($insurance as $entry) {

            foreach ($entry->levels as $level) {

                Insurance::updateOrCreate([
                    'type_id' => $entry->type_id,
                    'name'    => $level->name,
                ], [
                    'cost'    => $level->cost,
                    'payout'  => $level->payout,
                ]);

            }

        }

    }
}
