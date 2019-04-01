<?php
namespace csm2020\PatientApp\Tests;

use csm2020\PatientApp\Config\Config;
use csm2020\PatientApp\Database\Database;
use PHPUnit_Framework_TestCase;

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    public function testSanitise()
    {
        $dirtyInput = '<script>alert("Hello World!");</script>';
        $checkedInput = Database::sanitise($dirtyInput);
        $this->assertNotTrue($checkedInput);
    }

    public function testDatabase()
    {
        $db = Database::getDatabase();

        $this->assertNotNull($db);
        $this->assertInstanceOf('PDO', $db);
    }
}
