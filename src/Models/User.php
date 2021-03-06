<?php
namespace csm2020\PatientApp\Models;

use csm2020\PatientApp\Database\Database;

use PDO;
use PDOException;

/**
 * Class User
 * @package csm2020\PatientApp\Models
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class User
{
    /**
     * @var
     */
    private $userId;
    /**
     * @var
     */
    private $username;
    /**
     * @var
     */
    private $userPassword;
    /**
     * @var
     */
    private $userEmail;
    /**
     * @var
     */
    private $userFirstName;
    /**
     * @var
     */
    private $userLastName;
    /**
     * @var
     */
    private $userType;

    /**
     * User constructor.
     * @param $uid
     * @param $username
     * @param $password
     * @param $email
     * @param $firstName
     * @param $lastName
     * @param $userType
     */
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

    /**
     *
     */
    private function __clone() {}

    /**
     * @param $ingredients
     * @return User
     */
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

    /**
     * Get User By ID Method
     * @param $uid
     * @return User|null
     *
     * Another incredibly important method as it is used so frequently throughout the program. Retrieves a User object
     * based on the provided user ID, which quite often is the user ID provided by the token. Null on fail.
     */
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

    /**
     * Register Method
     * @param $ingredients
     * @return string|null
     *
     * This is the second part of the register method found in the controller. The ingredients necessary for creating
     * a user are forwarded to this method in an array, which is checked. An SQL query is constructed, as all
     * sanitisation and validation have already been taken care of in the controller, and is executed. If successful,
     * this method actually returns the autoincrement ID of the user it just created in the database, so that additional
     * actions can take place in the controller - predominantly using that ID to finish up changes to the patient
     * table. Null on fail.
     */
    public static function register($ingredients)
    {
        if (!$ingredients) {
            return null;
        }
        $db = Database::getDatabase();
        $userType = 'patient';
        try {
            $stmt = $db->prepare('INSERT INTO user (username, userPassword, userEmail, userFirstName, 
userLastName, userType) VALUES (:username, :password, :email, :firstName, :lastName, :userType)');
            $stmt->bindParam(':username',   $ingredients['username']);
            $stmt->bindParam(':password',   $ingredients['password']);
            $stmt->bindParam(':email',      $ingredients['email']);
            $stmt->bindParam(':firstName',  $ingredients['firstName']);
            $stmt->bindParam(':lastName',   $ingredients['lastName']);
            $stmt->bindParam(':userType',   $userType);
            $stmt->execute();

            return $db->lastInsertId();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * @return array
     */
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
