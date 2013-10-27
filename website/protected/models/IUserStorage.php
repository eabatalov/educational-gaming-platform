<?php

namespace model;
/**
 * Description of IUserStorage
 * Class which provides access to persistent storage of user information.
 * Performs very limited business logic - only authorization to perform
 * requested actions.
 * @author eugene
 */
Interface IUserStorage {
    /*
     * @param user: instance of User class
     * @param password: passwrod of new user
     * @returns: void
     */
    public function addUser($user, $password);
    /*
     * @param email: user's email
     * @returns: corresponding instance of class User or NULL if failed
     */
    public function getUser($email);
    /*
     * @param email: user's email
     * @param password: user's password
     * @returns: corresponding instance of class AuthentificatedUser or NULL if failed
     */
    public function getAuthentificatedUser($email, $password);
    /*
     * @param authUser: instance of AuthentificatedUser to login
     * @returns: TRUE if success otherwise FALSE
     */
    public function login($authUser);
    /*
     * @param email: AuthentificatedUser instance to log out
     * @returns: TRUE if logout successed, otherwise FALSE 
     */
    public function logout($authUser);

    /*
     * Save all the changes made for the authentificated user to persistent
     * storage
     * @param authUser: instance of AuthentificatedUser to save
     * @returns TRUE if succeeded, FALSE otherwise
     */
    public function saveUser($authUser);
}