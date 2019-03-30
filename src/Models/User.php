<?php
namespace csm2020\PatientApp\Models;

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

    public static function factory($ingredients)
    {
        return new User(
            $ingredients['userId'],
            $ingredients['username'],
            $ingredients['userPassword'],
            $ingredients['userEmail'],
            $ingredients['userFirstName'],
            $ingredients['userLastName'],
            $ingredients['userType']
        );
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
