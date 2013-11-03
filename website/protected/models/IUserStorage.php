<?php

/**
 * Interface which provides access to persistent storage of user information.
 * Performs very limited business logic - only authorization to perform
 * requested actions.
 * @author eugene
 */
Interface IUserStorage {
    /*
     * adds user to persistent storage, sets its id
     * @param user: instance of User class
     * @param password: password of new user
     * @returns: void
     * @throws StorageException if failed
     */
    public function addUser($user, $password);
    /*
     * @param email: user's email
     * @returns: corresponding instance of User class
     * @throws StorageException if failed
     */
    public function getUser($email);
    /*
     * @param email: user's email
     * @param password: user's password
     * @returns: corresponding instance of AuthentificatedUser class
     * @throws StorageException if failed
     */
    public function getAuthentificatedUser($email, $password);
    /*
     * @param authUser: instance of AuthentificatedUser to login
     * @returns: void
     * @throws StorageException if failed
     */
    public function login($authUser);
    /*
     * @param email: AuthentificatedUser instance to log out
     * @returns: void
     * @throws StorageException if failed
     */
    public function logout($authUser);

    /*
     * Save all the changes made for the authentificated user to persistent
     * storage
     * @param authUser: instance of AuthentificatedUser to save
     * @returns: void
     * @throws StorageException if failed
     */
    public function saveAuthUser($authUser);
}