<?php

/**
 * Yii uses identity classes for user authentification
 * Basic class for HybridAuthIdentity and normal AuthIdentity
 * @author eugene
 */
class EGPIdentityBase extends CBaseUserIdentity {

    public function __construct() {
        $this->errorCode = self::ERROR_NONE;
    }

    public function authenticate() {
        if ($this->getId() != NULL)
            return TRUE;
        else return FALSE;
    }
    
    /**
    * Returns a value that uniquely represents the identity.
    * @return mixed a value that uniquely represents the identity (e.g. primary key value).
    */
    public function getId()
    {
           return $this->id;
    }

    /**
    * Returns the display name for the identity (e.g. username).
    * @return string the display name for the identity.
    */
    public function getName()
    {
           return $this->username;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
    public function setAuthUser(AuthentificatedUser $authUser) {
        $this->authUser = $authUser;
        $this->id = $this->authUser->getId();
        $this->username = $this->authUser->getDisplayName();
        $this->setState("authUser", $this->authUser);
    }
    
    private $id;
    private $username;
    private $authUser;
}
