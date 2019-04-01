<?php
namespace csm2020\PatientApp\Tests;

use csm2020\PatientApp\Controllers\UserController;
use PHPUnit_Framework_TestCase;

class UserControllerTest extends PHPUnit_Framework_TestCase
{
    public function testValidateUserDetails()
    {
        $details = ['username', 'email@email.com', 'password', 'Oliver', 'Earl'];
        $controller = new UserController();
        $results = $controller->validateUserDetails($details);

        $this->assertNotNull($results);
        $this->assertTrue($results);
    }

    public function testValidateInvalidUserDetails()
    {
        $poorDetails = ['u', 'notemail', null, 'oleb', ';;;;;'];
        $controller = new UserController();
        $results = $controller->validateUserDetails($poorDetails);

        $this->assertNull($results);
    }
}
