<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Config\Config;
use csm2020\PatientApp\Database\Database;

/**
 * Class FeedbackController
 * @package csm2020\PatientApp\Controllers
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class FeedbackController
{
    const ANONYMOUS = 'noreply@feedback.com';

    private $recipient;

    public function __construct()
    {
        $this->recipient = Config::getConfig()['email-recipient'] ?? self::ANONYMOUS;
    }

    /**
     * Feedback Method
     * @param $content
     * @param string $from
     * @return bool|null
     *
     * This method takes two parameters - one is the feedback content, and the other is an email address, which is
     * optional should the user desire an email response.
     *
     * After doing some initial sanitisation of inputs, returning null should any of them fail, it builds an email
     * message with the gathered information and sends to to the email address stored in the program's configuration. If
     * this succeeds, and the user opted to provide an email address and isn't an anonymous submitter, the method will
     * also send a secondary email confirming and thanking the user for their feedback.
     *
     * If it all goes right, true is returned by the method. If any problems are ran into by PHP's mail methods then
     * null will also be returned, causing an error message on the frontend.
     */
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
