<?php

/**
 * Description of HybridAuthRecord
 *
 * @author eugene
 */
class HybridAuthRecord {

    function __construct($userId, $loginProviderName, $loginProviderIdentifier) {
        $this->userId = $userId;
        $this->loginProviderName = $loginProviderName;
        $this->loginProviderIdentifier = $loginProviderIdentifier;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getLoginProviderName() {
        return $this->loginProviderName;
    }

    public function getLoginProviderIdentifier() {
        return $this->loginProviderIdentifier;
    }

    private $userId;
    private $loginProviderName;
    private $loginProviderIdentifier;
}
