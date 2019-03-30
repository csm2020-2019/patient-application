<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Models\User;

class UserController
{

    public function __construct()
    {

    }

    public function changeUsername() {}
    public function changePassword() {}

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
