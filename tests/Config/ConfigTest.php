<?php
namespace csm2020\PatientApp\Tests;

use csm2020\PatientApp\Config\Config;
use PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testExistenceOfSettingsFile()
    {
        $this->assertFileExists(Config::CONFIG_LOCATION);
    }

    public function testGetConfig()
    {
        $config = Config::getConfig();
        $this->assertNotNull($config);
        $this->assertArrayHasKey('server-url', $config);
    }
}
