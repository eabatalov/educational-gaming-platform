<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-30 at 03:10:18.
 */
class AuthentificatedUserTest extends PHPUnit_Framework_TestCase {

    const PASS_SUFFIX = "password";

    public function mkUser($id = NULL, $email = NULL) {
        $fields = array(
            "id" => $id == NULL ? "0" : $id,
            "email" => $email == NULL ? $id . UserTest::EMAIL_SUFFIX : $email
        );
        return $this->mkUserFromArray($fields);
    }

    static public function mkUserFromArray($fields = array()) {
        $id = array_key_exists("id", $fields) ? $fields['id'] : "0";
        $email = array_key_exists("email", $fields) ? $fields['email'] : $id . UserTest::EMAIL_SUFFIX;
        $name = array_key_exists("name", $fields) ? $fields['name'] : $id . UserTest::NAME_SUFFIX;
        $surname = array_key_exists("surname", $fields) ? $fields['surname'] : $id . UserTest::SURNAME_SUFFIX;
        $isActive = array_key_exists("isActive", $fields) ? $fields['isActive'] : UserTest::IS_ACTIVE;
        $pass = array_key_exists("pass", $fields) ? $fields['pass'] : $id . self::PASS_SUFFIX;
        $role = array_key_exists("role", $fields) ? $fields['role'] : UserTest::ROLE;
        return AuthentificatedUser::createAUInstance($email, $name, $surname, $isActive,
                                        $role, $pass, NULL, $id);
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
     * @covers AuthentificatedUser::setPassword
     */
    public function testSetPasswordInvInput() {
        $user = $this->mkUser();
        try {
            $user->setPassword($this);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            //PASS
        }
    }

    static private function setterTest($object, $_etter, $val, $invVal) {
        $setter = 'set' . $_etter;
        $getter = 'get' . $_etter;
        $object->$setter($val);
        assert($object->$getter() == $val);
        try {
            $object->$setter($invVal);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            //PASS
        }
    }

    /**
     * @covers AuthentificatedUser::setEmail
     * @covers AuthentificatedUser::setIsActive
     * @covers AuthentificatedUser::setName
     * @covers AuthentificatedUser::setRole
     * @covers AuthentificatedUser::setSurname
     * @covers AuthentificatedUser::setDescription
     */
    public function testUserSetters() {
        $user = $this->mkUser();
        self::setterTest($user, 'Email', "foo@bar.com", 1);
        self::setterTest($user, 'IsActive', !$user->getIsActive(), "yes!");
        self::setterTest($user, 'Name', $user->getName() ."Johnny", 1);
        self::setterTest($user, 'Surname', $user->getSurname() . "bar", 1);
    }

    public function testGetUser() {
        $authUser = $this->mkUser();
        assert($authUser->getEmail() == $authUser->getUser()->getEmail());
        assert($authUser->getName() == $authUser->getUser()->getName());
        assert($authUser->getSurname() == $authUser->getUser()->getSurname());
        assert($authUser->getIsActive() == $authUser->getUser()->getIsActive());
        assert($authUser->getRole() == $authUser->getUser()->getRole());
        assert($authUser->getId() == $authUser->getUser()->getId());
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
        $oldRole = $user->getRole();

        $newEmail = $oldEmail;
        $newName = $oldName;
        $newSurname = $oldSurname;
        $newPassword = $oldPassword;
        $newIsActive = $oldIsActive;
        $newRole = $oldRole;

        $user->setEmail($newEmail);
        $user->setName($newName);
        $user->setSurname($newSurname);
        $user->setPassword($newPassword);
        $user->setIsActive($newIsActive);
        $user->setRole($newRole);

        $changes = $user->getValueChanges();
        echo var_dump($changes, TRUE) . PHP_EOL;
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
        $oldRole = $user->getRole();

        $NEW_PREFIX = "new_";
        $newEmail = $NEW_PREFIX . $oldEmail;
        $newName = $NEW_PREFIX . $oldName;
        $newSurname = $NEW_PREFIX . $oldSurname;
        $newPassword = $NEW_PREFIX . $oldPassword;
        $newIsActive = !$oldIsActive;
        $newRole = UserRole::ADMIN;

        $user->setEmail($newEmail);
        $user->setName($newName);
        $user->setSurname($newSurname);
        $user->setPassword($newPassword);
        $user->setIsActive($newIsActive);
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

    /*
     * @covers AuthentificatedUser::createEmpty()
     */
    public function testCreateEmpty() {
        $authUser = AuthentificatedUser::createEmpty();
        assert($authUser->getName() == NULL);
        assert($authUser->getSurname() == NULL);
        assert($authUser->getEmail() == NULL);
        assert($authUser->getRole() == NULL);
        assert($authUser->getPassword() == NULL);
        assert($authUser->getIsActive() == NULL);
    }
}
