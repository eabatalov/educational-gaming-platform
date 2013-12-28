<?php
/**
 * Controller is responsible for handling manual login/signup requests
 * when user enters all the login/signup data manually.
 * @author eugene
 */
class UserController extends EGPWebFrontendController {

    private static $PAGE_NAME_SIGNUP = "SignUp";
    private static $PAGE_NAME_LOGIN = "Login";
    private static $PAGE_NAME_USER = "User";
    private static $URL_REDIRECT_ON_SUCCESS = "/";

    /*
     * Handles request to display website signup page
     */
    public function actionSignup() {
        if (!LearzingAuth::isGuest())
            $this->redirect (self::$URL_REDIRECT_ON_SUCCESS);
        $this->pageTitle = self::$PAGE_NAME_SIGNUP;

        $this->render('Signup');
    }

    /*
     * Handles request to display website login page
     */
    public function actionLogin() {
        if (!LearzingAuth::isGuest())
            $this->redirect (self::$URL_REDIRECT_ON_SUCCESS);
        $this->pageTitle = self::$PAGE_NAME_LOGIN;

        $this->render('Login');
    }

    /*
     * Handles request to display user's profile page
     */
    public function actionShowUserProfile() {
        $this->pageTitle = self::$PAGE_NAME_USER;

        $userid = Yii::app()->request->getParam("userid");
        if ($userid == NULL) {
            $this->requireAuthentification();
            $userSotrage = new PostgresUserStorage();
            $authUser = $userSotrage->getAuthentificatedUserByAccessToken(
                LearzingAuth::getCurrentAccessToken());
            $userid = $authUser->getId();            
        }

        $model = array(
            "userid" => $userid
        );
        $this->render('Profile', $model);
    }
}