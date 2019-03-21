<?php
namespace csm2020\PatientApp\Authentication;

use Firebase\JWT\JWT;

use PDO;

use csm2020\PatientApp\Database\Database;

class Authentication
{
    const SECRET_KEY =          '2@y$e^4efeqjqb2f+8xp&#yp4%4ku@e+^qcx_x0gz4id6t1jb&'; // predetermined base64 key
    const ALGORITHM =           'HS512';
    const SERVER_NAME =         'http://users.aber.ac.uk/ole4/';
    const UNAUTHORISED_JSON =   ['status' => 'error', 'msg' => 'Unauthorised'];

    private function __construct() {}
    private function __clone() {}

    public static function login()
    {
        if (!isset($_POST['username']) || !isset($_POST['password'])) {
            return json_encode(self::UNAUTHORISED_JSON);
        }
        return self::authenticate($_POST['username'], $_POST['password']);
    }

    // http://phpclicks.com/php-token-based-authentication/
    private static function authenticate($username, $password)
    {
        //$username = 'test';
        //$password = 'test';

        $sanitisedUsername = trim(stripslashes(htmlspecialchars(strip_tags($username))));
        $sanitisedPassword = trim(stripslashes(htmlspecialchars(strip_tags($password))));

        // Hashing really should be included at some point. Plaintext passwords are not good.
        // Alas, this can't be brought in until the Java client has it implemented too.
        $db = Database::getDatabase();
        $statement = $db->prepare(
            "SELECT * FROM user WHERE username = :username AND userPassword = :password LIMIT 1");
        $statement->bindParam('username', $sanitisedUsername);
        $statement->bindParam('password', $sanitisedPassword);
        $statement->execute();
        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) > 0) {
            $token =                base64_encode(random_bytes(32));
            $issueTime =            time();
            $noExpirationBefore =   $issueTime + 10;
            $expireTime =           $noExpirationBefore + 7200;
            $serverName =           self::SERVER_NAME;

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
            $secretKey = base64_decode(self::SECRET_KEY);
            $jwt = JWT::encode(
                $tokenData,
                $secretKey,
                self::ALGORITHM
            );

            $jsonArray = ['status' => 'success', 'resp' => ['jwt' => $jwt]];
            return json_encode($jsonArray);
        }
        return json_encode(self::UNAUTHORISED_JSON);
    }

    public static function tokenAuthenticate($token)
    {
        try {
            $secretKey = base64_decode(self::SECRET_KEY);
            $decodedData = JWT::decode($token, $secretKey, [self::ALGORITHM]);
            return json_encode(['status' => 'success', 'data' => $decodedData]);
        } catch (Exception $e) {
            return json_encode(self::UNAUTHORISED_JSON);
        }
    }
}
