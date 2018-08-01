<?php namespace Startupbros\Integrations;

use Slim\Http\Request;
use Slim\Http\Response;
use Startupbros\Apps\SamCart;
use Startupbros\Apps\Woopra;
use Startupbros\Libraries\Mailer;

class SamCartToWoopra
{
    private $samcartPayload;
    private $woopraPayload;

    public function __construct(Mailer $mailer, SamCart $samcart, Woopra $woopra) {
        $this->samcart = $samcart;
        $this->woopra  = $woopra;
        $this->mailer  = $mailer;
    }

    public function run(Request $request, Response $response) {
        $this->request = $request;
        $this->processIncomingPayload()
            ->prepareOutgoingPayload()
            ->runIntegration()
            ->sendEmailReport()
            ;

        return $response->withJson(['status' => 'success'], 200);
    }

    public function processIncomingPayload() {
        $this->samcartPayload = $this->samcart->processNotification(
            json_decode($this->request->getBody()->getContents())
        );
        return $this;
    }

    public function prepareOutgoingPayload() {
        $sc = $this->samcartPayload; // just a temp variable to save typing/screen space while mapping the data
        $this->woopraPayload = array(
            'event'           => $sc->event,
            'cv_email'        => isset($sc->customer->email) ? $sc->customer->email : null,
            'cv_firstname'    => isset($sc->customer->first_name) ? $sc->customer->first_name : null,
            'cv_lastname'     => isset($sc->customer->last_name) ? $sc->customer->last_name : null,
            'cv_phone_number' => isset($sc->customer->phone_number) ? $sc->customer->phone_number : null,

            'ce_revenue'      => $sc->revenue,
            'ce_reference'    => isset($sc->order->id) ? $sc->order->id : null,
            'ce_product'      => isset($sc->product->name) ? $sc->product->name : null,
            'ce_coupon'       => isset($sc->order->coupon) ? $sc->order->coupon : null,
            'ce_affiliate'    => isset($sc->affiliate->id) ? $sc->affiliate->coupon : null,
            'ip'              => isset($sc->order->ip_address) ? $sc->order->ip_address : null,
        );

        return $this;
    }

    public function runIntegration() {
        $this->woopraResponse = $this->woopra->trackEvent($this->woopraPayload);
        return $this;
    }

    public function sendEmailReport() {
        $this->mailer->sendEventsReport(['subject' => 'New Integration Event:: SamCart to Woopra']);
        return $this;
    }
}
