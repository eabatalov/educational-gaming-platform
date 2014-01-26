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
        return Rand::gen(16);
    }
}
