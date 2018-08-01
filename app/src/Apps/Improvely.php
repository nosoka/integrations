<?php namespace Startupbros\Apps;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Startupbros\Libraries\Logger;

class Improvely
{
    private $project;
    private $eventUrl;

    public function __construct(Logger $logger, Client $guzzle) {
        $this->logger = $logger;
        $this->guzzle = $guzzle;
        $this->conversionUrl = getenv('IMPROVELY_CONVERSION_URL');
    }

    public function conversion($payload = []) {
        $this->logger->info("Payload posted to Improvely", ['data' => $payload]);

        $message            = "Response received from Improvely";
        $payload['key']     = getenv('IMPROVELY_API_KEY');
        $payload['project'] = getenv('IMPROVELY_PROJECT');

        try {
            $response = $this->guzzle->post($this->conversionUrl, ['form_params' => $payload]);
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
