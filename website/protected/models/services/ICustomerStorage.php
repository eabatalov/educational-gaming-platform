<?php

/**
 * Class which provides access to persistent storage of customer information.
 * @author eugene
 */
interface ICustomerStorage {
    //InvalidArgumentException error codes addtional no IUserStorage error codes
    const ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL = IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL;
    const ERROR_NO_CUSTOMER_WITH_SUCH_ID = IUserStorage::ERROR_NO_USER_WITH_SUCH_ID;
    const ERROR_NO_CUSTOMER_WITH_SUCH_TOKEN = IUserStorage::ERROR_NO_USER_WITH_SUCH_TOKEN;

    /*
     * Adds customer and its user to persistent storage.
     * Customer's user should be set.
     * @param customer: instance of Customer class
     * @param password: password of new customer's user
     * @returns: void
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_EMAIL_EXISTS, ERROR_INVALID_OBJECT)
     */
    function addCustomerAndUser(Customer $customer, $password);
    /*
     * @param email: customer's email
     * @returns: corresponding instance of Customer class
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL)
     */
    function getCustomer($email);
    /*
     * @param id: customer's user id
     * @returns: corresponding instance of Customer class
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_ID)
     */
    function getCustomerById($id);
    /*
     * @param id: customer's user id
     * @returns: array(Customer) - array of friends
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_ID)
     */
    function getCustomerFriends($id);
    /*
     * @param email: customer's email
     * @param password: customer's password
     * @returns: corresponding instance of Customer class
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException
     *  if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL,
     * ERROR_INVALID_PASSWORD)
     */
    function getAuthCustomer($email, $password);
    /*
     * @param accessToken
     * @returns: corresponding instance of Customer class
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException if failed on user's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_TOKEN)
     */
    function getAuthCustomerByAccessToken($accessToken);
    /*
     * Save all the changes made for the authentificated customer to persistent
     * storage. Don't save customer's user changes.
     * @param authCustomer: instance of AuthentificatedCustomer to save
     * @returns: void
     * @throws InternalErrorException if failed on storage problem
     * @throws InvalidArgumentException if failed on customer's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL,
     * ERROR_INVALID_PASSWORD, ERROR_EMAIL_EXISTS, ERROR_INVALID_OBJECT)
     */
    function saveAuthCustomer(AuthentificatedCustomer $authCustomer);
}
