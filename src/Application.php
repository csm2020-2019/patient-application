<?php

namespace csm2020\PatientApp;

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
        $this->router = Router::getRouter();
    }

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new PatientApp();
        }
        return self::$instance;
    }

    public function run()
    {
        $this->router->route();
    }
}
