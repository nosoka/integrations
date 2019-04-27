<?php namespace App\Apps;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Libraries\Logger;

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
        $this->logger->info("Payload posted to Woopra", ['data' => $eventData]);

        $message              = "Response received from Woopra";
        $eventData['project'] = $this->project;

        // TODO:: maybe log guzzle exceptions everywhere from inside a wrapper class or middleware
        try {
            $response = $this->guzzle->post($this->eventUrl, ['form_params' => $eventData]);
        } catch (ClientException $e) {
            $this->logger->error($message, ['data' => json_decode($e->getResponse()->getBody())]);
            return;
        } catch (ConnectException $e) {
            $this->logger->error($message, ['data' => $e->getMessage()]);
            return;
        }

        $message .= " - {$response->getStatusCode()} {$response->getReasonPhrase()}";
        $body     = json_decode($response->getBody(), true) ?: (string) $response->getBody();
        $this->logger->success($message, ['data' => $body]);

        return $response;
    }
}
