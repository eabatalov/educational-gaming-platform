<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-29 at 04:02:43.
 */
class CustomerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Customer
     */
    protected $customer;
    protected $customerUser;
    protected $friend1;
    protected $friend2;
    protected $friend3;

    protected function mkUser($id) {
        $email = $id . "@example.com";
        $name = "name" . $id;
        $surname = "surname" . $id;
        $isActive = FALSE;
        $role = UserRole::CUSTOMER;
        $password = "password" . $id;
        $user = AuthentificatedUser::createInstance($email,
                $name, $surname, $isActive,
                $role,
                $password, $id);
        return $user;
    }

    protected function mkCustomer($id, $friends) {
        $this->customerUser = $this->mkUser($id);
        return Customer::createInstance($this->customerUser, $friends);
    }

    static public function mkCustomerFromArray($array, $friends = array()) {
        $user = UserTest::mkUserFromArray($array);
        return Customer::createInstance($user, $friends);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->friend1 = $this->mkCustomer("1", array());
        $this->friend2 = $this->mkCustomer("2", array());
        $this->friend3 = $this->mkCustomer("3", array());
        $this->customer = $this->mkCustomer("0", array($this->friend1,
            $this->friend2, $this->friend3));
    }

    /**
     * @covers model\Customer::getUser
     */
    public function testGetUser() {
        assert($this->customer->getUser() == $this->customerUser);
    }

    /**
     * @covers model\Customer::getFriends
     * @covers model\Customer::__construct
     * @covers model\Customer::setFriends
     */
    public function testGetFriends() {
        assert($this->customer->getFriends()[1] == $this->friend1);
        assert($this->customer->getFriends()[2] == $this->friend2);
        assert($this->customer->getFriends()[3] == $this->friend3);
    }

    public function testChangesTracking() {
        $changes = $this->customer->getValueChanges();
        assert(empty($changes));
    }

    /*
     * @covers Customer::rules
     */
    public function testValidation() {
        assert($this->customer->validate(), var_export($this->customer, true));
    }

    static public function CustomersDataEq(Customer $c1, Customer $c2) {
        $userEq = $c1->getUser()->getName() == $c2->getUser()->getName() and
                $c1->getUser()->getSurname() == $c2->getUser()->getSurname() and
                $c1->getUser()->getEmail() == $c2->getUser()->getEmail() and
                $c1->getUser()->getIsActive() == $c2->getUser()->getIsActive() and
                $c1->getUser()->getRole() == $c2->getUser()->getRole();
        return $userEq and array_keys($c1->getFriends()) == array_keys($c2->getFriends());
    }

    /*
     * @covers Customer::createEmpty()
     */
    public function testCreateEmpty() {
        $customer = Customer::createEmpty();
        assert($customer->getUser() == NULL);
        assert($customer->getFriends() == NULL);
    }
}
