<?php
namespace csm2020\PatientApp\Tests;

use csm2020\PatientApp\Controllers\TrialsController;
use PHPUnit_Framework_TestCase;

class TrialsControllerTest extends PHPUnit_Framework_TestCase
{
    public function testGetTrialsByRegimeId()
    {
        $rid = 1;
        $controller = new TrialsController();
        $trials = $controller->getTrialsByRegimeId($rid);

        $this->assertNotNull($trials);
    }
}
