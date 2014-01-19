<?php

/**
 * Postgres hybrid auth storage implementation
 * @author eugene
 */
class PostgresHybridAuthStorage implements IHybridAuthStorage {

    function __construct() {
        $this->conn = pg_connect(PostgresUtils::getConnString(), PGSQL_CONNECT_FORCE_NEW);
        TU::throwIf($this->conn == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error(),
                InternalErrorException::ERROR_CONNECTION_PROBLEMS);
    }

    /*
     * Release all the resources handled by this storage object
     * @returns: void
     */
    public function __destruct() {
        if ($this->conn != FALSE) {
            //pg_close($this->conn);
        }
    }

    public function saveHAuthRecord(HybridAuthRecord $record) {
        $result = pg_query_params($this->conn, self::$SQL_INSERT_HAUTH,
            array($record->getLoginProviderName(), $record->getLoginProviderIdentifier(),
                $record->getUserId()));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
    }

    public function getAuthentificatedUser($loginProvider, $loginProviderIdentifier,
                                            $email = NULL) {
        assert(is_string($loginProvider));
        assert($loginProviderIdentifier != NULL);

        $result = pg_query_params($this->conn, self::$SQL_GET_USER_EMAIL_PASS_BY_HAUTH,
            array($loginProvider, $loginProviderIdentifier));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        $data = pg_fetch_object($result);
        if ($data == FALSE) {
            $data = $this->getUserByEmail($email);
            if ($data == FALSE) {
                throw new InvalidArgumentException("Uknown hybauth user");
            }
        }

        $email = $data->email;
        $password = $data->password;
        $userStorage = new PostgresUserStorage();
        $authUser = $userStorage->getAuthentificatedUser($email, $password);
        return $authUser;
    }

    public function getUserHAuthRecords($userId) {
        assert(is_numeric($userId));

        $hauths = array();
        $result = pg_query_params($this->conn, self::$SQL_GET_USER_HAUTHS, array($userId));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        while(($data = pg_fetch_object($result)) != FALSE) {
            array_push($hauths,
                new HybridAuthRecord($data->userid, $data->loginprovider, loginprovideridentifier));
        }
        return $hauths;
    }

    private function getUserByEmail($email) {
        //second attempt with email
        if ($email == NULL)
            return FALSE;
        assert(is_string($email));
                
        $result = pg_query_params($this->conn, self::$SQL_GET_USER_EMAIL_PASS_BY_EMAIL,
            array($email));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        return pg_fetch_object($result);
    }

    private $conn;
    
    private static $SQL_GET_USER_EMAIL_PASS_BY_HAUTH = "SELECT email, password
        FROM egp.users
        WHERE id = (select userId from egp.ha_logins where loginProvider = $1 AND loginProviderIdentifier = $2);";
    private static $SQL_GET_USER_EMAIL_PASS_BY_EMAIL = "SELECT email, password
        FROM egp.users
        WHERE email = $1;";
    private static  $SQL_INSERT_HAUTH = "INSERT INTO egp.ha_logins(loginprovider, loginprovideridentifier, userid)
        VALUES ($1, $2, $3);";
    private static $SQL_GET_USER_HAUTHS = "SELECT loginprovider, loginprovideridentifier, userid
        FROM egp.ha_logins
        WHERE id = $1;";
}