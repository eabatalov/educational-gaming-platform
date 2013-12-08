<?php

/**
 * Description of AccessTokenInfo
 *
 * @author eugene
 */
class AccessTokenInfo {

    function __construct($accessToken, $userId, $clientId) {
        $this->accessToken = $accessToken;
        //$this->refreshToken = $refreshToken;
        //$this->expiresIn = $expiresIn;
        $this->userId = $userId;
        $this->clientId = $clientId;
    }

    const EXPIRES_IN = "3600"; //in secs, 1 hour
    //char(30)
    public $accessToken;
    //char(30)
    //public $refreshToken;
    //Int, seconds
    //public $expiresIn;
    //int8
    public $userId;
    //int
    public $clientId;
}