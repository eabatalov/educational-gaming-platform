<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-30 at 03:11:40.
 */
class AuthentificatedCustomerTest extends PHPUnit_Framework_TestCase {

    protected function mkCustomer($id, $friends) {
        $user = new AuthentificatedUser($id . "@example.com",
                "name" . $id, "surname" . $id, FALSE, $id,
                UserRole::CUSTOMER,
                "password" . $id, $id);
        return new AuthentificatedCustomer($user, $friends);
    }

    /**
     * @covers AuthentificatedCustomer::addFriend
     */
    public function testAddFriendNormalInput() {
        $customer = $this->mkCustomer("0", array());
        $customer->addFriend($this->mkCustomer("1", array()));
        $customer->addFriend($this->mkCustomer("2", array()));
        $customer->addFriend($this->mkCustomer("3", array()));
        assert($customer->getFriends() ==
                array(
                    "1" =>$this->mkCustomer("1", array()),
                    "2" => $this->mkCustomer("2", array()),
                    "3" => $this->mkCustomer("3", array()),
                ));
    }

    /**
     * @covers AuthentificatedCustomer::addFriend
     */
    public function testAddFriendInvalidInput() {
        $customer = $this->mkCustomer("0", array());
        $customer->addFriend($this->mkCustomer("1", array()));
        try {
            $customer->addFriend($this->mkCustomer("1", array()));
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            //PASSED
        }

        try {
            $customer->addFriend("I am new very good friend, Add me!");
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            //PASSED
        }
    }

    /**
     * @covers AuthentificatedCustomer::delFriend
     */
    public function testDelFriendNormalInput() {
        $friends = array(
            "1" => $this->mkCustomer("1", array()),
            "2" => $this->mkCustomer("2", array()),
            "3" => $this->mkCustomer("3", array()),
        );
        $customer = $this->mkCustomer("0", $friends);
        $customer->delFriend($this->mkCustomer("1", array()));
        $customer->delFriend($this->mkCustomer("3", array()));
        $customer->delFriend($this->mkCustomer("2", array()));
        $friends2 = $customer->getFriends();
        assert(empty($friends2));
    }

    /**
     * @covers AuthentificatedCustomer::delFriend
     */
    public function testDelFriendInvalidInput() {
        $customer = $this->mkCustomer("0", array());
        try {
            //Absent friend
            $customer->delFriend($this->mkCustomer("1", array()));
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            //PASSED
        }

        try {
            //Invalid type
            $customer->delFriend("I am new very good friend, Add me!");
            assert(FALSE, "Exception expected");
        } catch (InvalidArgumentException $ex) {
            //PASSED
        }
    }

    /**
     * @covers AuthentificatedCustomer::addFriend
     * @covers AuthentificatedCustomer::delFriend
     */
    public function testChangesTrackingAddDelFriend() {
        $friends = array(
            "1" => $this->mkCustomer("1", array()),
            "2" => $this->mkCustomer("2", array()),
            "3" => $this->mkCustomer("3", array()),
        );
        $customer = $this->mkCustomer("0", $friends);
        $customer->delFriend($this->mkCustomer("1", array()));
        $customer->delFriend($this->mkCustomer("3", array()));
        $customer->delFriend($this->mkCustomer("2", array()));

        $customer->addFriend($this->mkCustomer("10", array()));
        $customer->addFriend($this->mkCustomer("11", array()));

        foreach ($customer->getValueChanges() as $change) {
            $field = $change->getField();

            if ($field == AuthentificatedCustomer::CH_FRIENDS) {
                assert($change->getOldVal() == $friends);
                assert($change->getNewVal() == array(
                    "10" => $this->mkCustomer("10", array()),
                    "11" => $this->mkCustomer("11", array())
                ), "Invalid change set: " . var_export($change->getNewVal(), true));
            } else {
                assert(FALSE, "Only friends should be changed");
            }
        }
    }

    /*
     * @covers Customer::rules
     */
    public function testValidation() {
        $customer = $this->mkCustomer("0", array());
        $customer->addFriend($this->mkCustomer("1", array()));
        $customer->addFriend($this->mkCustomer("2", array()));
        $customer->addFriend($this->mkCustomer("3", array()));
        assert($customer->validate(), "Normal customer");
    }
}