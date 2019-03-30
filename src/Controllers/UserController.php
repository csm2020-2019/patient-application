<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Database\Database;
use csm2020\PatientApp\Models\User;

use PDO;
use PDOException;

class UserController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getDatabase();
    }

    public function getUserById($uid)
    {
        if (!$uid) {
            return null;
        }
        try {
            $stmt = $this->db->prepare('SELECT * FROM user WHERE userId = :uid LIMIT 1');
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = User::factory($results);

            return $user;
        } catch (PDOException $e) {
            return null;
        }
    }
}
