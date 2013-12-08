<?php

/**
 * Responsible for supplying access_tokens to users.
 * Inspired by oauth2, http://habrahabr.ru/company/mailru/blog/115163/,
 * http://tools.ietf.org/html/rfc6749
 *
 * @author eugene
 */

class AuthController extends ApiController {
 
    function __construct() {
        $this->auth = new LearzingAuth();
    }

    /*
     * Used for authentication of client side only apps
     * Analog of response_type=token in oauth2 spec
     */
    public function actionGetAccessToken() {
        $this->requireNoAuthentification();
        try {
            TU::throwIfNot(isset($this->getRequest()["email"]), TU::INVALID_ARGUMENT_EXCEPTION,
                "REQUEST field 'email' should be passed", self::ERROR_ABSENT_ARGUMENT);
            TU::throwIfNot(isset($this->getRequest()["password"]), TU::INVALID_ARGUMENT_EXCEPTION,
                "REQUEST field 'password' should be passed", self::ERROR_ABSENT_ARGUMENT);
            TU::throwIfNot(isset($this->getRequest()["client_id"]), TU::INVALID_ARGUMENT_EXCEPTION,
                "REQUEST field 'client_id' should be passed", self::ERROR_ABSENT_ARGUMENT);

            $email = $this->getRequest()["email"];
            $password = $this->getRequest()["password"];
            $client_id = $this->getRequest()["client_id"];

            $apiTokenInfo = $this->auth->getAccessToken($email, $password, $client_id);

            $this->addHttpHeaderToResponse("Cache-Control: no-store");
            $this->addHttpHeaderToResponse("Pragma: no-cache");
            $this->sendResponse(self::RESULT_SUCCESS, NULL, "token_info",
                $apiTokenInfo->toArray($this->getFields()));

        } catch (InvalidArgumentException $ex) {
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $ex->getMessage());
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    public function actionDestroyAccessToken() {
        $this->requireAuthentification();
        try {
            TU::throwIfNot(isset($this->getRequest()["client_id"]), TU::INVALID_ARGUMENT_EXCEPTION,
                "REQUEST field 'client_id' should be passed", self::ERROR_ABSENT_ARGUMENT);
            TU::throwIfNot(isset($this->getRequest()["access_token"]), TU::INVALID_ARGUMENT_EXCEPTION,
                "REQUEST field 'access_token' should be passed", self::ERROR_ABSENT_ARGUMENT);

            $clientId = $this->getRequest()["client_id"];
            $accessToken = $this->getRequest()["access_token"];

            $this->auth->destroyAccessToken($accessToken, $clientId);

            $this->sendResponse(self::RESULT_SUCCESS);
        }  catch (InvalidArgumentException $ex) {
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $ex->getMessage());
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    private $auth;
    const ERROR_ABSENT_ARGUMENT = 0x30000030;
}