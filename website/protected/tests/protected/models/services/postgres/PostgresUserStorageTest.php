<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-03 at 23:16:54.
 */
class PostgresUserStorageTest extends PostgresTest {

    /**
     * @var PostgresUserStorage
     */
    static protected $storage;

    const PASSWORD = "777secret";
    const EMAIL = "postgres_user_storage_test@example.com";
    const NO_EMAIl = "postgres_user_storage_test_fail@example.com";
    const DOUBLE_EMAIL = 'double@example.com';

    static public function setUpBeforeClass() {
        parent::setUpBeforeClass();
        self::$storage = new PostgresUserStorage();
    }

    /**
     * @covers PostgresUserStorage::__construct
     */
    public function testConstruct() {
        $st1 = new PostgresUserStorage();
        $st2 = new PostgresUserStorage();
        $st3 = new PostgresUserStorage();
    }

    /**
     * @covers PostgresUserStorage::addUser
     */
    public function testAddUser() {
        $user = UserTest::mkUserFromArray(array("email" => self::EMAIL));
        self::$storage->addUser($user, self::PASSWORD);
        $authUser = self::$storage->getAuthentificatedUser(
                $user->getEmail(), self::PASSWORD);
        assert($authUser->getEmail() == $user->getEmail());
        assert($authUser->getName() == $user->getName());
        assert($authUser->getSurname() == $user->getSurname());
        assert($authUser->getIsActive() == $user->getIsActive());
        assert($authUser->getRole() == $user->getRole());
        assert($authUser->getId() != $user->getId());
    }

    /**
     * @covers PostgresUserStorage::addUser
     */
    public function testAddUserSameEmails() {
        $user = UserTest::mkUserFromArray(array('email' => self::DOUBLE_EMAIL));
        self::$storage->addUser($user, self::PASSWORD);
        try {
            self::$storage->addUser($user, self::PASSWORD);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == IUserStorage::ERROR_EMAIL_EXISTS);
        }
    }

    /**
     * @covers PostgresUserStorage::getAuthentificatedUser
     */
    public function testGetAuthentificatedUser() {
        $authUser = self::$storage->getAuthentificatedUser(self::EMAIL, self::PASSWORD);
        assert($authUser instanceof AuthentificatedUser);
        assert($authUser->getEmail() == self::EMAIL);
        assert($authUser->getPassword() == self::PASSWORD);
    }

    /**
     * @covers PostgresUserStorage::getAuthentificatedUser
     */
    public function testGetAuthentificatedUserNoUserWithSuchEmail() {
        try {
            self::$storage->getAuthentificatedUser(self::NO_EMAIl, self::PASSWORD);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL);
        }
    }

    /**
     * @covers PostgresUserStorage::getAuthentificatedUser
     */
    public function testGetAuthentificatedUserInvalidPassword() {
        try {
            self::$storage->getAuthentificatedUser(self::EMAIL, self::PASSWORD . 'inval');
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == IUserStorage::ERROR_INVALID_PASSWORD);
        }
    }

    /**
     * @covers PostgresUserStorage::getUser
     */
    public function testGetUser() {
        $authUser = self::$storage->getUser(self::EMAIL, self::PASSWORD);
        assert($authUser instanceof User);
        assert($authUser->getEmail() == self::EMAIL);
    }

    /**
     * @covers PostgresUserStorage::getUser
     */
    public function testGetUserNoUserWithSuchEmail() {
        try {
            self::$storage->getUser(self::NO_EMAIl);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL);
        }
    }

    /**
     * @covers PostgresUserStorage::saveAuthUser
     */
    public function testSaveAuthUserNoSuchEmail() {
        try {
            $authUser = AuthentificatedUserTest::mkUserFromArray(
                            array("email" => self::NO_EMAIl));
            self::$storage->saveAuthUser($authUser);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL);
        }
    }

    /**
     * @covers PostgresUserStorage::saveAuthUser
     */
    public function testSaveAuthUserInvalidPassword() {
        try {
            $authUser = AuthentificatedUserTest::mkUserFromArray(
                            array("email" => self::EMAIL, "pass" => self::PASSWORD . "fail"));
            self::$storage->saveAuthUser($authUser);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == IUserStorage::ERROR_INVALID_PASSWORD);
        }
    }

    /**
     * @covers PostgresUserStorage::saveAuthUser
     */
    public function testSaveAuthUserEmailExists() {
        try {
            $authUser = self::$storage->getAuthentificatedUser(self::EMAIL, self::PASSWORD);
            $authUser->setEmail(self::DOUBLE_EMAIL);
            self::$storage->saveAuthUser($authUser);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == IUserStorage::ERROR_EMAIL_EXISTS);
        }
    }

    /**
     * @covers PostgresUserStorage::saveAuthUser
     */
    public function testSaveAuthUser() {
        $email = "bar@example.com";
        $pass = "777";
        $user = UserTest::mkUserFromArray(
                        array("email" => $email, "id" => 10));
        self::$storage->addUser($user, $pass);

        $authUser = self::$storage->getAuthentificatedUser(
                $user->getEmail(), $pass);
        $newEmail = "new" . $email;
        $newPass = "new" . $pass;
        $newName = "newname";
        $newSurname = "newsurname";
        $newRole = UserRole::ADMIN;
        $newIsActive = !$authUser->getIsActive();
        $authUser->setEmail($newEmail);
        $authUser->setName($newName);
        $authUser->setSurname($newSurname);
        $authUser->setPassword($newPass);
        $authUser->setRole($newRole);
        $authUser->setIsActive($newIsActive);
        self::$storage->saveAuthUser($authUser);

        $authUserSaved = self::$storage->getAuthentificatedUser($newEmail, $newPass);
        assert($authUser->getPassword() == $authUserSaved->getPassword());
        assert($authUser->getEmail() == $authUserSaved->getEmail());
        assert($authUser->getName() == $authUserSaved->getName());
        assert($authUser->getSurname() == $authUserSaved->getSurname());
        assert($authUser->getIsActive() == $authUserSaved->getIsActive());
        assert($authUser->getRole() == $authUserSaved->getRole());
        assert($authUser->getId() == $authUserSaved->getId());
    }
    /**
     * @covers PostgresUserStorage::addUser
     */
    public function testInvalidUserAdd() {
        try {
            $user = UserTest::mkUserFromArray(array("email" => ""));
            self::$storage->addUser($user, self::PASSWORD);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == ModelObject::ERROR_INVALID_OBJECT);
        }
    }
    /**
     * @covers PostgresUserStorage::saveAuthUser
     */
    public function testInvalidUserSave() {
        try {
            $authUser = self::$storage->getAuthentificatedUser(self::EMAIL, self::PASSWORD);
            $authUser->setEmail("");
            self::$storage->saveAuthUser($authUser);
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == ModelObject::ERROR_INVALID_OBJECT);
        }
    }
}
