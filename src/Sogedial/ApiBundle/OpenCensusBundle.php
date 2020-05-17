<?php

namespace Sogedial\ApiBundle;

use OpenCensus\Trace\Exporter\StackdriverExporter;
use OpenCensus\Trace\Integrations\Mysql;
use OpenCensus\Trace\Integrations\PDO;
use OpenCensus\Trace\Integrations\Symfony;
use OpenCensus\Trace\Tracer;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OpenCensusBundle extends Bundle
{
    public function boot()
    {
        $this->setupOpenCensus();
    }

    private function setupOpenCensus()
    {
        // trace agent configs
        $GOOGLE_CLOUD_PROJECT = getenv('GOOGLE_CLOUD_PROJECT');
        $TRACE_AGENT_ENABLED = getenv('TRACE_AGENT_ENABLED');

        if (php_sapi_name() == 'cli' || $GOOGLE_CLOUD_PROJECT === false || $TRACE_AGENT_ENABLED == false ) {
            return;
        }

        // Enable OpenCensus extension integrations
        //Mysql::load();
        PDO::load();
        Symfony::load();

        // Start the request tracing for this request
        $exporter = new StackdriverExporter([
               'clientConfig' => [
                  'projectId' => $GOOGLE_CLOUD_PROJECT
               ]
            ]);
        Tracer::start($exporter);
    }
}
