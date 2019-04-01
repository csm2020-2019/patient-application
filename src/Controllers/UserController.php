<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Database\Database;
use csm2020\PatientApp\Models\Patient;
use csm2020\PatientApp\Models\User;

class UserController
{

    public function __construct()
    {

    }

    public function register($username, $email, $password, $firstName, $lastName, $pid)
    {
        // Check if validate patient first
        $patient = Patient::getPatientByPatientId($pid);
        if (!$patient) {
            return null;
        }
        Database::sanitise($username);
        Database::sanitise($email);
        Database::sanitise($password);
        Database::sanitise($firstName);
        Database::sanitise($lastName);
        $array = [
            'username' =>   $username,
            'email' =>      $email,
            'password' =>   $password,
            'firstName' =>  $firstName,
            'lastName' =>   $lastName
        ];
        if (!$this->validateUserDetails($array) || !filter_var($email, FILTER_SANITIZE_EMAIL)) {
            return null;
        }

        $user = User::register($array);
        if (!$user) {
            return null;
        }
        // Update patient accordingly
        $patient->setUserId($user);
        $patient->update();
        return true;
    }

    public function validateUserDetails($details)
    {
        foreach ($details as $detail) {
            if (!$detail || strlen($detail) > 45) {
                return null;
            }
        }
        return true;
    }

    public function getGPDetailsById($gpId)
    {
        $user = User::getUserById($gpId);
        if ($user->getUserType() === 'gp') {
            return [
                'gp_first_name' =>  $user->getUserFirstName(),
                'gp_last_name' =>   $user->getUserLastName(),
                'gp_email' =>       $user->getUserEmail()
            ];
        }
        return null;
    }
}
