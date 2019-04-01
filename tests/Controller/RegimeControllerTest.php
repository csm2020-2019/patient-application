<?php
namespace csm2020\PatientApp\Tests;

use csm2020\PatientApp\Controllers\RegimeController;
use PHPUnit_Framework_TestCase;

class RegimeControllerTest extends PHPUnit_Framework_TestCase
{
    public function testRegimes()
    {
        $uid = 1;
        $controller = new RegimeController();
        $regimes = $controller->regimes($uid);

        $this->assertNotNull($regimes);
    }

    public function testRegime()
    {
        $rid = 1;
        $uid = 2;
        $controller = new RegimeController();
        $regime = $controller->regime($rid, $uid);

        $this->assertNotNull($regime);
        $this->assertArrayHasKey('trials', $regime);
        $this->assertArrayHasKey('gp', $regime);
    }
}
