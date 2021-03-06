<?php
namespace csm2020\PatientApp\Router;

use csm2020\PatientApp\Authentication\Authentication;
use csm2020\PatientApp\Controllers\FeedbackController;
use csm2020\PatientApp\Controllers\PatientController;
use csm2020\PatientApp\Controllers\RegimeController;
use csm2020\PatientApp\Controllers\SportsCentreController;
use csm2020\PatientApp\Controllers\UserController;
use csm2020\PatientApp\Models\User;

/**
 * Class Router
 * @package csm2020\PatientApp\Router
 * @author Oliver Earl <ole4@aber.ac.uk>
 * @todo Repent for my sins
 */
class Router
{
    /**
     * Invalid email provided
     */
    const BAD_EMAIL =               'Invalid email submitted';
    /**
     * No command provided at all
     */
    const NO_COMMAND =              'No command specified';
    /**
     * Something went wrong during the data submission process - usually a sanitisation problem
     */
    const SUBMISSION_FAILURE =      'Data submission unsuccessful';
    /**
     * Unauthorised - usually no token or trying to access something that doesn't belong to the user
     */
    const UNAUTHORISED =            'Unauthorised';
    /**
     * Something wrong with the user input
     */
    const UNRECOGNISED_COMMAND =    'Unrecognised command specified';

    /**
     * @var Authentication
     */
    private $auth;
    /**
     * @var array
     */
    private $responseData = [];
    /**
     * @var null
     */
    private $user;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->auth = new Authentication();
        $this->responseData = [];
        $this->user = null;
    }

    /**
     * Router Method
     * @return false|string
     * @throws \Exception
     *
     * The absolute behemoth function in the program, a monstrosity of epic proportions. It smells, but is the main
     * reason why this program is written how it is. Because of the unique infrastructure the program is designed for
     * where standard RESTful APIs are simply out of the question and routing has to be done without regard to HTTP
     * method, and are simply specified as POST variables.
     *
     * Users logging in or registering are naturally without a token, so they are taken care of first. If the necessary
     * POST variables aren't present indicating a user registering or logging in, a token is required before anything
     * else is checked. And how are they checked? A gigantic switch statement. What else.
     *
     * Whilst messy and crude - it does work quite well. 'Routes' are effectively cases determined by the 'request'
     * POST variable, as opposed to a path and HTTP method. Unusual but necessary in this case. Should everything go
     * okay, data is added to an associative array which is eventually processed at the end into JSON and is printed
     * to the screen - which is fine for AJAX queries. HTTP response headers are also determined throughout the program.
     *
     * TODO: This whole thing needs rewriting at some stage.
     */
    public function route()
    {
        // Uncomment me if you want to get your debug on
//        if (!isset($_POST['token']) && isset($_GET['token'])) {
//            $_POST = $_GET;
//        }
//        if (!isset($_POST['pid']) && isset($_GET['pid'])) {
//            $_POST = $_GET;
//        }

        // Check if there's no token, if so, we need to do initial authentication
        if (!isset($_POST['token'])) {
            if (isset($_POST['pid'])) {
                $controller = new UserController();
                if (!$controller->register(
                    $_POST['username'],
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['first_name'],
                    $_POST['last_name'],
                    $_POST['pid'])) {
                    return $this->error(self::SUBMISSION_FAILURE);
                }
                return $this->success(['registration' => true], 200);
            }
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
                $this->responseData['regime'] = $regimeController->regime($_POST['regime_id'], $this->user->getUserId());
                if ($this->responseData['regime'] === null) {
                    return $this->error(self::UNAUTHORISED, $tokenData);
                }
                break;
            case 'sportscentres':
                $sportsCentreController = new SportsCentreController();
                $patientController = new PatientController();

                $this->responseData['sportscentres'] = $sportsCentreController->getAllSportsCentres();
                $this->responseData['appointment'] = $patientController->getAppointment($this->user->getUserId());
                if ($this->responseData['sportscentres'] === null || $this->responseData['sportscentres'] === null) {
                    return $this->error(self::UNRECOGNISED_COMMAND, $tokenData);
                }
                break;
            case 'appointment':
                $sportsCentreController = new SportsCentreController();
                if (!$sportsCentreController->setSportsCentre($this->user->getUserId(), $_POST['appointment'])) {
                    return $this->error(self::SUBMISSION_FAILURE, $tokenData);
                }
                break;
            case 'feedback':
                $feedbackController = new FeedbackController();
                if (!$feedbackController->feedback($_POST['feedback'], $_POST['email'])) {
                    return $this->error(self::SUBMISSION_FAILURE, $tokenData);
                }
                break;
            default:
                return $this->error(self::UNRECOGNISED_COMMAND, $tokenData);
                break;
        }
        $this->setResponseCode(200);
        return $this->success($this->responseData, 200);
    }

    /**
     * Success Method
     * @param array $data
     * @param int $code
     * @return false|string
     *
     * Sets the response code and then encodes all the data accumulated so far as JSON for printing.
     */
    private function success(array $data, int $code = 200)
    {
        $this->setResponseCode($code);
        return json_encode($data);
    }

    /**
     * Error Method
     * @param String $errorCode
     * @param array|null $auth
     * @return String
     *
     * Sets the HTTP response headers according to the error, and returns a standard JSON response for errors.
     */
    private function error(String $errorCode, array $auth = null): String
    {
        $this->setResponseCode($errorCode);
        return json_encode(['status' => 'error', 'message' => $errorCode, 'auth' => $auth ?? null]);
    }

    /**
     * Set Response Code
     * @param $code
     *
     * A simple switch statement that, depending on the error constant used, sets the HTTP response.
     */
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
