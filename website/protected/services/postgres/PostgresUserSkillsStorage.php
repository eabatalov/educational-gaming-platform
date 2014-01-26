<?php

/**
 * Description of PostgresUserSkillsStorage
 *
 * @author eugene
 */
class PostgresUserSkillsStorage {

    function __construct() {
        $this->conn = pg_connect(PostgresUtils::getConnString(), PGSQL_CONNECT_FORCE_NEW);
        TU::throwIf($this->conn == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error(),
            InternalErrorException::ERROR_CONNECTION_PROBLEMS);
    }

    public function __destruct() {
        if ($this->conn != FALSE) {
            //pg_close($this->conn);
        }
    }

    /*
     * @returns array of UserSkill objects
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException if failed on input validation
     */
    public function getUserSkills($userId, Paging $paging) {
        UserSkill::validateUserId($userId);
        TU::throwIfNot(is_numeric($paging->getOffset()), TU::INVALID_ARGUMENT_EXCEPTION,
            "Only numeric paging.offset is supported for this service");
        $skills = array();

        $result = pg_query_params($this->conn, self::$SQL_SELECT_BY_USER_ID,
            array($userId, $paging->getOffset(), $paging->getLimit()));
        TU::throwIf($result === FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        while(($data = pg_fetch_object($result)) != FALSE) {
            $skills[] = self::userSkillFromDBData($data);
        }

        $result = pg_query_params($this->conn, self::$SQL_SELECT_BY_USER_ID_TOTAL_COUNT,
            array($userId));
        TU::throwIf($result === FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
        $data = pg_fetch_object($result);
        TU::throwIf($data === FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
        $paging->setTotal(intval($data->cnt));
        return $skills;
    }
    /*
     * @returns UserSkill object
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException if failed on input validation
     */
    public function getUserSkill($userId, $skillId) {
        UserSkill::validateUserId($userId);
        UserSkill::validateSkillId($skillId);

        $result = pg_query_params($this->conn, self::$SQL_SELECT,
            array($userId, $skillId));
        TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

        $userSkill = new UserSkill($userId, $skillId, 0);
        $data = pg_fetch_object($result);
        if ($data !== FALSE) {
             $userSkill->setValue(self::pgValueToUserSkillValue($data->value));
        }

        return $userSkill;
    }
    /*
     * @returns array of UserSkill objects
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException if failed on input validation
     */
    public function updateUserSkill(UserSkill $userSkill) {

        $result = pg_query_params($this->conn, self::$SQL_UPDATE, array(
            $userSkill->getUserId(),
            $userSkill->getSkillId(),
            $userSkill->getValue())
        );
        $result = pg_fetch_object($result); //Successful update returns user_id
        if ($result === FALSE) {
            $result = pg_query_params($this->conn, self::$SQL_INSERT, array(
                $userSkill->getUserId(),
                $userSkill->getSkillId(),
                $userSkill->getValue())
            );
            TU::throwIf($result === FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());
        }
    }

    private static function userSkillFromDBData($data) {
        return new UserSkill(
            $data->user_id,
            $data->skill_id,
            self::pgValueToUserSkillValue($data->value)
        );
    }

    private static function pgValueToUserSkillValue($pgValue) {
        return intval($pgValue);
    }

    private $conn;
    //SQL
    static private $SQL_INSERT =
        "INSERT INTO egp.user_skills
            (user_id, skill_id, value)
        VALUES ($1, $2, $3) RETURNING user_id;";
    static private $SQL_UPDATE =
        "UPDATE egp.user_skills
            SET value=$3
        WHERE user_id=$1 AND skill_id=$2 RETURNING user_id;";
    static private $SQL_SELECT =
        "SELECT user_id, skill_id, value
        FROM egp.user_skills
        WHERE user_id=$1 AND skill_id=$2;";
    static private $SQL_SELECT_BY_USER_ID =
        "SELECT user_id, skill_id, value
        FROM egp.user_skills
        WHERE user_id=$1
        OFFSET $2
        LIMIT $3;";
    private static $SQL_SELECT_BY_USER_ID_TOTAL_COUNT =
        "SELECT count(*) AS cnt
         FROM egp.user_skills
         WHERE user_id = $1;";
}
