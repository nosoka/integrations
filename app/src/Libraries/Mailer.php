<?php namespace Startupbros\Libraries;

use Slim\Collection;
use Swift_MailTransport;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Swift_Mailer;
use Openbuildings\Swiftmailer\CssInlinerPlugin;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class Mailer
{

    public function __construct(Collection $settings)
    {
        $this->settings = $settings;

        $this->setTransport();
        $this->setMailer();
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
            return false;
        }
    }

    public function setMailer()
    {
        $this->mailer = new Swift_Mailer($this->transport);
        $this->mailer->registerPlugin(new CssInlinerPlugin(new CssToInlineStyles()));
    }

    public function getTransport()
    {
        return $this->transport;
    }

    public function getMailer()
    {
        return $this->mailer;
    }
}
