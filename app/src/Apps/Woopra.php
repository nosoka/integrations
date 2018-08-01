<?php namespace Startupbros\Apps;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Startupbros\Libraries\Logger;

class Woopra
{
    private $project;
    private $eventUrl;

    public function __construct(Logger $logger, Client $guzzle) {
        $this->project  = getenv('WOOPRA_PROJECT');
        $this->eventUrl = getenv('WOOPRA_TRACK_EVENT_URL');
        $this->logger   = $logger;
        $this->guzzle   = $guzzle;
    }

    public function trackEvent($eventData = []) {
        $eventData['project'] = $this->project;

        $this->logger->event("Payload posted to Woopra", ['data' => $eventData]);

        try {
            $response = $this->guzzle->post($this->eventUrl, ['form_params' => $eventData]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        $this->logger->event("Response received from Woopra - {$response->getStatusCode()} {$response->getReasonPhrase()}", ['data' => (string) $response->getBody(), 'nolog' => true]);

        return $response;
    }
}
