<?php

/**
 * Basic immutable customer description.
 * Can be used, passed everywhere on the
 * platform.
 * To change Customer's info you need his AuthentificatedCustomer instance,
 * @author eugene
 */
class Customer extends ModelObject {

    /* Fabric method.
     * Create customer instance for $user which have
     * friends $friends
     * @param user: User object
     * @param friends: array of Customer objects
     */
    public static function createCInstance($user, &$friends = array()) {
        return new Customer(FALSE, $user, $friends);
    }

    /*
     * Overrides corresponding ModelObject method
     */
    public static function createEmpty() {
        return new Customer(TRUE);
    }

    /*
     * @returns: customer's User instance
     */
    public function getUser() {
        return $this->user;
    }

    /*
     * @param user: instance of User class this customer is
     * @returns: nothing
     */
    private function setUser($user) {
        TU::throwIfNot($user instanceof User, TU::INVALID_ARGUMENT_EXCEPTION,
                "user must be instance of User");
        $this->user = $user;
    }

    /*
     * get copy of array of customer's friends
     * @returns: array(userID => Customer)
     */
    public function getFriends() {
        return $this->friends;
    }

    /*
     * @param friends: array of Customer objects which are friends
     * @returns: nothing
     */
    private function setFriends(&$friends) {
        TU::throwIfNot(is_array($friends), TU::INVALID_ARGUMENT_EXCEPTION,
                "friends should be array");

        $this->valueChanged(self::CH_FRIENDS, $this->friends, $friends);
        $this->friends =& $friends;
    }

    /*
     * @param friend: instance of Customer class
     * @returns: nothing
     */
    protected function addFriend(&$friend) {
        $this->checkFriend($friend);
        $friendId = $friend->getUser()->getId();
        TU::throwIf(array_key_exists($friendId, $this->friends),
            TU::INVALID_ARGUMENT_EXCEPTION, "double addition of friend");

        $newFriends = $this->friends;
        $newFriends[$friendId] = $friend;
        $this->setFriends($newFriends);
    }

    /*
     * @param friend: Customer object to add as a friend
     * @returns: nothing
     */
    protected function delFriend(&$friend) {
        $this->checkFriend($friend);
        $friendId = $friend->getUser()->getId();
        TU::throwIf(!array_key_exists($friendId, $this->friends),
            TU::INVALID_ARGUMENT_EXCEPTION,
            "deletion of not existed friend with id " . (string)$friendId);

        $newFriends = $this->friends;
        unset($newFriends[$friend->getUser()->getId()]);
        $this->setFriends($newFriends);
    }

    public function rules() {
        /*
         * No user input validation rules for now.
         */
        return parent::rules();
    }

    /* 
     * performs necessary checks on friend passed from outside
     * @param friend: instance of Customer to check
     * @return nothing
     * @throws InvalidArgumentException if check has failed
     */
    protected function checkFriend(&$friend) {
        TU::throwIfNot($friend instanceof Customer, TU::INVALID_ARGUMENT_EXCEPTION,
                "friend should be instance of Customer");
        TU::throwIfNot($friend->getUser()->getRole() == UserRole::CUSTOMER,
                TU::INVALID_ARGUMENT_EXCEPTION, "friend's role should be CUSTOMER");
        TU::throwIfNot($friend->getUser()->getId() != $this->getUser()->getId(),
                TU::INVALID_ARGUMENT_EXCEPTION, "Can't add myself to friends");
    }

    //public just because we can't hide constructor if it was public in one of parent classes
    public function __construct($mkEmpty, $user = NULL, &$friends = NULL) {
        assert(is_bool($mkEmpty));
        if (!$mkEmpty) {
            parent::__construct();
            $this->dsChangeTracking();
            $this->setUser($user);        
            $this->setFriends($this->mkFriendsArrayCanonical($friends));
            $this->enChangeTracking();
        }
    }

    /*
     * @param: array(Customer) of any form
     * @returns: array(Customer->getUser()->getId() => Customer)
     */
    private function &mkFriendsArrayCanonical(&$friends) {
        TU::throwIfNot(is_array($friends), TU::INVALID_ARGUMENT_EXCEPTION,
                "friends should be array()");

        $canonicalArray = NULL;
        foreach ($friends as $userIx => $friend) {
            $this->checkFriend($friend);
            if ($userIx != $friend->getUser()->getId()) {
                if ($canonicalArray == NULL) {
                    $canonicalArray = array();
                }
                $canonicalArray[$friend->getUser()->getId()] = $friend;
            }
        }
        if ($canonicalArray == NULL) {
            return $friends;
        } else {
            return $canonicalArray;
        }
    }

    //User object
    private $user;
    //Array of Customer objects
    private $friends;
    //ModelObject constants for changes supply
    const CH_FRIENDS = 1;
}