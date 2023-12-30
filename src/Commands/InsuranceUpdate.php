<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 29/12/2017
 * Time: 19:51.
 */

namespace Denngarr\Seat\SeatSrp\Commands;

use Denngarr\Seat\SeatSrp\Jobs\UpdateEsiInsurance;
use Illuminate\Console\Command;

class InsuranceUpdate extends Command
{

    protected $signature = 'esi:insurances:update';

    protected $description = 'Queue a job which will refresh insurances data';

    public function handle(): void
    {
        UpdateEsiInsurance::dispatch();
    }
}
