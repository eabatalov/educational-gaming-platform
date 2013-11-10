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
     * @returns AuthentificatedUser instance if current user if not guest
     *  else returns NULL
     */
    static  public function authUser() {
        if (isset(Yii::app()->user->authUser))
            return Yii::app()->user->authCustomer->getUser();
        else return NULL;
    }

    /*
     * @returns AuthentificatedCustomer instance if current user if not guest
     *  else returns NULL
     */
    static public function authCustomer() {
        if (isset(Yii::app()->user->authUser))
            return Yii::app()->user->authCustomer;
        else return NULL;
    }
}