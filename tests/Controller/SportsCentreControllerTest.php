<?php
namespace csm2020\PatientApp\Tests;

use csm2020\PatientApp\Controllers\SportsCentreController;
use PHPUnit_Framework_TestCase;

class SportsCentreControllerTest extends PHPUnit_Framework_TestCase
{
    public function testGetAllSportsCentres()
    {
        $controller = new SportsCentreController();
        $centres = $controller->getAllSportsCentres();

        $this->assertNotNull($centres);
        $this->assertArrayHasKey('sportsCenterId', $centres[0]);
    }
}
