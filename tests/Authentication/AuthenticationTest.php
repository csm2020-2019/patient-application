<?php
namespace csm2020\PatientApp\Tests;

use csm2020\PatientApp\Authentication\Authentication;
use PHPUnit_Framework_TestCase;

class AuthenticationTest extends PHPUnit_Framework_TestCase
{
    public function testAuthenticate()
    {
        $username = 'wibble';
        $password = 'something';

        $auth = new Authentication();
        $response = $auth->authenticate($username, $password);

        $this->assertNotNull($response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('response', $response);
    }

    public function testTokenAuthenticate()
    {
        $username = 'wibble';
        $password = 'something';

        $auth = new Authentication();
        $response = $auth->authenticate($username, $password);
        $token = $response['response']['jwt'];
        $tokenAuth = $auth->tokenAuthenticate($token);

        $this->assertNotNull($tokenAuth);
        $this->assertArrayHasKey('status', $tokenAuth);
        $this->assertArrayHasKey('data', $tokenAuth);
    }

    public function testGetId()
    {
        $username = 'wibble';
        $password = 'something';

        $auth = new Authentication();
        $response = $auth->authenticate($username, $password);
        $token = $response['response']['jwt'];
        $tokenAuth = $auth->tokenAuthenticate($token);

        $this->assertNotNull($tokenAuth);
    }
}
