<?php
namespace csm2020\PatientApp\Tests;

use csm2020\PatientApp\Controllers\PatientController;
use PHPUnit_Framework_TestCase;

class PatientControllerTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $id = 1;
        $controller = new PatientController();
        $patientData = $controller->get($id);

        $this->assertNotNull($patientData);
        $this->assertArrayHasKey('displayable', $patientData);
        $this->assertArrayHasKey('editable', $patientData);
    }

    public function testAcceptableAddress()
    {
        $array = ['Address 1', 'Address 2', 'Town', 'SY23 3QQ'];
        $controller = new PatientController();
        $results = $controller->validateAddress($array);

        $this->assertNotFalse($results);
    }

    public function testUnacceptableAddress()
    {
        $array = [
            '122234142434325324324253242344234242342342342342342323523424243523424223423523424234',
            'adres 2',
            '1',
            'postcode'
        ];
        $controller = new PatientController();
        $results = $controller->validateAddress($array);

        $this->assertFalse($results);
    }

    public function testGetAppointment()
    {
        $uid = 1;
        $controller = new PatientController();
        $appointment = $controller->getAppointment($uid);

        $this->assertNotNull($appointment);
    }
}
