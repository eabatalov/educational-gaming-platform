<?php

/**
 * User and login API services request handler
 *
 * @author eugene
 */
class ApiUserController extends ApiController {

    public function actionGetUser()
    {
        try {
            $this->requireAuthentification();
            $userStorage = new PostgresUserStorage();
            if (AU::arrayHasKey($this->getRequest(), "userid")) {
                $userId = AU::arrayValue($this->getRequest(), "userid");
                $user = $userStorage->getUserById($userId);
            } else {
                $user = $userStorage->getAuthentificatedUserByAccessToken(
                    LearzingAuth::getCurrentAccessToken());
            }

            $userApi = new UserApiModel();
            $userApi->initFromUser($user);
            $this->sendResponse(self::RESULT_SUCCESS, NULL,
                $userApi->toArray($this->getFields()));
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
            $newUserApi->initFromArrayOnCreate(TU::getValueOrThrow("user", $this->getRequest()));

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
            $userApi->initFromArrayOnUpdate(TU::getValueOrThrow("user", $this->getRequest()));

            $userStorage = new PostgresUserStorage();
            $user = $userStorage->getAuthentificatedUserByAccessToken(
                LearzingAuth::getCurrentAccessToken());
            $user->setAttributes($userApi->toUserFieldsArray());

            if (AU::arrayHasKey($this->getRequest(), "password")) {
                $passChange = new PasswordChangeApiModel();
                $passChange->initFromArray(AU::arrayValue($this->getRequest(), "password"));
                TU::throwIf($passChange->old !== $user->getPassword(), TU::INVALID_ARGUMENT_EXCEPTION,
                    "Old password is invalid");
                $user->setPassword($passChange->new);
            }

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