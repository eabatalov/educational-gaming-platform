<?php

require_once 'Customer.php';
/**
 * Description of AuthentificatedCustomer
 * This customer was authorized on server and is current customer,
 * Beeing him we are able to change his data.
 * @author eugene
 */
class AuthentificatedCustomer extends Customer {

    function __construct($user, $friends) {
        assert($user instanceof AuthentificatedUser);
        parent::__construct($user, $friends);
    }

    public function addFriend($friend) {
        parent::addFriend($friend);
    }

    public function delFriend($friend) {
        parent::delFriend($friend);
    }
}
