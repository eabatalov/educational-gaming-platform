<?php

/**
 * This exception will be thrown by all
 * *Storage classes to report errors on storage level.
 * So StorageException is easy to catch,
 * no need to think about different possible types.
 * 
 * If you really want to check for actual source of error
 * you'll need to examine getPrevious() or getCode()
 *
 * @author eugene
 */
class StorageException extends Exception {
    //Error consts you may find in getCode()
    const ERROR_CONNECTION_PROBLEMS = 0x8000001;
    const ERROR_CONCURRENT_MODIFICATION = 0x8000002;
    const ERROR_INTERNAL_INCONSISTENCY = 0x8000003;
}
