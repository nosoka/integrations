<?php namespace App\Apps;

use App\Libraries\Logger;

class Samcart
{
    private $knownEvents   = [
        'Order', 'Refund', 'Cancel', 'RecurringPaymentFailed',
        'RecurringPaymentRecovered', 'RecurringPaymentSucceeded'
    ];

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function processNotification($payload) {

        $this->logger->info("Received payload from SamCart", ['data' => $payload]);

        $event   = isset($payload->type) ? $payload->type : null;
        $event   = in_array(strtolower($event), array_map('strtolower', $this->knownEvents)) ? $event : 'Samcart';

        $revenue = isset($payload->product->price) ? $payload->product->price : null;
        $revenue = (strtolower($event) == "refund") ? -abs($revenue) : $revenue;

        $payload->event   = $event;
        $payload->revenue = $revenue;
        return $payload;
    }
}
