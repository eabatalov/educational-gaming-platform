<?php

/**
 * @author eugene
 */
class AuthUtils {
    //TODO use CPasswordHelper for password related stuff
    //FIXME this password has limit on number of occurences of every char in it. It is one.
    /*
     * @returns: random string suitable to be a password
     */
    static public function genPassword() {
        $length = 16;
        $chars = array_merge(range(0,9), range('a','z'), range('A','Z'));
        shuffle($chars);
        $password = implode('', array_slice($chars, 0, $length));
        return $password;
    }
}
