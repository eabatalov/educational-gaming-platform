<?php
/**
 * Controller is responsible for handling manual login/signup requests
 * when user enters all the login/signup data manually.
 * @author eugene
 */
class UserController extends EGPControllerBase {

    private static $PAGE_NAME_SIGNUP = "SignUp";
    private static $PAGE_NAME_LOGIN = "Login";
    private static $URL_REDIRECT_ON_SUCCESS = "/";

    /*
     * Handler for manual user registration through entering email and password
     */
    public function actionSignup() {
        if (!Yii::app()->user->isGuest)
            $this->redirect (self::$URL_REDIRECT_ON_SUCCESS);

        $this->pageTitle = self::$PAGE_NAME_SIGNUP;
        $model = AuthentificatedUser::createEmpty();

        if (Yii::app()->request->isPostRequest) {
            try {
                $model->attributes = $this->getPOSTVal('AuthentificatedUser');
                $model->setRole(UserRole::CUSTOMER);
                $model->setIsActive(TRUE);

                if ($model->validate()) {
                    $userStorage = new PostgresUserStorage();
                    $userStorage->addUser($model, $model->getPassword());

                    $identity = new AuthIdentity($model->getEmail(), $model->getPassword());
                    if (!$identity->authenticate() ||
                        !AuthUtils::login($identity))
                        throw new Exception($identity->errorMessage, $identity->errorCode);

                    $this->redirect(self::$URL_REDIRECT_ON_SUCCESS);
                }
            } catch(Exception $ex) {
                if ($ex instanceof InvalidArgumentException &&
                    $ex->getCode() == IUserStorage::ERROR_EMAIL_EXISTS) {
                        $model->addError('email', 'User with such email already exists');
                } else $model->addFatalError($ex);
            }
        } else if (Yii::app()->request->isAjaxRequest) {
            //Process ajax validation request here
            //TODO
        }

        $this->render('Signup', array('model' => $model));
    }

    /*
     * Handler for manual user login through entering email and password
     */
    public function actionLogin() {
        /*if (!Yii::app()->user->isGuest)
            $this->redirect (self::$URL_REDIRECT_ON_SUCCESS);*/

        $this->pageTitle = self::$PAGE_NAME_LOGIN;
        $model = AuthentificatedUser::createEmpty();

        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $this->getPOSTVal('AuthentificatedUser');
                $model->setRole(UserRole::CUSTOMER);
                $model->setIsActive(TRUE);

                if ($model->validate(array('email', 'password'))) {
                    $identity = new AuthIdentity($model->getEmail(), $model->getPassword());
                    if ($identity->authenticate() &&
                        AuthUtils::login($identity)) {
                        $this->redirect(self::$URL_REDIRECT_ON_SUCCESS);
                    } else
                        $model->addHtmlFormattedError ('Email or password', $identity->errorMessage);
                }
        }
        $this->render('Login',  array('model' => $model));
    }

    /*
     * Handler for logout action. Both for manual and hybrid login.
     */
    public function actionLogout() {
        if (!Yii::app()->user->isGuest)
            Yii::app()->user->logout();
        $this->redirect(self::$URL_REDIRECT_ON_SUCCESS);
    }
}