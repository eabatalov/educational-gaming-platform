<?php
/**
 * Controller is responsible for handling manual login/signup requests
 * when user enters all the login/signup data manually.
 * @author eugene
 */
class UserController extends EGPWebFrontendController {

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
        $model->setRole(UserRole::CUSTOMER);
        $model->setIsActive(TRUE);

        if (Yii::app()->request->isPostRequest) {
            try {
                $model->attributes = $this->getPOSTVal('AuthentificatedUser');
                if (Yii::app()->request->isAjaxRequest) {
                    //Ajax validation request processing
                    echo CActiveForm::validate(array($model));
                    Yii::app()->end();
                } else if ($model->validate()) {
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
                } else 
                    $model->addFatalError($ex);
                if (Yii::app()->request->isAjaxRequest) {
                    echo CActiveForm::validate($model, array('email', ModelObject::FATAL_ERROR_FIELD_NAME));
                    Yii::app()->end();
                }
            }
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
        $model->setRole(UserRole::CUSTOMER);
        $model->setIsActive(TRUE);

        if (Yii::app()->request->isPostRequest) {
            try {
                $model->attributes = $this->getPOSTVal('AuthentificatedUser');
                $attrsToValidate = array('email', 'password');
                if (Yii::app()->request->isAjaxRequest) {
                        //Ajax validation request processing
                        echo CActiveForm::validate(array($model), $attrsToValidate);
                        Yii::app()->end();
                } else if ($model->validate($attrsToValidate)) {
                    $identity = new AuthIdentity($model->getEmail(), $model->getPassword());
                    if ($identity->authenticate() &&
                        AuthUtils::login($identity)) {
                        $this->redirect(self::$URL_REDIRECT_ON_SUCCESS);
                    } else
                        $model->addError(ModelObject::FATAL_ERROR_FIELD_NAME, $identity->errorMessage);
                }
            } catch(Exception $ex) {
                if ($ex instanceof InvalidArgumentException &&
                        ($ex->getCode() == IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL ||
                        $ex->getCode() == IUserStorage::ERROR_INVALID_PASSWORD)) {
                    $model->addError(ModelObject::FATAL_ERROR_FIELD_NAME,
                            'Invalid email or password');
                } else $model->addFatalError($ex);
                if (Yii::app()->request->isAjaxRequest) {
                    echo CActiveForm::validate($model, array('email', 'password', ModelObject::FATAL_ERROR_FIELD_NAME));
                    Yii::app()->end();
                }
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