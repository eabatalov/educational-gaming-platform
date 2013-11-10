<?php

/**
 * Yii user identity implementation for hybrid authentification
 * Can authentificate user using (Auth provider name, auth provider identifier)
 *  pair and using email which we've got from auth provider
 * @author eugene
 */
class HybridAuthIdentity extends EGPIdentityBase {
    /**
     * @param string the provider you are using
     * @param Hybrid_Auth an instance of Hybrid_Auth 
     */
    public function __construct($provider, Hybrid_Auth $hybridAuth) {
        parent::__construct();
        assert(is_string($provider));
        $this->loginProvider = $provider;
        $this->hybridAuth = $hybridAuth;
        $this->hauthStorage = new PostgresHybridAuthStorage();
    }

    public function __destruct() {
        /*
         * Remove all the hybrid auth data which we got from provider from session.
         * It is not needed now.
         */
        $this->hybridAuth->logoutAllProviders();
    }

    /**
     * Authenticates a user.
     * @return boolean whether authentication succeeds.
     */
    public function authenticate() {
        if (parent::authenticate())
            return TRUE;

        if (strtolower($this->loginProvider) == 'openid') {
                if (!isset($_GET['openid-identity'])) {
                        throw new Exception('You chose OpenID but didn\'t provide an OpenID identifier');
                } else {
                        $params = array( "openid_identifier" => $_GET['openid-identity']);
                }
        } else {
                $params = array();
        }

        $adapter = $this->hybridAuth->authenticate($this->loginProvider,$params);
        //This line was in original module. Don't know why it is needed.
        //if ($adapter->isUserConnected()) {
                $this->adapter = $adapter;
                $this->loginProviderIdentifier = $this->adapter->getUserProfile()->identifier;
                $this->email = $this->adapter->getUserProfile()->email;

                $this->fillAuthUserFromStorage();
                return $this->errorCode == self::ERROR_NONE;
        //}
    }

    private function fillAuthUserFromStorage() {
        try {
            $this->setAuthUser($this->hauthStorage->getAuthentificatedUser(
                $this->loginProvider, $this->loginProviderIdentifier, $this->email));
        } catch (Exception $ex) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
            return;
        }
    }

    /**
     * Returns the Adapter provided by Hybrid_Auth.  See http://hybridauth.sourceforge.net
     * for details on how to use this
     * @return Hybrid_Provider_Adapter adapter
     */
    public function getAdapter() {
        return $this->adapter;
    }

    public function getLoginProvider() {
        return $this->loginProvider;
    }

    public function getLoginProviderIdentifier() {
        return $this->loginProviderIdentifier;
    }

    private $loginProvider;
    private $loginProviderIdentifier;
    private $email;
    private $adapter;
    private $hybridAuth;
    private $hauthStorage;
}