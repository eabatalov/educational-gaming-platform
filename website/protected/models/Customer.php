<?php

/**
 * Basic immutable customer description.
 * Can be used, passed everywhere on the
 * platform.
 * To change Customer's info you need his AuthentificatedCustomer instance,
 * @author eugene
 */
class Customer extends ModelObject {

    function __construct($user, $friends) {
        parent::__construct();
        $this->user == NULL;
        $this->friends == NULL;
        $this->dsChangeTracking();
        $this->setUser($user);
        $this->setFriends($friends);
        $this->enChangeTracking();
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
        $this->valueChanged("userId",
                $this->getUser() != NULL ? $this->getUser()->getId() : $user->getId(),
                $user->getId());
        $this->user = $user;
    }

    /*
     * get immutable array of customer's friends
     * @returns: array(Customer)
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
        assert(is_array($friends));
        if ($this->friends != NULL) {
            foreach ($this->friends as $friend)
                $this->delFriend($friend);
        }
        foreach ($friends as $friend)
            $this->addFriend($friend);
    }

    /*
     * @param friend: instance of Customer class
     */
    protected function addFriend($friend) {
        assert($friend instanceof Customer);
        assert($friend->getUser()->getId() != $this->getUser()->getId());
        $this->valueChanged("friendId" . strval($friend->getUser()->getId()),
                ModelChangeRecord::REMOVED, ModelChangeRecord::ADDED,
                $friend->getUser()->getId());
        $this->friends[$friend->getUser()->getId()] = $friend;
    }

    /*
     * @param friend: Customer object to add as a friend
     * @returns: nothing
     */
    protected function delFriend($friend) {
        assert($friend instanceof Customer);
        $this->valueChanged("friendId" . strval($friend->getUser()->getId()),
                ModelChangeRecord::ADDED, ModelChangeRecord::REMOVED,
                $friend->getUser()->getId());
        unset($this->friends[$friend->getUser()->getId()]);
    }

    //User object
    private $user;
    //Array of Customer objects
    private $friends;
}