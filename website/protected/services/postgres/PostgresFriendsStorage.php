<?php

/**
 * PostgreSQL implementation of IFriendsStorage interface.
 * Inherits from PostgresUserStorage to avoid creation of additional
 * connection.
 * TODO: substitute this inheritance with DI of IUserStorage.
 * @author eugene
 */
class PostgresFriendsStorage extends PostgresUserStorage implements IFriendsStorage {

    public function __construct() {   
        parent::__construct();
    }

    public function getUserFriends($reqId, Paging &$paging) {
        TU::throwIfNot(is_numeric($paging->getOffset()), TU::INVALID_ARGUMENT_EXCEPTION,
            "Only numeric paging.offset is supported for this service");
        $friends = array();

        $result = pg_query_params($this->conn, self::$SQL_SELECT_FRIENDS,
            array($reqId, $paging->getOffset(), $paging->getLimit()));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        while(($data = pg_fetch_object($result)) != FALSE) {
            $friends[] = $this->getUserById($data->acceptor);
        }

        $result = pg_query_params($this->conn, self::$SQL_SELECT_ALL_FRIENDS_COUNT,
            array($reqId));
        TU::throwIf($result === FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
        $data = pg_fetch_object($result);
        TU::throwIf($data === FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
        $paging->setTotal(intval($data->cnt));
        return $friends;
    }

    public function delFriend($reqId, $accId) {
        $result = pg_query_params($this->conn, self::$SQL_DELETE_FRIEND,
                                         array($reqId, $accId));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
    }

    public function addFriend($reqId, $accId) {
        $result = pg_query_params($this->conn, self::$SQL_INSERT_FRIEND,
                                         array($reqId, $accId));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
    }

    public function hasFriend($reqId, $accId) {
        $result = pg_query_params($this->conn, self::$SQL_FRIEND_EXISTS,
                                    array($reqId, $accId));
        TU::throwIf($result === FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
        $data = pg_fetch_object($result);
        TU::throwIf($data === FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
        return PostgresUtils::PGBoolToPHP($data->result);
    }

    private static $SQL_SELECT_FRIENDS =
        "SELECT acceptor
         FROM egp.friendnships
         WHERE requestor = $1
         OFFSET $2
         LIMIT $3;";
    private static $SQL_FRIEND_EXISTS =
         "SELECT EXISTS(SELECT *
          FROM egp.friendnships
          WHERE requestor = $1 AND acceptor = $2) AS result;";
    private static $SQL_SELECT_ALL_FRIENDS_COUNT =
        "SELECT count(*) AS cnt
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
