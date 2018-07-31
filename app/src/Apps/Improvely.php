<?php namespace Startupbros\Apps;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Startupbros\Libraries\Logger;

class Improvely
{
    private $project;
    private $eventUrl;

    public function __construct(Logger $logger, Client $guzzle) {
        $this->logger = $logger;
        $this->guzzle = $guzzle;
        $this->eventUrl = getenv('IMPROVELY_CONVERSION_URL');
    }

    public function conversion($payload = []) {
        $payload['key']     = getenv('IMPROVELY_API_KEY');
        $payload['project'] = getenv('IMPROVELY_PROJECT');

        $this->logger->event("Payload posted to Improvely", ['data' => $payload]);

        try {
            $response = $this->guzzle->post($this->eventUrl, ['form_params' => $payload]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        $data = (string) $response->getBody();
        $this->logger->event("Response received from Improvely - {$response->getStatusCode()} {$response->getReasonPhrase()}", ['data' => json_decode($data), 'nolog' => true]);

        return $response;
    }
}


