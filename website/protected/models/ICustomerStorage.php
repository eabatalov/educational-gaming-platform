<?php

/**
 * Class which provides access to persistent storage of customer information.
 * @author eugene
 */
class ICustomerStorage {
    /*
     * Returns user's Customer instance.
     * Friends are filled in.
     * @param user: customer's user instance
     * @returns: corresponding Customer object
     * @throws StorageException if failed
     */
    public function getCustomer($user);
    /*
     * The same as for getCustomer but with authentificated instances
     */
    public function getAuthCustomer($authUser);
    /*
     * Search Customers by query
     * @param query: what to search for
     * @param matchType: how to search, ignored for now
     * @returns: array of matched Customer objects
     * @throws StorageException if failed
     */
    public function findCustomers($query, $matchType = NULL);
    /*
     * Save all changes made to AuthentificatedCustomer object
     * to persistent storage
     * @param authCustomer: AuthentificatedCustomer object to save
     * @returns: void
     * @throws StorageException if failed
     */
    public function saveAuthCustomer($authCustomer);
}
