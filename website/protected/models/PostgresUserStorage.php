<?php

/**
 * PostgreSQL implementation of IUserStorage interface
 * Need to install PostgreSQL php lib to work
 * On ubuntu: "sudo apt-get install php5-pgsql"
 * @author eugene
 */
class PostgresUserStorage implements IUserStorage {

    /*
     * Creates object and connects to postgres DB
     * @returns PostgresUserStorage object connected to DB
     * @throws StorageException if connection falied
     */
    function __construct() {
        $this->conn = pg_connect(PostgresUtils::getConnString(), PGSQL_CONNECT_FORCE_NEW);
        TU::throwIf($this->conn == FALSE, TU::STORAGE_EXCEPTION, pg_last_error(),
            StorageException::ERROR_CONNECTION_PROBLEMS);
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

    public function addUser(User $user, $password) {
        assert(is_string($password));
        TU::throwIfNot($user->validate(), TU::INVALID_ARGUMENT_EXCEPTION,
            NULL, ModelObject::ERROR_INVALID_OBJECT);

        $dupEmail = TRUE;
        try {
            //TODO make it all in DB, fix races
            $this->getUser($user->getEmail());
        } catch (InvalidArgumentException $ex) { $dupEmail = FALSE; }
        if ($dupEmail) {
            throw new InvalidArgumentException('', self::ERROR_EMAIL_EXISTS);
        }

        $result = pg_query_params($this->conn, self::$SQL_INSERT, array(
                $user->getName(),
                $user->getSurname(),
                $user->getEmail(),
                PostgresUtils::boolToPGBool($user->getIsActive()),
                $password,
                $user->getRole()));
        TU::throwIf($result == FALSE, TU::STORAGE_EXCEPTION, pg_last_error());
    }

    public function getAuthentificatedUser($email, $password) {
        assert(is_string($email));
        assert(is_string($password));
        $result = pg_query_params($this->conn, self::$SQL_SELECT_BY_EMAIL,
                                         array($email));
        TU::throwIf($result == FALSE, TU::STORAGE_EXCEPTION, pg_last_error());

        $data = pg_fetch_object($result);
        TU::throwIf($data == FALSE, TU::INVALID_ARGUMENT_EXCEPTION, pg_last_error(),
            IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL);
        TU::throwIf($data->password != $password, TU::INVALID_ARGUMENT_EXCEPTION, pg_last_error(),
            IUserStorage::ERROR_INVALID_PASSWORD);

        return new AuthentificatedUser(
                $data->email,
                $data->name,
                $data->surname,
                PostgresUtils::PGBoolToPHP($data->is_active),
                $data->role,
                $data->password,
                $data->id
        );
    }

    public function getUser($email) {
        assert(is_string($email));
        return $this->getUserBy(self::$SQL_SELECT_BY_EMAIL, $email);
    }

    public function saveAuthUser(AuthentificatedUser $authUser) {
        TU::throwIfNot($authUser->validate(), TU::INVALID_ARGUMENT_EXCEPTION,
            NULL, ModelObject::ERROR_INVALID_OBJECT);

        $changes = $authUser->getValueChanges();
        //TODO make it all in DB, fix races
        $authEmail = array_key_exists(AuthentificatedUser::CH_EMAIL, $changes) ?
            $changes[User::CH_EMAIL]->getOldVal() : $authUser->getEmail();
        $authPass = array_key_exists(AuthentificatedUser::CH_PASS, $changes) ?
            $changes[AuthentificatedUser::CH_PASS]->getOldVal() : $authUser->getPassword();
        $authUserFromDb =
            $this->getAuthentificatedUser($authEmail, $authPass);
        //auth password and email are valid here
        if (array_key_exists(User::CH_EMAIL, $changes)) {
            $hasUserWithNewEmail = TRUE;
            try {
                $newEmailUser = $this->getUser($authUser->getEmail());
                assert($newEmailUser->getId() != $authUser->getId());
            } catch(InvalidArgumentException $ex) { $hasUserWithNewEmail = FALSE; }
            TU::throwIf($hasUserWithNewEmail, TU::INVALID_ARGUMENT_EXCEPTION,
                'User with such email already exists', IUserStorage::ERROR_EMAIL_EXISTS);
        }

        $result = pg_query_params($this->conn, self::$SQL_UPDATE, array(
            $authUser->getId(),
            $authUser->getName(),
            $authUser->getSurname(),
            $authUser->getEmail(),
            $authUser->getIsActive(),
            $authUser->getPassword(),
            $authUser->getRole()));
        TU::throwIf($result == FALSE, TU::STORAGE_EXCEPTION, pg_last_error());
    }

    protected function getUserById($id) {
        assert(is_numeric($id));
        return $this->getUserBy(self::$SQL_SELECT_BY_ID, $id);
    }

    protected function getUserBy($sql, $arg) {
        $result = pg_query_params($this->conn, $sql, array($arg));
        TU::throwIf($result == FALSE, TU::STORAGE_EXCEPTION, pg_last_error());

        $data= pg_fetch_object($result);
        TU::throwIf($data == FALSE, TU::INVALID_ARGUMENT_EXCEPTION, pg_last_error(),
                self::ERROR_NO_USER_WITH_SUCH_EMAIL);

        return new User(
                $data->email,
                $data->name,
                $data->surname,
                PostgresUtils::PGBoolToPHP($data->is_active),
                $data->role,
                $data->id
        );
    }

    protected function searchUsers($query) {
        assert(is_string($query));
        $result = pg_query_params($this->conn, self::$SQL_SEARCH, array($query));
        TU::throwIf($result == FALSE, TU::STORAGE_EXCEPTION, pg_last_error());

        $found = array();
        while(($data = pg_fetch_object($result)) != FALSE) {
            $found[$data->id] = new User(
                    $data->email,
                    $data->name,
                    $data->surname,
                    PostgresUtils::PGBoolToPHP($data->is_active),
                    $data->role,
                    $data->id
                );
        }
        return $found;
    }

    protected $conn;
    
    //SQL
    static private $SQL_INSERT =
        "INSERT INTO egp.users(
            name, surname, email, is_active, password, role)
        VALUES ($1, $2, $3, $4, $5, $6);";
    static private $SQL_UPDATE =
            "UPDATE egp.users
            SET name=$2, surname=$3, email=$4, is_active=$5, password=$6, role=$7
            WHERE id=$1;";
    static private $SQL_SELECT_BY_EMAIL =
            "SELECT id, name, surname, email, is_active, password, role
             FROM egp.users
             WHERE email=$1";
    static private $SQL_SELECT_BY_ID =
            "SELECT id, name, surname, email, is_active, password, role
             FROM egp.users
             WHERE id=$1";
    static private $SQL_SEARCH =
            "SELECT id, name, surname, email, is_active, password, role
             FROM egp.users
             WHERE name LIKE $1 OR surname LIKE $1";
}