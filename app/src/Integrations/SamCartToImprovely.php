<?php namespace Integrations;

use Apps\SamCart;
use Apps\Improvely;
use Psr\Log\LoggerInterface;
use Slim\Collection;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use SlimSession\Helper;
use Libraries\Mailer;
use Swift_Message;
use Swift_Attachment;

class SamCartToImprovely
{
    private $scData;
    private $improvelyPayload;

    public function __construct(Request $request, Response $response, Twig $view, Helper $session,
        LoggerInterface $logger, Collection $settings, SamCart $samcart, Improvely $improvely, Mailer $mailer) {
        $this->request   = $request;
        $this->response  = $response;
        $this->view      = $view;
        $this->session   = $session;
        $this->logger    = $logger;
        $this->samcart   = $samcart;
        $this->improvely = $improvely;
        $this->mailer    = $mailer;
        $this->settings  = $settings;

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

        $events = $this->session->events;
        $textBody = $this->view->fetch('mails/events.txt.twig', ['events' => $events]);
        $htmlBody = $this->view->fetch('mails/events.html.twig', ['events' => $events]);

        $message = (new Swift_Message())
            ->setSubject('New Integration Event:: SamCart to Improvely')
            ->setFrom([$this->settings['email']['from']['address'] => $this->settings['email']['from']['name']])
            ->setTo([$this->settings['email']['to']['address'] => $this->settings['email']['to']['name']])
            ->setBody(html_entity_decode($textBody))
            ->addPart($htmlBody, 'text/html')
            ->attach(Swift_Attachment::fromPath($this->session->logfile))
        ;

        $this->mailer->getMailer()->send($message);
    }
}
