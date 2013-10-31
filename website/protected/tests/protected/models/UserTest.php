<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-29 at 02:58:03.
 */
class UserTest extends \PHPUnit_Framework_TestCase {

    const EMAIL_SUFFIX = "@example.com";
    const NAME_SUFFIX = "name";
    const SURNAME_SUFFIX = "surname";
    const IS_ACTIVE = FALSE;
    const DESCR_SUFFIX = "descr";
    const ROLE = UserRole::CUSTOMER;

    protected function mkUser($id_ = NULL, $email_ = NULL) {
        $id = $id_ == NULL ? "0" : $id_;
        $email = $email_ == NULL ? $id . UserTest::EMAIL_SUFFIX : $email_;
        $name = $id . UserTest::NAME_SUFFIX;
        $surname = $id . UserTest::SURNAME_SUFFIX;
        $isActive = UserTest::IS_ACTIVE;
        $userDesc = $id . UserTest::DESCR_SUFFIX;
        $role = new UserRole(UserTest::ROLE);
        return new User($email, $name, $surname, $isActive,
                            $userDesc, $role, $id);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers model\User::getId
     */
    public function testGetId() {
        assert($this->mkUser("0")->getId() == "0");
    }

    /**
     * @covers model\User::getEmail
     */
    public function testGetEmail() {
        $email = "test@example.com";
        assert($this->mkUser("0", $email)->getEmail() == $email);
    }

    /**
     * @covers model\User::getName
     */
    public function testGetName() {
        $id = "0";
        assert($this->mkUser($id)->getName() == $id . UserTest::NAME_SUFFIX);
    }

    /**
     * @covers model\User::getSurname
     */
    public function testGetSurname() {
        $id = "0";
        assert($this->mkUser($id)->getSurname() == $id . UserTest::SURNAME_SUFFIX);
    }

    /**
     * @covers model\User::getIsActive
     */
    public function testGetIsActive() {
        assert($this->mkUser()->getIsActive() == UserTest::IS_ACTIVE);
    }

    /**
     * @covers model\User::getUserDesc
     */
    public function testGetUserDesc() {
        $id = "0";
        assert($this->mkUser($id)->getDescription() == $id . UserTest::DESCR_SUFFIX);
    }

    /**
     * @covers model\User::getRole
     */
    public function testGetRole() {
        $id = "0";
        assert($this->mkUser($id)->getRole() == new UserRole(UserTest::ROLE));
    }

    public function testChangesTracking() {
        $id = "0";
        $changes = $this->mkUser($id)->getValueChanges();
        assert(empty($changes));
    }
}
