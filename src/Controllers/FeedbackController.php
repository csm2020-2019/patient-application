<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Config\Config;
use csm2020\PatientApp\Database\Database;

class FeedbackController
{
    const ANONYMOUS = 'noreply@feedback.com';

    private $recipient;

    public function __construct()
    {
        $this->recipient = Config::getConfig()['email-recipient'] ?? self::ANONYMOUS;
    }

    public function feedback($content, $from = self::ANONYMOUS)
    {
        $content =  Database::sanitise($content);
        $from =     Database::sanitise($from);
        if (!$from || !$content) {
            return null;
        }
        if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $to =       $this->getRecipient();
        $headers =  "From: {$from}";
        $subject =  'Feedback from PatientApp';
        $message =
"New feedback has been received from ${from} containing the following feedback: 

${content}";
        // Send feedback to author
        if (!mail($to, $subject, $message, $headers)) {
            return null;
        }

        $replyHeaders = "From: {$to}";
        $reply =
"Your feedback has been successfully received and you can expect a reply shortly! Thank you for your time and for your continued usage of the web app!
            
In summary, your feedback was: {$content}
            
            
Yours,
Development Team";

        // Send reply to sender
        if ($from !== self::ANONYMOUS) {
            if (!mail($from, $subject, $reply, $replyHeaders)) {
                return null;
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient(string $recipient)
    {
        $this->recipient = $recipient;
    }
}
