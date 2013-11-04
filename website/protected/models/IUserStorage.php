<?php

/**
 * Interface which provides access to persistent storage of user information.
 * Performs validation of input on storage level (one which can't be performed
 * on model object level).
 * @author eugene
 */
Interface IUserStorage {

    //InvalidArgumentException error codes
    const ERROR_NO_USER_WITH_SUCH_EMAIL = 0x7000001;
    const ERROR_INVALID_PASSWORD = 0x7000002;
    const ERROR_EMAIL_EXISTS = 0x7000003;

    /*
     * adds user to persistent storage
     * @param user: instance of User class
     * @param password: password of new user
     * @returns: void
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException if failed on user's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_EMAIL_EXISTS)
     */
    function addUser(User $user, $password);
    
    /*
     * @param email: user's email
     * @returns: corresponding instance of User class
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException if failed on user's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_USER_WITH_SUCH_EMAIL)
     */
    function getUser($email);
    /*
     * @param email: user's email
     * @param password: user's password
     * @returns: corresponding instance of AuthentificatedUser class
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException if failed on user's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_USER_WITH_SUCH_EMAIL,
     * ERROR_INVALID_PASSWORD)
     */
    function getAuthentificatedUser($email, $password);

    /*
     * Save all the changes made for the authentificated user to persistent
     * storage
     * @param authUser: instance of AuthentificatedUser to save
     * @returns: void
     * @throws StorageException if failed on storage problem
     * @throws InvalidArgumentException if failed on user's storage level validation
     * Relevant InvalidArgumentException codes: (ERROR_NO_USER_WITH_SUCH_EMAIL,
     * ERROR_INVALID_PASSWORD, ERROR_EMAIL_EXISTS)
     */
    function saveAuthUser(AuthentificatedUser $authUser);
}