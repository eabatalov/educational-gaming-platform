<?php

/**
 * User and login API services request handler
 *
 * @author eugene
 */
class ApiUserController extends ApiController {

    public function actionLoginUser()
    {
        try {
            $this->requireNoAuthentification();
            $cred = TU::getValueOrThrow("cred", $this->getRequest());
            $userEmail = TU::getValueOrThrow("email", $cred);
            $userPassword = TU::getValueOrThrow("password", $cred);
            /*
             * As website can use our API we should create login session
             * This will be removed once website and API auth mechanisms merged
             */
            $identity = new AuthIdentity($userEmail, $userPassword);
            $identity->authenticate();
            AuthUtils::login($identity);
            $user = AuthUtils::authUser();
            /* End website auth */
            if ($user == NULL)
                throw new InvalidArgumentException("Invalid email or password");
            $userApi = new UserApiModel($user);
            $userApi->initFromUser($user);
            $this->sendResponse(self::RESULT_SUCCESS, NULL, "user", $userApi);
        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    public function actionGetUser()
    {
        try {
            $this->requireAuthentification();
            $userId = TU::getValueOrThrow("userid", $this->getRequest());
            $userStorage = new PostgresUserStorage();
            $user = $userStorage->getUserById($userId);
            $userApi = new UserApiModel();
            $userApi->initFromUser($user);
            $this->sendResponse(self::RESULT_SUCCESS, NULL, "user", $userApi);
        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            if ($ex->getCode() == IUserStorage::ERROR_NO_USER_WITH_SUCH_ID)
                $message = "No user with such id";
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    public function actionRegisterUser()
    {
        try {
            $this->requireNoAuthentification();
            $newUserPassword = TU::getValueOrThrow("password", $this->getRequest());
            $newUserApi = new UserApiModel();
            $newUserApi->initFromArray(TU::getValueOrThrow("user", $this->getRequest()));
            $newUserApi->id = 0; //Required to pass validation. Need use scenario based validation to omit it

            $newUser = $newUserApi->toUser();
            if (!$newUser->validate())
                $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $newUser->getErrors());

            $userStorage = new PostgresUserStorage($newUser);
            $userStorage->addUser($newUser, $newUserPassword);

            $this->sendResponse(self::RESULT_SUCCESS);
        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            if ($ex->getCode() == IUserStorage::ERROR_EMAIL_EXISTS)
                $message = "User with such email already exists";
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    /*
     * Mapped to PUT request
     */
    public function actionModifyUser()
    {
        try {
            $this->requireAuthentification();
            $userApi = new UserApiModel();
            $userApi->initFromArray(TU::getValueOrThrow("user", $this->getRequest()));

            $userStorage = new PostgresUserStorage();
            $user = $userStorage->getAuthentificatedUser(
                $this->getUserEmail(), $this->getUserPassword());
            $user->setAttributes($userApi->toArray());

            if ($this->getUserEmail() != $user->getEmail() &&
                $this->getUserPassword() != $user->getPassword())
                $this->sendResponse(self::RESULT_AUTHORIZATION_FAILED,
                        "You are not allowed to change this user's data");
            if (!$user->validate())
                $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $user->getErrors());

            $userStorage->saveAuthUser($user);
            $this->sendResponse(self::RESULT_SUCCESS);
        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            if ($ex->getCode() == IUserStorage::ERROR_NO_USER_WITH_SUCH_EMAIL ||
                $ex->getCode () == IUserStorage::ERROR_INVALID_PASSWORD)
                $message = "Invalid email or password";
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }
}