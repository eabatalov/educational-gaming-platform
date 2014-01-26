<?php

/**
 * Class which provides access to persistent storage of friends information.
 * Friends information is stored in form of connections from user to user.
 * "addFriend" means add connection in one direction.
 * @author eugene
 */
interface IFriendsStorage {
    public function getUserFriends($reqId, Paging $paging);
    public function delFriend($reqId, $accId);
    public function addFriend($reqId, $accId);
    public function hasFriend($reqId, $accId);
}
