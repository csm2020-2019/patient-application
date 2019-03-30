<?php
namespace csm2020\PatientApp\Router;

use csm2020\PatientApp\Authentication\Authentication;
use csm2020\PatientApp\Controllers\PatientController;
use csm2020\PatientApp\Controllers\RegimeController;
use csm2020\PatientApp\Models\User;

class Router
{
    const BAD_EMAIL =               'Invalid email submitted';
    const NO_COMMAND =              'No command specified';
    const SUBMISSION_FAILURE =      'Data submission unsuccessful';
    const UNAUTHORISED =            'Unauthorised';
    const UNRECOGNISED_COMMAND =    'Unrecognised command specified';

    private $auth;
    private $responseData = [];
    private $user;

    public function __construct()
    {
        $this->auth = new Authentication();
        $this->responseData = [];
        $this->user = null;
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

        $this->responseData             = $tokenData;
        $this->user                     = User::getUserById($this->auth->getId($_POST['token']));
        $this->responseData['user']     = $this->user->getDisplayableInfo();

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
                if (!$this->responseData['patient'] = $controller->get($this->user->getUserId())) {
                    return $this->error(self::UNAUTHORISED, $tokenData);
                    break;
                }
                break;
            case 'patient-address':
                $controller = new PatientController();
                if (!$controller->address(
                    $_POST['address1'],
                    $_POST['address2'],
                    $_POST['town'],
                    $_POST['postcode'],
                    $this->user->getUserId())) {
                    return $this->error(self::SUBMISSION_FAILURE, $tokenData);
                }
                break;
            case 'patient-email':
                $controller = new PatientController();
                if (!$controller->email(
                    $_POST['email'],
                    $this->user->getUserId())) {
                    return $this->error(self::BAD_EMAIL, $tokenData);
                }
                break;
            case 'patient-subscription':
                $controller = new PatientController();
                if (!$controller->emailSubscription(
                    $_POST['subscription'],
                    $this->user->getUserId())) {
                    return $this->error(self::SUBMISSION_FAILURE, $tokenData);
                }
                break;
            case 'regimes':
                $controller = new RegimeController();
                $this->responseData['regimes'] = $controller->regimes($this->user->getUserId());
                if ($this->responseData['regimes'] === null) {
                    return $this->error(self::UNAUTHORISED, $tokenData);
                }
                break;
            case 'regime':
                $regimeController = new RegimeController();
                $this->responseData['regime'] = $regimeController->regime($_POST['regime_id']);
                if ($this->responseData['regime'] === null) {
                    return $this->error(self::UNAUTHORISED, $tokenData);
                }
                break;
            case 'sportscentres':
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
            case self::BAD_EMAIL:
            case self::NO_COMMAND:
            case self::SUBMISSION_FAILURE:
            case self::UNRECOGNISED_COMMAND:
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
