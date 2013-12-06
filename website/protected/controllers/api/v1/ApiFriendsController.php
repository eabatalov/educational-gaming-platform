<?php

/**
 * friends API service request handler
 *
 * @author eugene
 */
class ApiFriendsController extends ApiController {

    public function actionGetFriends() {
        try {
            $this->requireAuthentification();
            $userId = TU::getValueOrThrow("userid", $this->getRequest());
            $customerStorage = new PostgresCustomerStorage();
            $friends = $customerStorage->getCustomerFriends($userId);

            $friendsUserApi = array();
            foreach ($friends as $friendCustomer) {
                $friendUser = $friendCustomer->getUser();
                $friendUserApi = new UserApiModel();
                $friendUserApi->initFromUser($friendUser);
                $friendsUserApi[] = $friendUserApi;
            }
            $this->sendResponse(self::RESULT_SUCCESS, NULL, "friends", $friendsUserApi);

        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            if ($ex->getCode() == ICustomerStorage::ERROR_NO_CUSTOMER_WITH_SUCH_ID)
                $message = "No user with such id";
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    public function actionAddFriend() {
        try {
            $this->requireAuthentification();
            $customerStorage = new PostgresCustomerStorage();

            $friendId = TU::getValueOrThrow("userid", $this->getRequest());
            $friend = $customerStorage->getCustomerById($friendId);

            $authCustomer =
                $customerStorage->getAuthCustomer($this->getUserEmail(),
                        $this->getUserPassword());
            $authCustomer->addFriend($friend);

            $customerStorage->saveAuthCustomer($authCustomer);

            $this->sendResponse(self::RESULT_SUCCESS);

        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            if ($ex->getCode() == ICustomerStorage::ERROR_NO_CUSTOMER_WITH_SUCH_ID ||
                $ex->getCode() == ICustomerStorage::ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL ||
                $ex->getCode() == IUserStorage::ERROR_INVALID_PASSWORD)
                    $message = "Invalid user id, email or password";
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    public function actionDeleteFriend() {
        try {
            $this->requireAuthentification();
            $customerStorage = new PostgresCustomerStorage();

            $friendId = TU::getValueOrThrow("userid", $this->getRequest());
            $friend = $customerStorage->getCustomerById($friendId);

            $authCustomer =
                $customerStorage->getAuthCustomer($this->getUserEmail(),
                        $this->getUserPassword());
            $authCustomer->delFriend($friend);

            $customerStorage->saveAuthCustomer($authCustomer);

            $this->sendResponse(self::RESULT_SUCCESS);

        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            if ($ex->getCode() == ICustomerStorage::ERROR_NO_CUSTOMER_WITH_SUCH_ID ||
                $ex->getCode() == ICustomerStorage::ERROR_NO_CUSTOMER_WITH_SUCH_EMAIL ||
                $ex->getCode() == IUserStorage::ERROR_INVALID_PASSWORD)
                    $message = "Invalid user id, email or password";
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }
}