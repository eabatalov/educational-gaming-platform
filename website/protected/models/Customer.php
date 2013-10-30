<?php

/**
 * Basic immutable customer description.
 * Can be used, passed everywhere on the
 * platform.
 * To change Customer's info you need his AuthentificatedCustomer instance,
 * @author eugene
 */
class Customer {

    function __construct($user, $friends) {
        $this->setUser($user);
        $this->setFriends($friends);
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
     * get immutable array of customer's friends
     * @returns: array(Cistomer)
     */
    public function getFriends() {
        //TODO return immutable iterator here
        return $this->friends;
    }

    /*
     * @param friends: array of Customer objects which are friends
     * @returns: nothing
     */
    protected function setFriends($friends) {
        $this->friends = array();
        foreach ($friends as $friend)
            $this->addFriend($friend);
    }

    /*
     * @param friend: instance of Customer class
     */
    protected function addFriend($friend) {
        assert($friend instanceof Customer);
        assert($friend->getUser()->getId() != $this->getUser()->getId());
        $this->friends[$friend->getUser()->getId()] = $friend;
    }

    /*
     * @param friend: Customer object to add as a friend
     * @returns: nothing
     */
    protected function delFriend($friend) {
        assert($friend instanceof Customer);
        unset($this->friends[$friend->getUser()->getId()]);
        
    }
    //User object
    private $user;
    //Array of Customer objects
    private $friends;
}