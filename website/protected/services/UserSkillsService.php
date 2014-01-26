<?php

/**
 * Validates and performs typical user skills operations
 * @author eugene
 */
/*
* TODO: implement UserSkill objects validation.
* - Check that current client is allowed to change requested skill value
* - Check that current client has not excided limit of skill value changes for this moment
* - Looks like we'll allow value change only for leaf skill nodes in tree of skills
* - Check that skill_id exists and return InvalidArgumentException instead of InternalErrorException
*   which we return now.
*/
class UserSkillsService {
    /*
     * @throws InternalErrorException if failed on storage problem
     */
    function __construct() {
        $this->userSkillsStorage = new PostgresUserSkillsStorage();
    }

    /*
     * @returns array of UserSkill objects
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException if failed on input validation
     */
    public function getUserSkills($userId, Paging $paging) {
        return $this->userSkillsStorage->getUserSkills($userId, $paging);
    }

    /*
     * @returns UserSkill object
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException if failed on input validation
     */
    public function getUserSkill($userId, $skillId) {
        return $this->userSkillsStorage->getUserSkill($userId, $skillId);
    }

    /*
     * @returns void
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException if failed on input validation
     */
    public function setUserSkillValue(UserSkill $userSkill) {
        //Simple validation.
        $valueCurrent =
            $this->userSkillsStorage->getUserSkill($userSkill->getUserId(), $userSkill->getSkillId())->getValue();
        $valueDiff = $valueCurrent - $userSkill->getValue();
        TU::throwIf($valueDiff >= 100, TU::INVALID_ARGUMENT_EXCEPTION, "Skill value increment cannot be >= 100");
        $this->userSkillsStorage->updateUserSkill($userSkill);
    }

    private $userSkillsStorage;
}