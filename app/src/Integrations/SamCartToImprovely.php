<?php namespace Startupbros\Integrations;

use Slim\Http\Request;
use Slim\Http\Response;
use Startupbros\Apps\SamCart;
use Startupbros\Apps\Improvely;
use Startupbros\Libraries\Mailer;

class SamCartToImprovely
{
    private $samcartPayload;
    private $improvelyPayload;

    public function __construct(Mailer $mailer, SamCart $samcart, Improvely $improvely) {
        $this->samcart   = $samcart;
        $this->improvely = $improvely;
        $this->mailer    = $mailer;
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
        $this->improvelyPayload = array(
            'goal'               => $sc->event,
            'revenue'            => $sc->revenue,
            'label'              => isset($sc->customer->email) ? $sc->customer->email : null,
            'reference'          => isset($sc->order->id) ? $sc->order->id : null,
            'previous_reference' => '',
        );

        return $this;
    }

    public function runIntegration() {
        $this->improvelyResponse = $this->improvely->conversion($this->improvelyPayload);
        return $this;
    }

    public function sendEmailReport() {
        $this->mailer->sendEventsReport(['subject' => 'New Integration Event:: SamCart to Improvely']);
        return $this;
    }
}
