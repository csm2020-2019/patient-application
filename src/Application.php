<?php
namespace csm2020\PatientApp;

use csm2020\PatientApp\Config\Config;
use csm2020\PatientApp\Database\Database;
use csm2020\PatientApp\Router\Router;

class PatientApp
{
    private static $instance;

    private $config;
    private $database;
    private $router;

    private function __construct()
    {
        $this->config = Config::getConfig();
        $this->database = Database::getDatabase();
        $this->router = new Router();
    }

    public static function init(): PatientApp
    {
        if (!isset(self::$instance)) {
            self::$instance = new PatientApp();
        }
        return self::$instance;
    }

    public function run()
    {
        echo $this->router->route();
    }
}
