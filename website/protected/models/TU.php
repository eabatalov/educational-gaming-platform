<?php

/**
 * TU - self testing utilities
 *
 * @author eugene
 */
class TU {

    /*
     * Throw exception $ExceptionClass with $message if $condition is TRUE
     * @param condition: what we check
     * @param ExceptionClass: stringified name of exception class to throw
     *          (see helpers here in the bottom)
     * @param message: string to add to throwed exception
     * @returns : nothing
     */
    static public function throwIf($condition, $ExceptionClass = TU::EXCEPTION,
                                    $message = NULL) {
        if ($condition) {
            throw new $ExceptionClass($message);
        }
    }

    /*
     * throwif with inversed $condition
     */
    static public function throwIfNot($condition, $ExceptionClass = TU::EXCEPTION,
                                        $message = NULL) {
        if (!$condition) {
            throw new $ExceptionClass($message);
        }
    }

    //Exceptions names constants for convenience
    const EXCEPTION = 'Exception';
    const INVALID_ARGUMENT_EXCEPTION = 'InvalidArgumentException';
}