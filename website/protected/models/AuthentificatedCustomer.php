<?php

require_once 'Customer.php';
/**
 * This customer was authorized on server and is current customer,
 * Beeing him we are able to change his data.
 * @author eugene
 */
class AuthentificatedCustomer extends Customer {

    /*
     * Fabric method. Use as constructor.
     */
    public static function createInstance($user, &$friends = array()) {
        return new AuthentificatedCustomer(FALSE, $user, $friends);
    }

    /*
     * Overrides corresponding ModelObject method
     */
    public static function createEmpty() {
        return new AuthentificatedCustomer(TRUE);
    }

    public function addFriend($friend) {
        parent::addFriend($friend);
    }

    public function delFriend($friend) {
        parent::delFriend($friend);
    }

    //public just because we can't hide constructor if it was public in one of parent classes
    public function __construct($mkEmpty, $user = NULL, $friends = NULL) {
        assert(is_bool($mkEmpty));
        if (!$mkEmpty) {
            assert($user instanceof AuthentificatedUser);
            parent::__construct(FALSE, $user, $friends);
        }
    }
}