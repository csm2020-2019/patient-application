<?php
namespace csm2020\PatientApp\Config;

class Config
{
    const CONFIG_LOCATION = __DIR__ . '/../../settings.ini';

    private static $config;

    private function __construct()
    {
        if (!file_exists(self::CONFIG_LOCATION)) {
            // TODO: Better error handling
            echo 'settings.ini configuration file missing or damaged.';
            die();
        }
        $readConfig = [];
        if (file_exists(self::CONFIG_LOCATION)) {
            if ($readConfig = parse_ini_file(self::CONFIG_LOCATION)) {
                self::$config = $readConfig;
            }
        }
    }

    private function __clone(){}

    // TODO: Better error handling in this entire method
    public static function getConfig(): Array
    {
        if (!isset(self::$config)) {
            $configArray = [];
            if (!file_exists(self::CONFIG_LOCATION)) {

                echo 'settings.ini configuration file missing or damaged.';
                die();
            }

            if ($configArray = parse_ini_file(self::CONFIG_LOCATION)) {
                if (empty($configArray)) {
                    echo 'settings.ini configuration file is malformed or in an unsupported format.';
                    die();
                }
                self::$config = $configArray;
            }
        }
        return self::$config;
    }
}
