<?php

/**
 * Validates and performs typical user skills operations
 * @author eugene
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
    public function getUserSkills($userId, Paging &$paging) {
        return $this->userSkillsStorage->getUserSkills($userId, $paging);
    }

    /*
     * @returns void
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException if failed on input validation
     */
    public function setUserSkillValue(UserSkill $userSkill) {
        //Simple validation.
        $valueCurrent =
            $this->userSkillsStorage->getUserSkillValue($userSkill->getUserId(), $userSkill->getSkillId());
        $valueDiff = $valueCurrent - $userSkill->getValue();
        TU::throwIf($valueDiff >= 100, TU::INVALID_ARGUMENT_EXCEPTION, "Skill value increment cannot be >= 100");
        /*
         * TODO: implement real validation here.
         * - Check that current client is allowed to change requested skill value
         * - Check that current client has not excided limit of skill value changes for this moment
         * - Looks like we'll allow value change only for leaf skill nodes in tree of skills
         */
        $this->userSkillsStorage->updateUserSkill($userSkill);
    }

    private $userSkillsStorage;
}