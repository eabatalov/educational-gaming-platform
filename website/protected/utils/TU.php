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
                                    $message = NULL, $errorCode = 0) {
        if ($condition) {
            throw new $ExceptionClass($message, $errorCode);
        }
    }

    /*
     * throwif with inversed $condition
     */
    static public function throwIfNot($condition, $ExceptionClass = TU::EXCEPTION,
                                        $message = NULL, $errorCode = 0) {
        if (!$condition) {
            throw new $ExceptionClass($message, $errorCode);
        }
    }

    /*
     * Format exception data depending on current debug mode
     * and make it ready for rendering to HTML page
     */
    static public function htmlFormatExceptionForUser(Exception $ex) {
        $message =
            'Sorry. Error occured.' . PHP_EOL .
            'Error code: ' . (string)$ex->getCode() . PHP_EOL .
            'Message: ' . $ex->getMessage() . PHP_EOL .
            'Please try again or contact support for help.' . PHP_EOL;
        if (YII_DEBUG)
            $message = $message .
                'Exception backtrace (displayed in dev mode only):' . PHP_EOL .
                CVarDumper::dumpAsString($ex->getTrace());

        $formatter = new CFormatter();
        return $formatter->formatNtext($message);
    }

    /*
     * Throw InvalidArgumentException if array key doesn't exist.
     * @throws InvalidArgumentException
     */
    public static function getValueOrThrow($key, $array, $errorMessage = NULL) {
        if (isset($array[$key])) {
            return $array[$key];
        } else {
            $errorMessage = $errorMessage != NULL ? $errorMessage :
                "No key " . var_export($key, TRUE) . " in array";
            throw new InvalidArgumentException($errorMessage);
        }
    }

    //Exceptions names constants for convenience
    const EXCEPTION = 'Exception';
    const INVALID_ARGUMENT_EXCEPTION = 'InvalidArgumentException';
    const INTERNAL_ERROR_EXCEPTION = 'InternalErrorException';
}