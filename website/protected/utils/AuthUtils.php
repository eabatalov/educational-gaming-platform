<?php

/**
 * @author eugene
 */
class AuthUtils {
    //TODO Remove shit code from here
    //Don't save stuff in cookies until we know that there is no user info
    //saved in them if we enable them
    /*
     * Login Yii user and setup his parameters
     */     
    static public function login(IUserIdentity $identity, $duration = 0) {
        $result = Yii::app()->user->login($identity, $duration);
        if ($result) {
            //Set auth using cookie here?
            $customerStorage = new PostgresCustomerStorage();
            $authUser = Yii::app()->user->authUser;
            $authCustomer =
                $customerStorage->getAuthCustomer($authUser->getEmail(), $authUser->getPassword());
            Yii::app()->user->setState('authCustomer', $authCustomer);
        }
        return $result;
    }

    /*
     * @returns AuthentificatedUser instance if current user is not guest
     *  else returns NULL
     */
    static  public function authUser() {
        if (isset(Yii::app()->user->authUser))
            return Yii::app()->user->authCustomer->getUser();
        else return NULL;
    }

    /*
     * @returns AuthentificatedCustomer instance if current user is not guest
     *  else returns NULL
     */
    static public function authCustomer() {
        if (isset(Yii::app()->user->authUser))
            return Yii::app()->user->authCustomer;
        else return NULL;
    }

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