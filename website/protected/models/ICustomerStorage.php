<?php

namespace model;

/**
 * Description of ICustomerStorage
 *
 * @author eugene
 */
class ICustomerStorage {
        /*
     * @param email: user's email
     * @param password: user's password
     * @returns: 
     */
    public function getFriends($email, $password);
    //req - requestor
    //acc - acceptor
    public function addFriend($reqEmail, $reqPassword, $accEmail);
    public function removeFriend($reqEmail, $reqPassword, $accEmail);
    public function searchUser($query, $matchType);
    //TODO
}
