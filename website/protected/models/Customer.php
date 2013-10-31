<?php

/**
 * Basic immutable customer description.
 * Can be used, passed everywhere on the
 * platform.
 * To change Customer's info you need his AuthentificatedCustomer instance,
 * @author eugene
 */
class Customer extends ModelObject {

    /*
     * Create customer instance for $user which have
     * friends $friends
     * @param user: User object
     * @param friends: array of Customer objects
     */
    function __construct($user, &$friends = array()) {
        parent::__construct();
        $this->dsChangeTracking();
        $this->setUser($user);        
        $this->setFriends($this->mkFriendsArrayCanonical($friends));
        $this->enChangeTracking();
    }

    /*
     * @param: array(Customer) of any form
     * @returns: array(Customer->getUser()->getId() => Customer)
     */
    private function &mkFriendsArrayCanonical(&$friends) {
        $canonicalArray = NULL;
        foreach ($friends as $userIx => $friend) {
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
        assert($user instanceof User);
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
    private function setFriends(&$newFriends) {
        assert(is_array($newFriends));
        $this->valueChanged(self::CH_FRIENDS, $this->friends, $newFriends);
        $this->friends =& $newFriends;
    }

    /*
     * @param friend: instance of Customer class
     * @returns: nothing
     */
    protected function addFriend(&$friend) {
        assert($friend instanceof Customer);
        assert($friend->getUser()->getId() != $this->getUser()->getId());
        $newFriends = $this->friends;
        $newFriends[$friend->getUser()->getId()] = $friend;
        $this->setFriends($newFriends);
    }

    /*
     * @param friend: Customer object to add as a friend
     * @returns: nothing
     */
    protected function delFriend(&$friend) {
        assert($friend instanceof Customer);
        $newFriends = $this->friends;
        unset($newFriends[$friend->getUser()->getId()]);
        $this->setFriends($newFriends);
    }

    //User object
    private $user;
    //Array of Customer objects
    private $friends;
    //ModelObject constants for changes supply
    const CH_FRIENDS = 1;
}