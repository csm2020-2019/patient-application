<?php
namespace csm2020\PatientApp\Router;

use csm2020\PatientApp\Authentication\Authentication;

class Router
{
    const UNAUTHORISED = 'Unauthorised';
    const NO_COMMAND = 'Authenticated, but no command specified';
    const UNRECOGNISED_COMMAND = 'Authenticated, but unrecognised command specified';

    private $auth;
    private $json = [];

    public function __construct()
    {
        $this->auth = new Authentication();
        $this->json = [];
    }

    public function route(): String
    {
        // Check if there's no token, if so, we need to do initial authentication
        if (!isset($_POST['token'])) {
            $loginCheck = $this->auth->login();
            if (!$loginCheck) {
                return $this->returnError(self::UNAUTHORISED);
            }
            return json_encode($loginCheck);
        }

        // Time to check token and do the rest of the routing
        $tokenCheck = $this->auth->tokenAuthenticate($_POST['token']);
        if (!isset($tokenCheck)) {
            return json_encode($this->returnError(self::UNAUTHORISED));
        }
        $this->json = $tokenCheck;

        // Did they send a request?
        if (!isset($_POST['request'])) {
            return $this->returnError(self::NO_COMMAND, $tokenCheck);
        }

        // In a modern PHP framework, this would be like a list of routes. Yeah.
        switch ($_POST['request']) {
            case 'all':
                // Return everything;
                break;
            case 'programmes':
                // Spin up the controller and do stuff
                break;
            default:
                return $this->returnError(self::UNRECOGNISED_COMMAND, $tokenCheck);
                break;
        }
        return json_encode($this->json);
    }

    private function returnError(String $errorCode, array $auth = null): String
    {
        $this->setResponseCode($errorCode);
        return json_encode(['status' => 'error', 'message' => $errorCode, 'auth' => $auth ?? null]);
    }

    private function setResponseCode($errorCode)
    {
        $responseCode = 200; // OK is default
        switch ($errorCode) {
            case self::UNRECOGNISED_COMMAND:
            case self::NO_COMMAND:
                $responseCode = 400;
                break;
            case self::UNAUTHORISED:
                $responseCode = 403; // I know this is technically forbidden
                break;
            default:
                $responseCode = 200;
                break;
        }
        http_response_code($responseCode);
    }

}
