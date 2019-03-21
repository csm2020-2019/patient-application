<?php
namespace csm2020\PatientApp;

use csm2020\PatientApp\Config\Config;
use csm2020\PatientApp\Database\Database;
use csm2020\PatientApp\Authentication\Authentication;

class PatientApp
{
    private static $instance;

    private $config;
    private $database;
    private $router;
    private $auth;

    private function __construct()
    {
        $this->config = Config::getConfig();
        $this->database = Database::getDatabase();
        //$this->router = new Router();
        $this->auth = new Authentication();
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
        header("Content-Type: application/json; charset=UTF-8");
        echo $this->auth->login();
        //$this->router->route();
        //var_dump(self::$instance);
    }
}
