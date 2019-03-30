<?php
namespace csm2020\PatientApp\Models;

use csm2020\PatientApp\Database\Database;

use PDO;
use PDOException;

class User
{
    private $userId;
    private $username;
    private $userPassword;
    private $userEmail;
    private $userFirstName;
    private $userLastName;
    private $userType;

    private function __construct($uid, $username, $password, $email, $firstName, $lastName, $userType)
    {
        $this->userId =         $uid;
        $this->username =       $username;
        $this->userPassword =   $password;
        $this->userEmail =      $email;
        $this->userFirstName =  $firstName;
        $this->userLastName =   $lastName;
        $this->userType =       $userType;
    }

    private function __clone() {}

    public static function factory($ingredients)
    {
        $user = new User(
            $ingredients['userId'],
            $ingredients['username'],
            $ingredients['userPassword'],
            $ingredients['userEmail'],
            $ingredients['userFirstName'],
            $ingredients['userLastName'],
            $ingredients['userType']
        );
            return $user;
    }

    public static function getUserById($uid)
    {
        $db = Database::getDatabase();
        try {
            $stmt = $db->prepare('SELECT * FROM user WHERE userId = :uid LIMIT 1');
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();

            $ingredients = $stmt->fetch(PDO::FETCH_ASSOC);
            if (count($ingredients) === 0) {
                return null;
            }
        } catch (PDOException $e) {
            return null;
        }
        return self::factory($ingredients);
    }

    public function getDisplayableInfo()
    {
        return [
            'uid'         => $this->getUserId(),
            'username'    => $this->getUsername(),
            'firstName'   => $this->getUserFirstName(),
            'lastName'    => $this->getUserLastName(),
            'email'       => $this->getUserEmail(),
        ];
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUserPassword()
    {
        return $this->userPassword;
    }

    /**
     * @param mixed $userPassword
     */
    public function setUserPassword($userPassword)
    {
        $this->userPassword = $userPassword;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @param mixed $userEmail
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
    }

    /**
     * @return mixed
     */
    public function getUserFirstName()
    {
        return $this->userFirstName;
    }

    /**
     * @param mixed $userFirstName
     */
    public function setUserFirstName($userFirstName)
    {
        $this->userFirstName = $userFirstName;
    }

    /**
     * @return mixed
     */
    public function getUserLastName()
    {
        return $this->userLastName;
    }

    /**
     * @param mixed $userLastName
     */
    public function setUserLastName($userLastName)
    {
        $this->userLastName = $userLastName;
    }

    /**
     * @return mixed
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @param mixed $userType
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }


}
