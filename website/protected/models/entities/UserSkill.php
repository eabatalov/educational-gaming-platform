<?php

/**
 * Storage information about level of particular user skill
 *
 * @author eugene
 */
class UserSkill {

    /*
     * @throws InvalidArgumentException
     */
    function __construct($userId, $skillId, $value) {
        $this->setUserId($userId);
        $this->setSkillId($skillId);
        $this->setValue($value);
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getSkillId() {
        return $this->skillId;
    }

    public function getValue() {
        return $this->value;
    }

    public function setUserId($userId) {
        self::validateUserId($userId);
        $this->userId = $userId;
    }

    public function setSkillId($skillId) {
        self::validateSkillId($skillId);
        $this->skillId = $skillId;
    }

    public function setValue($value) {
        TU::throwIfNot(is_int($value), TU::INVALID_ARGUMENT_EXCEPTION, "value must be integer");
        $this->value = $value;
    }

    public static function validateUserId($userId) {
        TU::throwIfNot(is_numeric($userId), TU::INVALID_ARGUMENT_EXCEPTION, "user id must be integer");
    }

    public static function validateSkillId($skillId) {
        TU::throwIfNot(is_numeric($skillId), TU::INVALID_ARGUMENT_EXCEPTION, "skill id must be integer");
    }

    //Numeric
    private $userId;
    //Numeric
    private $skillId;
    //Int
    private $value;
}