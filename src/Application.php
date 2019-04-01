<?php
namespace csm2020\PatientApp;

use csm2020\PatientApp\Config\Config;
use csm2020\PatientApp\Database\Database;
use csm2020\PatientApp\Router\Router;

/**
 * Class PatientApp
 * @package csm2020\PatientApp
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class PatientApp
{
    /**
     * @var
     */
    private static $instance;

    /**
     * @var array
     */
    private $config;
    /**
     * @var \PDO
     */
    private $database;
    /**
     * @var Router
     */
    private $router;

    /**
     * PatientApp constructor.
     */
    private function __construct()
    {
        $this->config = Config::getConfig();
        $this->database = Database::getDatabase();
        $this->router = new Router();
    }

    /**
     * Bootstraps this beast.
     *
     * @return PatientApp
     */
    public static function init(): PatientApp
    {
        if (!isset(self::$instance)) {
            self::$instance = new PatientApp();
        }
        return self::$instance;
    }

    /**
     * Run
     * @throws \Exception
     *
     * This is where the magic happens. Routes. Sets headers. Echoes.
     */
    public function run()
    {
        $json = $this->router->route();
        $this->setHeaders();
        echo $json;
    }

    /**
     * Set Header Method
     *
     * Tells AJAX or the web browser to expect JSON as a response.
     */
    private function setHeaders()
    {
        header("Content-Type: application/json; charset=UTF-8");
    }
}
