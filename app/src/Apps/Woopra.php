<?php namespace Startupbros\Apps;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class Woopra
{
    private $project;
    private $eventUrl;

    public function __construct(LoggerInterface $logger, Client $guzzle) {
        $this->project  = getenv('WOOPRA_PROJECT');
        $this->eventUrl = getenv('WOOPRA_TRACK_EVENT_URL');
        $this->logger   = $logger;
        $this->guzzle   = $guzzle;
    }

    public function trackEvent($eventData = []) {
        $eventData['project'] = $this->project;

        $this->logger->event("Payload posted to Woopra", ['data' => $eventData]);

        $response = $this->guzzle->post($this->eventUrl, ['form_params' => $eventData]);

        $this->logger->event("Response received from Woopra - {$response->getStatusCode()} {$response->getReasonPhrase()}", ['data' => (string) $response->getBody(), 'nolog' => true]);

        return $response;
    }
}
