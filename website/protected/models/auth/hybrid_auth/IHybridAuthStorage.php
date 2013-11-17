<?php

/**
 * Implements hybrid auth persistent state
 *
 * @author eugene
 */
Interface IHybridAuthStorage {

    /*
     * @throws StorageException
     */
    public function saveHAuthRecord(HybridAuthRecord $record);
    /*
     * Will try to frind user for (loginProvider, loginProviderIdentifier) pair
     *  if failed will try to find a user with email.
     * @returns: AuthentificatedUser instance which has hybrid auth record for
     *   $loginProvider, $loginProviderIdentifier pair
     * @throws: InvalidArgumentException if no such AuthentificatedUser,
     *   StorageException
     */
    public function getAuthentificatedUser($loginProvider, $loginProviderIdentifier,
                                            $email = NULL);
    /*
     * @returns: array(HybridAuthRecord)
     */
    public function getUserHAuthRecords($userId);
}