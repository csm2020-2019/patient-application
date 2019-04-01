<?php
namespace csm2020\PatientApp\Database;

use csm2020\PatientApp\Config\Config;

use PDO;
use PDOException;

/**
 * Class Database
 * @package csm2020\PatientApp\Database
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class Database
{
    private static $db;

    private function __construct() {}
    private function __clone() {}

    /**
     * Get Database Method
     * @return PDO
     *
     * Arguably the most important method in the entire program. This static method will return a PDO singleton to
     * whatever calls it, allowing for database connectivity, predominantly in models.
     *
     * Should the database fail at the initialisation stage, the program will fall over.
     */
    public static function getDatabase(): PDO
    {
        if (!isset(self::$db)) {
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
        return self::$db;
    }

    /**
     * Sanitise Helper Method
     * @param $input
     * @return string
     *
     * Static method that can be called wherever the Database class is being used (a lot of places!) that will clean
     * a string of any naughty business, including HTML tags, Unicode characters, and whitespace. It's not completely
     * foolproof, especially against SQL injection, which is why it's important to use it in conjunction with using
     * prepared statements, as all uses of the PDO object do as of this time of writing. It will however defuse any
     * JavaScript injection attacks. Hopefully.
     */
    public static function sanitise($input)
    {
        return trim(stripslashes(htmlspecialchars(strip_tags($input))));
    }
}
