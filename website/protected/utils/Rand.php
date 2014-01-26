<?php

/**
 * Description of RandomUtils
 *
 * @author eugene
 */
class Rand {
    /*
     * @returns string
     */
    public static function gen($length) {
        //FIXME
        $chars = array_merge(range(0,9), range('a','z'), range('A','Z'));
        shuffle($chars);
        $rand = implode('', array_slice($chars, 0, $length));
        return $rand;
    }
}