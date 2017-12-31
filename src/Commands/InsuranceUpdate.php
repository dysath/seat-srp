<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 29/12/2017
 * Time: 19:51
 */

namespace Denngarr\Seat\SeatSrp\Commands;


use Denngarr\Seat\SeatSrp\Jobs\UpdateEsiInsurance;
use Illuminate\Console\Command;
use Seat\Eveapi\Helpers\JobPayloadContainer;
use Seat\Eveapi\Traits\JobManager;
use Seat\Services\Helpers\AnalyticsContainer;
use Seat\Services\Jobs\Analytics;

class InsuranceUpdate extends Command {

    use JobManager;

    protected $signature = 'esi:insurances:update';

    protected $description = 'Queue a job which will refresh insurances data';

    public function __construct() {
        parent::__construct();
    }

    public function handle(JobPayloadContainer $container)
    {
        $container->api      = 'ESI';
        $container->scope    = 'Insurances';
        $container->owner_id = 0;

        $job_id = $this->addUniqueJob(UpdateEsiInsurance::class, $container);

        $this->info('Job ' . $job_id . ' dispatched!');

        dispatch((new Analytics((new AnalyticsContainer())
            ->set('type', 'event')
            ->set('ec', 'queues')
            ->set('ea', 'queue_tokens')
            ->set('el', 'console')
            ->set('ev', 1)))
        ->onQueue('low'));
    }

}
