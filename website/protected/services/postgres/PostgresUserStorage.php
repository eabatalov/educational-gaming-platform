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
     * @throws InternalErrorException if connection falied
     */
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
                $user->getRole(),
                $user->getAvatar(),
                $user->getBirthDate(),
                $user->getGender()
        ));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
    }

    public function getAuthentificatedUser($email, $password) {
        assert(is_string($email));
        assert(is_string($password));
        $result = pg_query_params($this->conn, self::$SQL_SELECT_BY_EMAIL,
                                         array($email));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        $data = pg_fetch_object($result);
        TU::throwIf($data == FALSE, TU::INVALID_ARGUMENT_EXCEPTION, pg_last_error(),
            IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL);
        TU::throwIf($data->password != $password, TU::INVALID_ARGUMENT_EXCEPTION, pg_last_error(),
            IUserStorage::ERROR_INVALID_PASSWORD);

        return $this->pgAuthUserToAuthUser($data);
    }

    function getAuthentificatedUserByAccessToken($accessToken) {
        assert(is_string($accessToken));
        $result = pg_query_params($this->conn, self::$SQL_SELECT_BY_ACCESS_TOKEN,
                                         array($accessToken));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        $data = pg_fetch_object($result);
        TU::throwIf($data == FALSE, TU::INVALID_ARGUMENT_EXCEPTION, pg_last_error(),
            IUserStorage::ERROR_NO_USER_WITH_SUCH_TOKEN);

        return $this->pgAuthUserToAuthUser($data);
    }

    private function pgAuthUserToAuthUser($data) {
        return AuthentificatedUser::createAUInstance(
                $data->email,
                $data->name,
                $data->surname,
                PostgresUtils::PGBoolToPHP($data->is_active),
                $data->role,
                $data->password,
                array(
                    User::OPT_AVATAR => $data->avatar,
                    User::OPT_BIRTH_DATE => PostgresUtils::PGDateToPhp($data->birthday),
                    User::OPT_GENDER => $data->gender,
                ),
                $data->id
        );
    }

    public function getUser($email) {
        assert(is_string($email));
        try {
            return $this->getUserBy(self::$SQL_SELECT_BY_EMAIL, $email);
        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgumentException($ex->getMessage(),
                self::ERROR_NO_USER_WITH_SUCH_EMAIL, $ex);
        }
    }

    public function getUserById($id) {
        assert(is_numeric($id));
        try {
            return $this->getUserBy(self::$SQL_SELECT_BY_ID, $id);
        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgumentException($ex->getMessage(),
                self::ERROR_NO_USER_WITH_SUCH_ID, $ex);
        }
    }

    public function saveAuthUser(AuthentificatedUser $authUser) {
        TU::throwIfNot($authUser->validate(), TU::INVALID_ARGUMENT_EXCEPTION,
            NULL, ModelObject::ERROR_INVALID_OBJECT);

        $result = pg_query_params($this->conn, self::$SQL_UPDATE, array(
            $authUser->getId(),
            $authUser->getName(),
            $authUser->getSurname(),
            $authUser->getEmail(),
            PostgresUtils::boolToPGBool($authUser->getIsActive()),
            $authUser->getPassword(),
            $authUser->getRole(),
            $authUser->getAvatar(),
            PostgresUtils::PhpDateToPG($authUser->getBirthDate()),
            $authUser->getGender()
        ));

        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
    }

    protected function getUserBy($sql, $arg) {
        $result = pg_query_params($this->conn, $sql, array($arg));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        $data= pg_fetch_object($result);
        TU::throwIf($data == FALSE, TU::INVALID_ARGUMENT_EXCEPTION, pg_last_error());

        return User::createUInstance(
                $data->email,
                $data->name,
                $data->surname,
                PostgresUtils::PGBoolToPHP($data->is_active),
                $data->role,
                array(
                    User::OPT_AVATAR => $data->avatar,
                    User::OPT_BIRTH_DATE => PostgresUtils::PGDateToPhp($data->birthday),
                    User::OPT_GENDER => $data->gender,
                ),
                $data->id
        );
    }

    protected $conn;
    
    //SQL
    static private $SQL_INSERT =
        "INSERT INTO egp.users(
            name, surname, email, is_active, password, role, avatar, birthday, gender)
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9);";
    static private $SQL_UPDATE =
            "UPDATE egp.users
            SET name=$2, surname=$3, email=$4, is_active=$5, password=$6, role=$7,
            avatar=$8, birthday=$9, gender=$10
            WHERE id=$1;";
    static private $SQL_SELECT_BY_EMAIL =
            "SELECT id, name, surname, email, is_active, password, role,
                avatar, birthday, gender
             FROM egp.users
             WHERE email=$1";
    static private $SQL_SELECT_BY_ID =
            "SELECT id, name, surname, email, is_active, password, role,
                avatar, birthday, gender
             FROM egp.users
             WHERE id=$1";
    static private $SQL_SELECT_BY_ACCESS_TOKEN =
            "SELECT id, name, surname, email, is_active, password, role,
                avatar, birthday, gender
             FROM egp.users
             WHERE id=(SELECT user_id
                FROM egp.api_access_tokens
                WHERE access_token=$1);";
}
