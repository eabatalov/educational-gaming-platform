<?php

/**
 * Array utils : wrappers for different operations with arrays.
 * Primaraly used to overcome limitations of PHP ver < 4.4
 *
 * @author eugene
 */
class AU {

    public static function arrayValue(Array $array, $key) {
        return $array[$key];
    }

    public static function arrayHasKey(Array $array, $key) {
        return isset($array[$key]);
    }
}
