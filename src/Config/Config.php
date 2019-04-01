<?php
namespace csm2020\PatientApp\Config;

/**
 * Class Config
 * @package csm2020\PatientApp\Config
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class Config
{
    /**
     * Settings location
     */
    const CONFIG_LOCATION = __DIR__ . '/../../settings.ini';

    /**
     * @var array|bool
     */
    private static $config;

    /**
     * Config constructor
     * When this singleton is instantiated, it will pull the program configuration out of a settings.ini file.
     *
     * Without this file, the program won't run. No ifs, no buts.
     *
     * TODO: Refactor this outside of the constructor, and into a separate private method.
     */
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

    /**
     *
     */
    private function __clone(){}

    /**
     * Get Config Method
     * @return array
     *
     * Returns the Configuration array from the INI file. If anything goes wrong during this process, the program dies.
     */
    public static function getConfig(): array
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
