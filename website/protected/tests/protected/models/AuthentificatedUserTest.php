<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-30 at 03:10:18.
 */
class AuthentificatedUserTest extends PHPUnit_Framework_TestCase {

    const PASS_SUFFIX = "password";

    protected function mkUser($id_ = NULL, $email_ = NULL) {
        $id = $id_ == NULL ? "0" : $id_;
        $email = $email_ == NULL ? $id . UserTest::EMAIL_SUFFIX : $email_;
        $name = $id . UserTest::NAME_SUFFIX;
        $surname = $id . UserTest::SURNAME_SUFFIX;
        $isActive = UserTest::IS_ACTIVE;
        $userDesc = $id . UserTest::DESCR_SUFFIX;
        $role = UserTest::ROLE;
        $pass = $id . AuthentificatedUserTest::PASS_SUFFIX;
        return new AuthentificatedUser($email, $name, $surname, $isActive,
            $userDesc, $role, $pass, $id);
    }

    /**
     * @covers AuthentificatedUser::getPassword
     */
    public function testGetPassword() {
        $id = "0";
        assert($this->mkUser()->getPassword() ==
            $id . AuthentificatedUserTest::PASS_SUFFIX);
    }

     /**
     * @covers AuthentificatedUser::setPassword
     */
    public function testSetPassword() {
        $user = $this->mkUser();
        $newPassword = "new_password";
        $user->setPassword($newPassword);
        assert($user->getPassword() == $newPassword);
    }

    /**
     * @covers All the change tracking setters of AuthentificatedUser class
     */
    public function testNoChangesTracking() {
        $user = $this->mkUser();

        $oldEmail = $user->getEmail();
        $oldName = $user->getName();
        $oldSurname = $user->getSurname();
        $oldPassword = $user->getPassword();
        $oldIsActive = $user->getIsActive();
        $oldUserDesc = $user->getDescription();
        $oldRole = $user->getRole();

        $newEmail = $oldEmail;
        $newName = $oldName;
        $newSurname = $oldSurname;
        $newPassword = $oldPassword;
        $newIsActive = $oldIsActive;
        $newUserDesc = $oldUserDesc;
        $newRole = $oldRole;

        $user->setEmail($newEmail);
        $user->setName($newName);
        $user->setSurname($newSurname);
        $user->setPassword($newPassword);
        $user->setIsActive($newIsActive);
        $user->setDescription($newUserDesc);
        $user->setRole($newRole);

        $changes = $user->getValueChanges();
        assert(empty($changes));
    }

    /**
     * @covers All the change tracking setters of AuthentificatedUser class
     */
    public function testRealChangesTracking() {
        $user = $this->mkUser();

        $oldEmail = $user->getEmail();
        $oldName = $user->getName();
        $oldSurname = $user->getSurname();
        $oldPassword = $user->getPassword();
        $oldIsActive = $user->getIsActive();
        $oldUserDesc = $user->getDescription();
        $oldRole = $user->getRole();

        $NEW_PREFIX = "new_";
        $newEmail = $NEW_PREFIX . $oldEmail;
        $newName = $NEW_PREFIX . $oldName;
        $newSurname = $NEW_PREFIX . $oldSurname;
        $newPassword = $NEW_PREFIX . $oldPassword;
        $newIsActive = !$oldIsActive;
        $newUserDesc = $NEW_PREFIX . $oldUserDesc;
        $newRole = ($oldRole + 1) % UserRole::LAST_ROLE;

        $user->setEmail($newEmail);
        $user->setName($newName);
        $user->setSurname($newSurname);
        $user->setPassword($newPassword);
        $user->setIsActive($newIsActive);
        $user->setDescription($newUserDesc);
        $user->setRole($newRole);

        $changes = $user->getValueChanges();
        assert(!empty($changes), "Changes were made to the object");

        foreach ($changes as $change) {
            $field = $change->getField();

            if ($field == AuthentificatedUser::CH_ID) {
                assert(FALSE, "id shouldn't change");
            } else if ($field == AuthentificatedUser::CH_NAME) {
                assert($change->getOldVal() == $oldName &&
                        $change->getNewVal() == $newName);
            } else if ($field == AuthentificatedUser::CH_SURNAME) {
                assert($change->getOldVal() == $oldSurname &&
                        $change->getNewVal() == $newSurname);
            } else if ($field == AuthentificatedUser::CH_EMAIL) {
                assert($change->getOldVal() == $oldEmail &&
                        $change->getNewVal() == $newEmail);
            } else if ($field == AuthentificatedUser::CH_PASS) {
                assert($change->getOldVal() == $oldPassword &&
                        $change->getNewVal() == $newPassword);
            } else if ($field == AuthentificatedUser::CH_ISACTIVE) {
                assert($change->getOldVal() == $oldIsActive &&
                        $change->getNewVal() == $newIsActive);
            } else if ($field == AuthentificatedUser::CH_DESCR) {
                assert($change->getOldVal() == $oldUserDesc &&
                        $change->getNewVal() == $newUserDesc);
            } else if ($field == AuthentificatedUser::CH_ROLE) {
                assert($change->getOldVal() == $oldRole &&
                        $change->getNewVal() == $newRole);
            } else {
                assert(FALSE, "Unhandled change");
            }
        }
    }

    /*
     * @covers AuthentificatedUser::rules()
     */
    public function testValidation() {
        $str240 =
            "12345678901234567890" . "12345678901234567890" .
            "12345678901234567890" . "12345678901234567890" .
            "12345678901234567890" . "12345678901234567890" .
            "12345678901234567890" . "12345678901234567890" .
            "12345678901234567890" . "12345678901234567890" .
            "12345678901234567890" . "12345678901234567890";
        $user = $this->mkUser();
        assert($user->validate(), "Normal password" . var_export($this->mkUser(), true));

        $user->setPassword("123");
        assert(!$user->validate(), "Too short password");

        $user->setPassword($str240);
        assert(!$user->validate(), "Too long password");
    }
}