<?php
namespace csm2020\PatientApp\Authentication;

use Firebase\JWT\JWT;

use Exception;
use PDO;

use csm2020\PatientApp\Config\Config;
use csm2020\PatientApp\Database\Database;

class Authentication
{
    const UNAUTHORISED_JSON =   ['status' => 'error', 'msg' => 'Unauthorised'];

    private $secretKey;
    private $algorithm;
    private $serverName;

    public function __construct()
    {
        $config = Config::getConfig();

        $this->secretKey =  $config['auth-secret-key'];
        $this->algorithm =  $config['auth-algorithm'];
        $this->serverName = $config['server-url'];
    }
    private function __clone() {}

    public function login()
    {
        if (!isset($_POST['username']) || !isset($_POST['password'])) {
            return json_encode(self::UNAUTHORISED_JSON);
        }
        return $this->authenticate($_POST['username'], $_POST['password']);
    }

    // http://phpclicks.com/php-token-based-authentication/
    private function authenticate($username, $password)
    {
        //$username = 'test';
        //$password = 'test';

        $sanitisedUsername = trim(stripslashes(htmlspecialchars(strip_tags($username))));
        $sanitisedPassword = trim(stripslashes(htmlspecialchars(strip_tags($password))));

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

            $jsonArray = ['status' => 'success', 'resp' => ['jwt' => $jwt]];
            return json_encode($jsonArray);
        }
        return json_encode(self::UNAUTHORISED_JSON);
    }

    public function tokenAuthenticate($token)
    {
        try {
            $secretKey = base64_decode($this->secretKey);
            $decodedData = JWT::decode($token, $secretKey, [$this->algorithm]);
            return json_encode(['status' => 'success', 'data' => $decodedData]);
        } catch (Exception $e) {
            return json_encode(self::UNAUTHORISED_JSON);
        }
    }
}
