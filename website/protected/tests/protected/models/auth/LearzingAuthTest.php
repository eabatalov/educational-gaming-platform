<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-12-08 at 16:51:25.
 */
class LearzingAuthTest extends PHPUnit_Framework_TestCase {

    /**
     * @var LearzingAuth
     */
    protected $auth;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->auth = new LearzingAuth();
    }

    const USER_EMAIL = "9_Email@example.com";
    const USER_PASSWORD = "Password_9";
    const CLIENT_GLOBAL_ID = "LearzingTestingAPIClient123456";
    const CLIENT_ID = "1";

    private static $token;
    /**
     * @covers LearzingAuth::getAccessToken
     */
    public function testGetAccessToken() {
        try {
            //valid flow
            self::$token = $this->auth->getAccessToken(
                self::USER_EMAIL, self::USER_PASSWORD,
                self::CLIENT_GLOBAL_ID);
            //invalid user creds
            try {
                $this->auth->getAccessToken(
                    self::USER_EMAIL, self::USER_PASSWORD . "foo",
                    self::CLIENT_GLOBAL_ID);
                assert(FALSE);
            } catch (InvalidArgumentException $ex) {
                assert(TRUE);
            }

            try {
                //invalid client id
                $this->auth->getAccessToken(
                    self::USER_EMAIL, self::USER_PASSWORD,
                    "");
                assert(FALSE);
            } catch (InvalidArgumentException $ex) {
                assert(TRUE);
            }
        }  catch (Exception $ex) {
                throw $ex;
        }
    }

    /**
     * @covers LearzingAuth::destroyAccessToken
     */
    public function testDestroyAccessToken() {
        try {
            assert(self::$token != NULL);
            //invalid client_id
            try {
                $this->auth->destroyAccessToken(self::$token ->access_token, self::CLIENT_GLOBAL_ID . "foo");
                assert(FALSE);
            } catch(InvalidArgumentException $ex) {}
            //valid path
            $this->auth->destroyAccessToken(self::$token ->access_token, self::CLIENT_GLOBAL_ID);
            //double destroy
            try {
                $this->auth->destroyAccessToken(self::$token ->access_token, self::CLIENT_GLOBAL_ID);
            } catch(InvalidArgumentException $ex) {}
            //another invalid token
            try {
                $this->auth->destroyAccessToken("foo", self::CLIENT_GLOBAL_ID);
                assert(FALSE);
            } catch(InvalidArgumentException $ex) {}
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @covers LearzingAuth::authenticateRequest
     */
    public function testAuthenticateRequest() {
        //set self::$token
        $this->testGetAccessToken();
        try {
            //valid path
            $_SERVER['HTTP_AUTHORIZATION'] =
                AccessTokenInfoApiModel::TOKEN_TYPE_BEARER . " " . self::$token->access_token;
            assert($this->auth->authenticateRequest());
            //invalid token
            $_SERVER['HTTP_AUTHORIZATION'] =
                AccessTokenInfoApiModel::TOKEN_TYPE_BEARER . " 1eadasdasfergt344regetgeg";
            assert(!$this->auth->authenticateRequest());
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}