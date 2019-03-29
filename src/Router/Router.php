<?php
namespace csm2020\PatientApp\Router;

use csm2020\PatientApp\Authentication\Authentication;
use csm2020\PatientApp\Controllers\PatientController;
//use csm2020\PatientApp\Models\Patient;

class Router
{
    const UNAUTHORISED =            'Unauthorised';
    const NO_COMMAND =              'No command specified';
    const UNRECOGNISED_COMMAND =    'Unrecognised command specified';
    const SUBMISSION_FAILURE =      'Data submission unsuccessful';

    private $auth;
    private $responseData = [];
    private $userId;

    public function __construct()
    {
        $this->auth = new Authentication();
        $this->responseData = [];
        $this->userId = null;
    }

    public function route()
    {
        // TODO: Please comment me out when you're done! Or affix me to a debug switch!
        if (!isset($_POST['token']) && isset($_GET['token'])) {
            $_POST = $_GET;
        }

        // Check if there's no token, if so, we need to do initial authentication
        if (!isset($_POST['token'])) {
            $loginCheck = $this->auth->login();
            if (!$loginCheck) {
                return $this->error(self::UNAUTHORISED);
            }
            return $this->success($loginCheck, 200);
        }

        // Time to check token and do the rest of the routing
        $tokenData = $this->auth->tokenAuthenticate($_POST['token']);
        if (!isset($tokenData)) {
            return $this->error(self::UNAUTHORISED);
        }
        $this->userId           = $this->auth->getId($_POST['token']);
        $this->responseData     = $tokenData;


        // Did they send a request?
        if (!isset($_POST['request'])) {
            return $this->error(self::NO_COMMAND, $tokenData);
        }

        // In a modern PHP framework, this would be like a list of routes. Yeah.
        switch ($_POST['request']) {
            case 'all':
                // Return everything;
                break;
            case 'patient':
                $controller = new PatientController();
                if (!$this->responseData['patient'] = $controller->get($this->userId)) {
                    return $this->error(self::UNAUTHORISED, $tokenData);
                    break;
                }
                //$this->responseData['patient'] = $controller->get($this->userId);
                break;
            case 'patient-address':
                break;
            case 'patient-subscription':
                $controller = new PatientController();
                if (!$controller->emailSubscription($_POST['subscription'], $this->userId)) {
                    return $this->error(self::SUBMISSION_FAILURE, $tokenData);
                }
                break;
            case 'programmes':
                // Spin up the controller and do stuff
                break;
            default:
                return $this->error(self::UNRECOGNISED_COMMAND, $tokenData);
                break;
        }
        $this->setResponseCode(200);
        return $this->success($this->responseData, 200);
    }

    private function success(array $data, int $code = 200)
    {
        $this->setResponseCode($code);
        return json_encode($data);
    }
    private function error(String $errorCode, array $auth = null): String
    {
        $this->setResponseCode($errorCode);
        return json_encode(['status' => 'error', 'message' => $errorCode, 'auth' => $auth ?? null]);
    }

    private function setResponseCode($code)
    {
        $responseCode = 200; // OK is default
        switch ($code) {
            case self::SUBMISSION_FAILURE:
            case self::UNRECOGNISED_COMMAND:
            case self::NO_COMMAND:
                $responseCode = 400;
                break;
            case self::UNAUTHORISED:
                $responseCode = 403; // I know this is technically forbidden
                break;
            case 200:
            default:
                break;
        }
        http_response_code($responseCode);
    }

}
