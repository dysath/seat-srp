<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 29/12/2017
 * Time: 19:57
 */

namespace Denngarr\Seat\SeatSrp\Jobs;


use Denngarr\Seat\SeatSrp\Models\Eve\Insurance;
use Exception;
use Monolog\Logger;
use Seat\Eseye\Configuration;
use Seat\Eseye\Eseye;
use Seat\Eseye\Exceptions\EsiScopeAccessDeniedException;
use Seat\Eseye\Exceptions\RequestFailedException;
use Seat\Eveapi\Jobs\Base;

class UpdateEsiInsurance extends Base {

    public function handle() {

        if (!$this->trackOrDismiss())
            return;

        $this->updateJobStatus(['status' => 'Working']);

        $this->writeInfoJobLog('Started ESI Insurance Update.');

        $job_start = microtime(true);

        try {

            // Setup Eseye
            $configuration = Configuration::getInstance();
            $configuration->http_user_agent = sprintf("eveseat-srp/%s (Denngarr B'tarn;Cripple Creek;N7VY ALLIANCE)",
                config('srp.config.version'));
            $configuration->logger_level = Logger::DEBUG;
            $configuration->logfile_location = storage_path('logs/eseye.log');
            $configuration->file_cache_location = storage_path('app/eseye/');
            $configuration->datasource = 'tranquility';

            $esi = new Eseye();
            $response = $esi->setVersion('v1')->invoke('get', '/insurance/prices/');

            foreach ($response as $entry) {

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

        } catch (EsiScopeAccessDeniedException $e) {

            $this->writeErrorJobLog('An EsiScopeAccessDeniedException occurred while processing ESI Insurances Update. ' .
            'This normally means the key does not have access.');

        } catch (RequestFailedException $e) {

            $this->writeErrorJobLog('A RequestFailedException occurred while processing ESI Insurances Update. ' .
                                    'This normally means the request is wrong.');

        } catch (Exception $e) {

            $this->writeErrorJobLog('An Exception occurred while processing ESI Insurances Update. Something terrible append !');

        }

        $this->writeInfoJobLog(sprintf('The full update run took %ss to complete.',
            number_format(microtime(true) - $job_start, 2)));

        $this->updateJobStatus([
            'status' => 'Done',
            'output' => null,
        ]);

        return;
    }

}
