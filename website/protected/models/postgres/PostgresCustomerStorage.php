<?php

/**
 * PostgreSQL implementation of ICustomerStorage interface.
 * Need to install PostgreSQL php lib to work
 * On ubuntu: "sudo apt-get install php5-pgsql"
 * @author eugene
 */
class PostgresCustomerStorage extends PostgresUserStorage implements ICustomerStorage {

    public function __construct() {   
        parent::__construct();
    }

    public function addCustomerAndUser(Customer $customer, $password) {
        assert($customer->getUser() instanceof User);
        assert($customer->getUser()->getRole() == UserRole::CUSTOMER);
        TU::throwIfNot($customer->validate() && $customer->getUser()->validate(),
            TU::INVALID_ARGUMENT_EXCEPTION, NULL, ModelObject::ERROR_INVALID_OBJECT);

        $this->addUser($customer->getUser(), $password);
        $authCustomer =
            $this->getAuthCustomer($customer->getUser()->getEmail(), $password);
        foreach ($customer->getFriends() as $friendId => $friend) {
            $authCustomer->addFriend($friend);
        }
        $this->saveAuthCustomer($authCustomer);
    }

    public function getCustomer($email) {
        assert(is_string($email));
        $user = $this->getUser($email);
        $friends = array();

        $result = pg_query_params($this->conn, self::$SQL_SELECT_FRIENDS,
                array($user->getId()));
        TU::throwIf($result == FALSE, TU::STORAGE_EXCEPTION, pg_last_error());

        while(($data = pg_fetch_object($result)) != FALSE) {
            $friends[$data->acceptor] = $this->getCustomerWithNoFriendsById($data->acceptor);
        }

        return Customer::createCInstance($user, $friends);
    }

    public function getAuthCustomer($email, $password) {
        assert(is_string($email));
        assert(is_string($password));
        $authUser = $this->getAuthentificatedUser($email, $password);
        $friends = array();

        $result = pg_query_params($this->conn, self::$SQL_SELECT_FRIENDS,
                array($authUser->getId()));
        TU::throwIf($result == FALSE, TU::STORAGE_EXCEPTION, pg_last_error());

        while(($data = pg_fetch_object($result)) != FALSE) {
            $friends[$data->acceptor] = $this->getCustomerWithNoFriendsById($data->acceptor);
        }

        return AuthentificatedCustomer::createACInstance($authUser, $friends);
    }

    public function searchCustomers($query, $matchType = NULL) {
        assert(is_string($query));
        $users = $this->searchUsers($query);
        $result = array();
        foreach ($users as $user) {
            $result[$user->getId()] = $this->getCustomer($user->getEmail());
        }
        return $result;
    }

    public function saveAuthCustomer(AuthentificatedCustomer $authCustomer) {
        TU::throwIfNot($authCustomer->validate(), TU::INVALID_ARGUMENT_EXCEPTION,
            NULL, ModelObject::ERROR_INVALID_OBJECT);
        if (!array_key_exists(AuthentificatedCustomer::CH_FRIENDS,
            $authCustomer->getValueChanges())) {
            return;
        }

        $friendsChanges =
            $authCustomer->getValueChanges()[AuthentificatedCustomer::CH_FRIENDS];
        foreach ($friendsChanges->getNewVal() as $friendId => $friend) {
            if (!array_key_exists($friendId, $friendsChanges->getOldVal())) {
                $this->addFriend($authCustomer->getUser()->getId(), $friendId);
            }
        }
        foreach ($friendsChanges->getOldVal() as $friendId => $friend) {
            if (!array_key_exists($friendId, $friendsChanges->getNewVal())) {
                $this->delFriend($authCustomer->getUser()->getId(), $friendId);
            }
        }
    }

    protected function delFriend($reqId, $accId) {
        $result = pg_query_params($this->conn, self::$SQL_DELETE_FRIEND,
                                         array($reqId, $accId));
        TU::throwIf($result == FALSE, TU::STORAGE_EXCEPTION, pg_last_error());
    }

    protected function addFriend($reqId, $accId) {
        $result = pg_query_params($this->conn, self::$SQL_INSERT_FRIEND,
                                         array($reqId, $accId));
        TU::throwIf($result == FALSE, TU::STORAGE_EXCEPTION, pg_last_error());
    }

    protected function getCustomerWithNoFriendsById($id) {
        assert(is_numeric($id));
        return Customer::createCInstance($this->getUserById($id));
    }

    private static $SQL_SELECT_FRIENDS =
        "SELECT acceptor
         FROM egp.friendnships
         WHERE requestor = $1;";
    private static $SQL_INSERT_FRIEND =
        "INSERT INTO egp.friendnships(
        requestor, acceptor)
        VALUES ($1, $2);";
    private static $SQL_DELETE_FRIEND =
        "DELETE FROM egp.friendnships
        WHERE requestor = $1 AND acceptor = $2";
}
