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
     */
    public function getCustomer($user);
    /*
     * the same as for getCustomer but with authentificated instances
     */
    public function getAuthCustomer($authUser);
    /*
     * Search Customers by query
     * @param query: name or surname prefix
     * @param matchType: ignored
     * @returns: array of matched Customer objects
     */
    public function findCustomers($query, $matchType = NULL);
    /*
     * Save all changes made to AuthentificatedCustomer object
     * to persistent storage
     * @param authCustomer: AuthentificatedCustomer object to save
     * @returns: TRUE if success FALSE otherwise.
     */
    public function saveAuthCustomer($authCustomer);
}
