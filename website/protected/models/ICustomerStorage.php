<?php

/**
 * Class which provides access to persistent storage of customer information.
 * @author eugene
 */
interface ICustomerStorage {
    //InvalidArgumentException error codes addtional no IUserStorage error codes
    const ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL = IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL;
    const ERROR_NO_CUSTOMER_WITH_SUCH_ID = IUserStorage::ERROR_NO_USER_WITH_SUCH_ID;

    /*
     * Adds customer and its user to persistent storage.
     * Customer's user should be set.
     * @param customer: instance of Customer class
     * @param password: password of new customer's user
     * @returns: void
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_EMAIL_EXISTS, ERROR_INVALID_OBJECT)
     */
    function addCustomerAndUser(Customer $customer, $password);
    /*
     * @param email: customer's email
     * @returns: corresponding instance of Customer class
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL)
     */
    function getCustomer($email);
    /*
     * @param id: customer's user id
     * @returns: corresponding instance of Customer class
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_ID)
     */
    function getCustomerById($id);
    /*
     * @param id: customer's user id
     * @returns: array(Customer) - array of friends
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_ID)
     */
    function getCustomerFriends($id);
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
    function getAuthCustomer($email, $password);
    /*
     * Search Customers by query
     * @param query: what to search for
     * @param matchType: how to search, ignored for now
     * @returns: array of matched Customer objects
     * @throws StorageException if failed on storage problem
     */
    function searchCustomers($query, $matchType = NULL);
    /*
     * Save all the changes made for the authentificated customer to persistent
     * storage. Don't save customer's user changes.
     * @param authCustomer: instance of AuthentificatedCustomer to save
     * @returns: void
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL,
     * ERROR_INVALID_PASSWORD, ERROR_EMAIL_EXISTS, ERROR_INVALID_OBJECT)
     */
    function saveAuthCustomer(AuthentificatedCustomer $authCustomer);
}
