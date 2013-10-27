<?php

namespace model;

/**
 * Basic immutable customer description.
 * Can be used, passed everywhere on the
 * platform.
 * To change user's info you need his AuthentificatedUser instance,
 * @author eugene
 */
class Customer {

    function __construct($user, $friends) {
        $this->user = $user;
        $this->friends = $friends;
    }

    /*
     * @returns: customer's User instance
     */
    public function getUser() {
        return $this->user;
    }

    /*
     * get immutable array of customer's friends
     * @returns: array(Cistomer)
     */
    public function getFriends() {
        return $this->friends;
    }
 
    private $user;
    private $friends;
}
