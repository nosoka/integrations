<?php namespace App\Libraries;

use Slim\Views\Twig;
use Swift_Attachment;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_Message;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Openbuildings\Swiftmailer\CssInlinerPlugin;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class Mailer extends Swift_Mailer
{
    private $transport;

    public function __construct($settings = [], Twig $view) {
        $this->view     = $view;
        $this->settings = $settings;

        $this->setTransport();
        parent::__construct($this->transport);
    }

    public function setTransport()
    {
        if($this->settings['email']['driver'] == 'mail') {
            $this->transport = Swift_MailTransport::newInstance();
        } else if($this->settings['email']['driver'] == 'sendmail') {
            $this->transport = Swift_SendmailTransport::newInstance();
        } else if($this->settings['email']['driver'] == 'smtp') {
            $this->transport = Swift_SmtpTransport::newInstance();
            $this->transport->setHost($this->settings['email']['smtp']['server']);
            $this->transport->setPort($this->settings['email']['smtp']['port']);
            $this->transport->setUsername($this->settings['email']['smtp']['username']);
            $this->transport->setPassword($this->settings['email']['smtp']['password']);
            $this->transport->setEncryption($this->settings['email']['smtp']['encryption']);
        } else {
            return;
        }
        if(isset($this->transport)) {
            $this->transport->registerPlugin(new CssInlinerPlugin(new CssToInlineStyles()));
        }
    }

    public function sendEventsReport($options) {
        $events   = $_SESSION['events'];
        $eventLog = $_SESSION['eventLog'];
        $textBody = $this->view->fetch('mails/events.txt.twig', ['events' => $events]);
        $htmlBody = $this->view->fetch('mails/events.html.twig', ['events' => $events]);
        $subject  = isset($options['subject']) ? $options['subject'] : 'New Integration Event';

        $message  = (new Swift_Message())
            ->setSubject($subject)
            ->setFrom([$this->settings['email']['from']['address'] => $this->settings['email']['from']['name']])
            ->setTo([$this->settings['email']['to']['address'] => $this->settings['email']['to']['name']])
            ->setBody(html_entity_decode($textBody))
            ->addPart($htmlBody, 'text/html')
        ;

        if(file_exists($eventLog)) {
            $message->attach(Swift_Attachment::fromPath($eventLog));
        }

        $this->send($message);
    }
}
