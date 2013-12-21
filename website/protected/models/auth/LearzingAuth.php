<?php
/**
 * Handles all the Learzing auth requests
 *
 * @author eugene
 */
class LearzingAuth {

    function __construct() {
        $this->conn = pg_connect(PostgresUtils::getConnString(), PGSQL_CONNECT_FORCE_NEW);
        TU::throwIf($this->conn == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error(),
                InternalErrorException::ERROR_CONNECTION_PROBLEMS);
    }

    /*
     * @returns string which represents access token of currently authenticated user.
     *   Returns null if current user is not authenticated.
     */
    public static function getCurrentAccessToken() {
        return self::$currentAccessTokenInfo == NULL ? NULL :
            self::$currentAccessTokenInfo->accessToken;
    }

    //AccessTokenInfo
    private static $currentAccessTokenInfo;
    const AUTH_COOKIE_KEY = "LEARZING_API_TOKEN";
    const AUTH_HTTP_HEADER_KEY = "HTTP_AUTHORIZATION";

    /*
     * User authentication is performed here and api access token is generated
     * @param string userLogin
     * @param string userPassword
     * @param string clientId
     * @returns AccessTokenApiModel instance which is used by client to authenticate
     * on each API request
     * @throws InvalidArgumentException if any of passed arguments don't match (invalid)
     * @throws InternalErrorException on server error
     */
    public function getAccessToken($userLogin, $userPassword, $clientId) {
        TU::throwIfNot(is_string($userLogin), TU::INVALID_ARGUMENT_EXCEPTION);
        TU::throwIfNot(is_string($userPassword), TU::INVALID_ARGUMENT_EXCEPTION);
        TU::throwIfNot(is_string($clientId), TU::INVALID_ARGUMENT_EXCEPTION);
        try {
            $userStorage = new PostgresUserStorage();
            $authUser =
                $userStorage->getAuthentificatedUser($userLogin, $userPassword);

            $this->verifyClientId($clientId);

            $apiToken = $this->genNewApiToken();
            $this->storageSaveAccessToken(new AccessTokenInfo(
                $apiToken->access_token,
                $authUser->getId(),
                $clientId));
            return $apiToken;

        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgumentException("Invalid email or password", $ex->getCode());
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage(), $ex->getCode());
        }
    }

    /* Make token invalid
     * @returns nothing
     * @throws InvalidArgumentException if accessToken or clientId are invalid
     * @throws InternalErrorException if server error occured
     */
    public function destroyAccessToken($accessToken, $clientId) {
        TU::throwIfNot(is_string($accessToken), TU::INVALID_ARGUMENT_EXCEPTION);
        TU::throwIfNot(is_string($clientId), TU::INVALID_ARGUMENT_EXCEPTION);
        try {
            $token = $this->storageGetAccessTokenInfo($accessToken);

            TU::throwIfNot($token->clientId == $clientId, TU::INVALID_ARGUMENT_EXCEPTION);

            $this->storageDestroyAccessToken($accessToken);
        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgumentException("Couldn't login", $ex->getCode());
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage(), $ex->getCode());
        }
    }

    /* Initializes getCurrentAccessToken() result to current user's access token
     * if user's access token is valid
     * @returns FALSE if user passed access token is invalid or absent, TRUE otherwise
     * @throws InternalErrorException on server error
     */
    public function authenticateRequest() {
        try {
            if (isset($_SERVER[self::AUTH_HTTP_HEADER_KEY])) {
                $accessToken = $this->cleanAccessTokenType($_SERVER[self::AUTH_HTTP_HEADER_KEY]);
            } else if (isset($_COOKIE[self::AUTH_COOKIE_KEY])) {
                $accessToken = $_COOKIE[self::AUTH_COOKIE_KEY];
            } else {
                throw new InvalidArgumentException(
                    "HTTP header " . self::AUTH_HTTP_HEADER_KEY . " or " .
                    "HTTP cookie " . self::AUTH_COOKIE_KEY .  PHP_EOL .
                    "with your access token should be passed");
            }

            $accessTokenInfo = $this->storageGetAccessTokenInfo($accessToken);
            //TU::throwIfNot($accessTokenInfo->expiresIn > 0, TU::INTERNAL_ERROR_EXCEPTION);

            self::$currentAccessTokenInfo = $accessTokenInfo;
            return TRUE;
        } catch (InvalidArgumentException $ex) {
            //throw new InvalidArgumentException("Couldn't login", $ex->getCode());
            return FALSE;
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage(), $ex->getCode());
        }
    }

    /* ========================== STORAGE PART ============================== */
    private $conn;
    private static $SQL_GET_API_CLENT_BY_ID =
        "SELECT id, global_id, name
         FROM egp.api_clients
         WHERE global_id=$1;";
    private static $SQL_GET_ACCESS_TOKEN_INFO_BY_TOKEN =
        "SELECT access_token, user_id,
            (SELECT global_id FROM egp.api_clients WHERE id=client_id) as client_id
         FROM egp.api_access_tokens
         WHERE access_token=$1;";
    private static $SQL_DELETE_ACCESS_TOKEN_BY_USER_LOGIN_AND_CLIENT =
        "DELETE FROM egp.api_access_tokens
         WHERE user_id=(SELECT id FROM egp.users WHERE email=$1) AND
            client_id=(SELECT id FROM egp.api_clients WHERE global_id=$2);";
    private static $SQL_DELETE_ACCESS_TOKEN_BY_TOKEN =
        "DELETE FROM egp.api_access_tokens
         WHERE access_token=$1;";
    private static $SQL_INSERT_ACCESS_TOKEN =
        "INSERT INTO egp.api_access_tokens
         (access_token, user_id, client_id)
         VALUES ($1, $2, (SELECT id FROM egp.api_clients WHERE global_id=$3));
        ";
    
    /*
     * @throws InvalidArgumentException if clientId is invalid
     */
    private function verifyClientId($clientId) {
        $result = pg_query_params($this->conn,
            self::$SQL_GET_API_CLENT_BY_ID, array($clientId));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        $data = pg_fetch_object($result);
        TU::throwIf($data == FALSE, TU::INVALID_ARGUMENT_EXCEPTION, "Invalid clientId");
    }

    private function storageGetAccessTokenInfo($accessToken) {
        $result = pg_query_params($this->conn,
            self::$SQL_GET_ACCESS_TOKEN_INFO_BY_TOKEN, array($accessToken));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        $data = pg_fetch_object($result);
        TU::throwIf($data == FALSE, TU::INVALID_ARGUMENT_EXCEPTION, "Invalid accessToken");
        $clientId = $data->client_id;
        $userId = $data->user_id;
        return new AccessTokenInfo($accessToken, $userId, $clientId);
    }

    private function storageDestroyAccessToken($accessToken) {
        $result = pg_query_params($this->conn,
            self::$SQL_DELETE_ACCESS_TOKEN_BY_TOKEN, array($accessToken));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
        //WARNING: suppressing errors here
        /*$data = pg_fetch_object($result);
        TU::throwIf($data == FALSE, TU::INVALID_ARGUMENT_EXCEPTION, "Invalid accessToken");*/
    }

    private function storageSaveAccessToken(AccessTokenInfo $accessTokenInfo) {
        $result = pg_query_params($this->conn,
            self::$SQL_INSERT_ACCESS_TOKEN,
            array($accessTokenInfo->accessToken, $accessTokenInfo->userId,
                $accessTokenInfo->clientId));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
    }

    private function genNewApiToken() {
        $token = new AccessTokenInfoApiModel();
        $token->token_type = AccessTokenInfoApiModel::TOKEN_TYPE_BEARER;
        $token->access_token = $this->genNewTokenVal();
        //$token->refresh_token = $this->genNewTokenVal();
        //$token->expires_in = "3600; //in secs, 1 hour by default
        return $token;
    }

    private function genNewTokenVal() {
        return substr(AuthUtils::genPassword() .
            AuthUtils::genPassword() .
            AuthUtils::genPassword(), 30);
    }

    private function cleanAccessTokenType($accessTokenWithType) {
        $prefix = AccessTokenInfoApiModel::TOKEN_TYPE_BEARER;
        $accessToken = trim($accessTokenWithType);
        if (substr($accessTokenWithType, 0, strlen($prefix)) == $prefix) {
            $accessToken = trim(substr($accessTokenWithType, strlen($prefix)));
        }
        return $accessToken;
    }
}