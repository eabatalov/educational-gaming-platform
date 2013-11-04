<?php

/**
 * PostgreSQL implementation of ICustomerStorage interface.
 * Need to install PostgreSQL php lib to work
 * On ubuntu: "sudo apt-get install php5-pgsql"
 * @author eugene
 */
class PostgresCustomerStorage implements ICustomerStorage {

    public function __construct() {
        throw new Exception("Not implemented");
    }

    public function addCustomer(Customer $customer, $password) {
        throw new Exception("Not implemented");
    }

    public function getCustomer($email) {
        assert(is_string($email));
        throw new Exception("Not implemented");
    }

    public function getAuthCustomer($email, $password) {
        assert(is_string($email));
        assert(is_string($password));
        throw new Exception("Not implemented");
    }

    public function findCustomers($query, $matchType = NULL) {
        assert(is_string($query));
        throw new Exception("Not implemented");
    }

    public function saveAuthCustomer(AuthentificatedCustomer $authCustomer) {
        throw new Exception("Not implemented");
    }
}
