<?php

/**
 * Class which provides access to persistent storage of customer information.
 * @author eugene
 */
class ICustomerStorage {
    //InvalidArgumentException error codes
    const ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL = IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL;
    const ERROR_INVALID_PASSWORD = IUserStorage::ERROR_INVALID_PASSWORD;
    const ERROR_EMAIL_EXISTS = IUserStorage::ERROR_EMAIL_EXISTS;

    /*
     * adds customer to persistent storage
     * @param customer: instance of Customer class
     * @param password: password of new customer's user
     * @returns: void
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_EMAIL_EXISTS)
     */
    public function addCustomer(Customer $customer, $password);
    /*
     * @param email: customer's email
     * @returns: corresponding instance of Customer class
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL)
     */
    public function getCustomer($email);
    /*
     * @param email: customer's email
     * @param password: customer's password
     * @returns: corresponding instance of Customer class
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL,
     * ERROR_INVALID_PASSWORD)
     */
    public function getAuthCustomer($email, $password);
    /*
     * Search Customers by query
     * @param query: what to search for
     * @param matchType: how to search, ignored for now
     * @returns: array of matched Customer objects
     * @throws StorageException if failed on storage problem
     */
    public function findCustomers($query, $matchType = NULL);
    /*
     * Save all the changes made for the authentificated customer to persistent
     * storage
     * @param authCustomer: instance of AuthentificatedCustomer to save
     * @returns: void
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL,
     * ERROR_INVALID_PASSWORD, ERROR_EMAIL_EXISTS)
     */
    public function saveAuthCustomer(AuthentificatedCustomer $authCustomer);
}
