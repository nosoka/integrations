<?php namespace Startupbros\Integrations;

use Startupbros\Apps\SamCart;
use Startupbros\Apps\Woopra;
use Startupbros\Libraries\Mailer;
use Psr\Log\LoggerInterface;
use Slim\Collection;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use SlimSession\Helper;
use Swift_Message;
use Swift_Attachment;

class SamCartToWoopra
{
    private $scData;
    private $woopraPayload;

    public function __construct(Request $request, Response $response, Twig $view, Helper $session,
        LoggerInterface $logger, Collection $settings, SamCart $samcart, Woopra $woopra, Mailer $mailer) {
        $this->request  = $request;
        $this->response = $response;
        $this->view     = $view;
        $this->session  = $session;
        $this->logger   = $logger;
        $this->samcart  = $samcart;
        $this->woopra   = $woopra;
        $this->mailer   = $mailer;
        $this->settings = $settings;

        $this->session->set('events', []);
    }

    public function run() {
        // TODO:: raise/catch any exceptions
        $this->processIncomingPayload()
            ->prepareOutgoingPayload()
            ->runIntegration()
            ->sendEmailReport()
            ;

        return $this->response->withJson(['status' => 'success'], 200);
    }

    public function processIncomingPayload() {
        $this->scData = $this->samcart->processNotification(json_decode($this->request->getBody()->getContents()));
        return $this;
    }

    public function prepareOutgoingPayload() {
        $sc = $this->scData;
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

        $events = $this->session->events;
        $textBody = $this->view->fetch('mails/events.txt.twig', ['events' => $events]);
        $htmlBody = $this->view->fetch('mails/events.html.twig', ['events' => $events]);

        $message = (new Swift_Message())
            ->setSubject('New Integration Event:: SamCart to Woopra')
            ->setFrom([$this->settings['email']['from']['address'] => $this->settings['email']['from']['name']])
            ->setTo([$this->settings['email']['to']['address'] => $this->settings['email']['to']['name']])
            ->setBody(html_entity_decode($textBody))
            ->addPart($htmlBody, 'text/html')
            ->attach(Swift_Attachment::fromPath($this->session->logfile))
        ;

        $this->mailer->getMailer()->send($message);
    }
}
