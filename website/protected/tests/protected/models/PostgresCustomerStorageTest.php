<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-05 at 00:38:35.
 */
class PostgresCustomerStorageTest extends PHPUnit_Framework_TestCase {

    /**
     * @var PostgresCustomerStorage
     */
    static protected $storage;
    static protected $fr;
    const PASSWORD = "777secret";
    const EMAIL = "postgres_customer_storage_test@example.com";

    static public function setUpBeforeClass() {
        PostgresUserStorageTest::setUpBeforeClass();
        self::$storage = new PostgresCustomerStorage();
        self::$fr = array();

        for($i = 1; $i <= 3; ++$i) {
            $fr_email = strval($i) . "friend" . self::EMAIL;
			self::$fr[$i] = Customer::createInstance(
				UserTest::mkUserFromArray(array('email' => $fr_email)));
            self::$storage->addCustomerAndUser(self::$fr[$i], self::PASSWORD);
            self::$fr[$i] = self::$storage->getCustomer($fr_email);
        }
    }

    /**
     * @covers PostgresCustomerStorage::__construct
     */
    public function testConstruct() {
        $st1 = new PostgresCustomerStorage();
        $st2 = new PostgresCustomerStorage();
        $st3 = new PostgresCustomerStorage();
    }

    protected static function mkCustomer() {
        return CustomerTest::mkCustomerFromArray(array(
            "email" => self::EMAIL,
            "password" => self::PASSWORD
        ), self::$fr);
    }

    
    public function testAddCustomer() {
        $customer = self::mkCustomer();
        self::$storage->addCustomerAndUser($customer, self::PASSWORD);
        $savedCustomer = self::$storage->getCustomer($customer->getUser()->getEmail());
        assert(CustomerTest::CustomersDataEq($customer, $savedCustomer),
            var_export($savedCustomer, true) . var_export($customer, true));
    }

    /**
     * @covers PostgresCustomerStorage::getAuthCustomer
     */
    public function testGetAuthCustomer() {
        $customer = self::mkCustomer();
        $authCustomer =
            self::$storage->getAuthCustomer($customer->getUser()->getEmail(),
                    self::PASSWORD);
        assert($authCustomer instanceof AuthentificatedCustomer);
        assert(CustomerTest::CustomersDataEq($customer, $authCustomer),
            var_export($authCustomer, true) . var_export($customer, true));
    }

    /**
     * @covers PostgresCustomerStorage::searchCustomers
     */
    public function testSearchCustomers() {
        $customer = self::mkCustomer();
        $searchResults = self::$storage->searchCustomers($customer->getUser()->getSurname());
        foreach ($searchResults as $foundCustomer) {
            if ($foundCustomer->getUser()->getEmail() == $customer->getUser()->getEmail()) {
                assert(CustomerTest::CustomersDataEq($customer, $foundCustomer),
                    var_export($foundCustomer, true) . var_export($customer, true));
                return;
            }
        }
        assert(FALSE, "Customer is not found");
    }

    /**
     * @covers PostgresCustomerStorage::saveAuthCustomer
     */
    public function testSaveAuthCustomer() {
        $customer = self::mkCustomer();
        $authCustomer =
            self::$storage->getAuthCustomer($customer->getUser()->getEmail(), self::PASSWORD);
        //delete 1 friend
        $authCustomer->delFriend(self::$storage->getCustomer(self::$fr[2]->getUser()->getEmail()));
        //add new friend
        $fr4 = Customer::createInstance(UserTest::mkUserFromArray(array(
            "email" => 'fr4' . self::EMAIL,
            "password" => self::PASSWORD
        )));
        self::$storage->addCustomerAndUser($fr4, self::PASSWORD);
        $fr4 = self::$storage->getCustomer($fr4->getUser()->getEmail());
        $authCustomer->addFriend($fr4);
        //check that all is ok
        self::$storage->saveAuthCustomer($authCustomer);
        $authCustomer2 =
            self::$storage->getAuthCustomer($customer->getUser()->getEmail(), self::PASSWORD);
        assert(CustomerTest::CustomersDataEq($authCustomer2, $authCustomer),
                    var_export($authCustomer2, true) . var_export($authCustomer, true));
    }
    /**
     * @covers PostgresCustomerStorage::addCustomer
     */
    public function testInvalidCustomerAndUserAdd() {
        try {
            $customer = CustomerTest::mkCustomerFromArray(array(
                "email" => ""
            ));
            self::$storage->addCustomerAndUser($customer, self::PASSWORD);
        } catch (InvalidArgumentException $ex) {
            assert($ex->getCode() == ModelObject::ERROR_INVALID_OBJECT);
        }
    }
    /**
     * @covers PostgresCustomerStorage::saveAuthCustomer
     */
    public function testInvalidAuthCustomerSave() {
            //PASS - no invalid customer for now
    }
}
