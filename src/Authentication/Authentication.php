<?php
namespace csm2020\PatientApp\Authentication;

use Firebase\JWT\JWT;

use Exception;
use PDO;

use csm2020\PatientApp\Config\Config;
use csm2020\PatientApp\Database\Database;

/**
 * Class Authentication
 * @package csm2020\PatientApp\Authentication
 * @author Oliver Earl <ole4@aber.ac.uk>
 *
 */
class Authentication
{
    /**
     * JSON that is re-used elsewhere in the class
     */
    const UNAUTHORISED_JSON =   ['status' => 'error', 'msg' => 'Unauthorised'];

    /**
     * @var
     */
    private $secretKey;
    /**
     * @var
     */
    private $algorithm;
    /**
     * @var
     */
    private $serverName;

    /**
     * Authentication constructor.
     */
    public function __construct()
    {
        $config = Config::getConfig();

        $this->secretKey =  $config['auth-secret-key'];
        $this->algorithm =  $config['auth-algorithm'];
        $this->serverName = $config['server-url'];
    }

    /**
     *
     */
    private function __clone() {}

    /**
     * Login Method
     * @return array|null
     * @throws Exception
     * Helper method that passes the login details contained within POST to the main authentication method. Returns
     * null if there's nothing inside the POST data.
     */
    public function login()
    {
        if (!isset($_POST['username']) || !isset($_POST['current-password'])) {
            return null;
        }
        return $this->authenticate($_POST['username'], $_POST['current-password']);
    }

    // http://phpclicks.com/php-token-based-authentication/

    /**
     * Authenticate Method
     * @param String $username
     * @param String $password
     * @return array|null
     * @throws Exception
     *
     * Carries out the main authentication legwork, including invoking the JWT library. After cleaning incoming login
     * data, it retrieves the user from the database that matches the login information. From this information, a
     * token is generated. This is included in a positive JSON response to the client that is extremely important as
     * it will be used to authenticate all communication between front and back ends until the token expires, which
     * is hardcoded as two hours. If anything goes wrong, it will return null - otherwise, an array of information,
     * including the token subarray, ready to be translated into JSON.
     *
     * TODO: Refactoring to include User class
     * This routine was written early into the backend's development, and directly accesses the database when it
     * really should bring in the User class more like some of the other methods in this class do.
     *
     * TODO: Plaintext passwords
     * Goes without saying. Hash passwords.
     */
    public function authenticate(String $username, String $password)
    {
        $sanitisedUsername = Database::sanitise($username);
        $sanitisedPassword = Database::sanitise($password);

        // TODO: Hashing really should be included at some point. Plaintext passwords are not good.
        // Alas, this can't be brought in until the Java client has it implemented too.
        $db = Database::getDatabase();
        $statement = $db->prepare(
"SELECT * FROM user WHERE username = :username AND userPassword = :password AND userType = 'patient' LIMIT 1");
        $statement->bindParam('username', $sanitisedUsername);
        $statement->bindParam('password', $sanitisedPassword);
        $statement->execute();
        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) > 0) {
            $token =                base64_encode(random_bytes(32));
            $issueTime =            time();
            $noExpirationBefore =   $issueTime + 10;
            $expireTime =           $noExpirationBefore + 7200;
            $serverName =           $this->serverName;

            $tokenData = [
                'iat' => $issueTime,
                'jti' => $token,
                'iss' => $serverName,
                'nbf' => $noExpirationBefore,
                'exp' => $expireTime,
                'data' => [
                    'id' => $row[0]['userId'],
                    'name' => $row[0]['username']
                ]
            ];
            $secretKey = base64_decode($this->secretKey);
            $jwt = JWT::encode(
                $tokenData,
                $secretKey,
                $this->algorithm
            );

            $jsonArray = ['status' => 'success', 'response' => ['jwt' => $jwt]];
            return $jsonArray;
        }
        return null;
    }

    /**
     * Token Authenticate Method
     * @param String $token
     * @return array|null
     *
     * The next major piece of the puzzle in authentication is being able to authenticate based on the token provided
     * when it is returned to the backend by responses sent from the frontend, which are normally stored as a cookie,
     * unless this changes at any point.
     *
     * This method really relies on the magic behind the JWT library to handle the authentication. Why re-invent the
     * wheel and all that. Should authentication fails, an exception is thrown, causing the method to return null and
     * ultimately the application will return a 403 Forbidden.
     */
    public function tokenAuthenticate(String $token)
    {
        try {
            $secretKey = base64_decode($this->secretKey);
            $decodedData = JWT::decode($token, $secretKey, [$this->algorithm]);
            return ['status' => 'success', 'data' => $decodedData];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get ID Method
     * @param String $token
     * @return string|null
     *
     * Extracts the User ID (uid) from a token in order to be used by other areas of the program. If a user ID for
     * whatever reason can't be found in the token, it returns null, triggering an error down the line.
     *
     * TODO: Refactor using User class
     * This was written early in the program's develop, and really should be tied properly into User objects.
     */
    public function getId(String $token)
    {
        try {
            $secretKey = base64_decode($this->secretKey);
            $decodedData = JWT::decode($token, $secretKey, [$this->algorithm]);

            return $decodedData->data->id;
            } catch (Exception $e) {
            return null;
        }
    }
}
