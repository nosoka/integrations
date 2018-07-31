<?php namespace Startupbros\Libraries;

use Swift_Mailer;
use Swift_MailTransport;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Openbuildings\Swiftmailer\CssInlinerPlugin;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class Mailer extends Swift_Mailer
{
    private $transport;

    public function __construct($settings = []) {
        $this->setTransport($settings);
        parent::__construct($this->transport);
    }

    public function setTransport($settings)
    {
        if($settings['email']['driver'] == 'mail') {
            $this->transport = Swift_MailTransport::newInstance();
        } else if($settings['email']['driver'] == 'sendmail') {
            $this->transport = Swift_SendmailTransport::newInstance();
        } else if($settings['email']['driver'] == 'smtp') {
            $this->transport = Swift_SmtpTransport::newInstance();
            $this->transport->setHost($settings['email']['smtp']['server']);
            $this->transport->setPort($settings['email']['smtp']['port']);
            $this->transport->setUsername($settings['email']['smtp']['username']);
            $this->transport->setPassword($settings['email']['smtp']['password']);
            $this->transport->setEncryption($settings['email']['smtp']['encryption']);
        } else {
            return;
        }
        if(isset($this->transport)) {
            $this->transport->registerPlugin(new CssInlinerPlugin(new CssToInlineStyles()));
        }
    }
}
