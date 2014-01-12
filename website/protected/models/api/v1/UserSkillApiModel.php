<?php

/**
 * Adaptor from/to JSON API UserSkill object to UserSkill model object
 * @author eugene
 */
class UserSkillApiModel extends SerializableApiModel {

    public function initFromArray($fieldsArray) {
        $this->user_id = TU::getValueOrThrow("user_id", $fieldsArray);
        $this->skill_id = TU::getValueOrThrow("skill_id", $fieldsArray);
        $this->value = TU::getValueOrThrow("value", $fieldsArray);
    }

    public function initFromUserSkill(UserSkill $userSkill) {
        $this->user_id = $userSkill->getUserId();
        $this->skill_id = $userSkill->getSkillId();
        $this->value = $userSkill->getValue();
    }

    public function toUserSkill() {
        TU::throwIfNot(is_int($this->value), TU::INVALID_ARGUMENT_EXCEPTION, "value must be integer");
        return new UserSkill(
            self::userIdFromApi($this->user_id),
            self::skillIdFromApi($this->skill_id),
            $this->value
        );
    }

    public static function userIdFromApi($userId) {
        TU::throwIfNot(is_numeric($userId), TU::INVALID_ARGUMENT_EXCEPTION, "user id must be numeric");
        TU::throwIfNot(is_string($userId), TU::INVALID_ARGUMENT_EXCEPTION, "user id must be string");
        return $userId;
    }

    public static function skillIdFromApi($skillId) {
        TU::throwIfNot(is_numeric($skillId), TU::INVALID_ARGUMENT_EXCEPTION, "skill id must be numeric");
        TU::throwIfNot(is_string($skillId), TU::INVALID_ARGUMENT_EXCEPTION, "skill id must be string");
        return $skillId;
    }
    
    //String
    public $user_id;
    //String
    public $skill_id;
    //Int32
    public $value;
}
