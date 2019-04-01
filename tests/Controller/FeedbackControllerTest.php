<?php
namespace csm2020\PatientApp\Tests;

use csm2020\PatientApp\Controllers\FeedbackController;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Tests\Compiler\F;

class FeedbackControllerTest extends PHPUnit_Framework_TestCase
{
    public function testFeedback()
    {
        $content = 'Test email';
        $from = 'ole4@aber.ac.uk';
        $feedback = new FeedbackController();

        $result = $feedback->feedback($content, $from);

        $this->assertNotNull($result);
    }

    public function testAnonymousFeedback()
    {
        $content = 'Anonymous email';
        $feedback = new FeedbackController();

        $result = $feedback->feedback($content);

        $this->assertNotNull($result);
    }

    public function testMalformedFeedback()
    {
        $content = '<script>alert("Hello World");</script>';
        $from = 'ole4@aber.ac.uk';
        $feedback = new FeedbackController();

        $result = $feedback->feedback($content, $from);

        $this->assertNull($result);
    }

    public function testMalformedEmail()
    {
        $content = 'Hello World';
        $from = 'notanemail';
        $feedback = new FeedbackController();

        $result = $feedback->feedback($content, $from);

        $this->assertNull($result);
    }
}
