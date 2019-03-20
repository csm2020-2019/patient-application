<?php
namespace csm2020\PatientApp\Database;

use csm2020\PatientApp\Config\Config;

use PDO;
use PDOException;

class Database
{
    private static $db;

    private function __construct()
    {
        $config = Config::getConfig();

        try {
            $dsn = "{$config['database-type']}:host={$config['database-hostname']};dbname={$config['database-name']}";
            self::$db = new PDO($dsn, $config['database-username'], $config['database-password']);
        } catch (PDOException $ex) {
            // TODO: Better error handling
            echo 'Database error has occurred.';
            die();
        }
    }

    private function __clone(){}

    public static function getDatabase()
    {
        if (!isset(self::$db)) {
            self::$db = new Database();
        }
        return self::$db;
    }
}
