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
            $user = $this->getUserById($userId); //as inefficient $id validation
            $friendsStorage = new PostgresFriendsStorage();
            $friends = $friendsStorage->getUserFriends($userId, $this->getPaging());

            $friendsApi = array();
            foreach ($friends as $friend) {
                $friendApi = new UserApiModel();
                $friendApi->initFromUser($friend);
                $friendsApi[] = $friendApi->toArray($this->getFields());
            }
            $this->sendResponse(self::RESULT_SUCCESS, NULL, $friendsUserApi, TRUE);

        } catch (InvalidArgumentException $ex) {
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $ex->getMessage());
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    public function actionAddFriend() {
        try {
            $this->requireAuthentification();
            $friendsStorage = new PostgresFriendsStorage();
            
            $currentUser = $friendsStorage->getAuthentificatedUserByAccessToken(
                LearzingAuth::getCurrentAccessToken());
            $currentUserId = $currentUser->getId();
            $friendId = TU::getValueOrThrow("userid", $this->getRequest());
            $friend = $friendsStorage->getUserById($friendId); //Rude user id validity check

            TU::throwIf($friendsStorage->hasFriend($currentUserId, $friendId),
                TU::INVALID_ARGUMENT_EXCEPTION, "User has friend with id " .
                    strval($friendId) . " yet");

            $friendsStorage->addFriend($currentUserId, $friendId);

            $this->sendResponse(self::RESULT_SUCCESS);

        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            if ($ex->getCode() === self::ERROR_NO_USER_WITH_SUCH_ID) {
                $message = "Invalid friend id";
            }
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    public function actionDeleteFriend() {
        try {
            $this->requireAuthentification();
            $friendsStorage = new PostgresFriendsStorage();
            
            $currentUser = $friendsStorage->getAuthentificatedUserByAccessToken(
                LearzingAuth::getCurrentAccessToken());
            $currentUserId = $currentUser->getId();
            $friendId = TU::getValueOrThrow("userid", $this->getRequest());
            $friend = $friendsStorage->getUserById($friendId); //Rude user id validity check

            TU::throwIfNot($friendsStorage->hasFriend($currentUserId, $friendId),
                TU::INVALID_ARGUMENT_EXCEPTION, "User doesn't have friend with id " .
                    strval($friendId) . " to delete");

            $friendsStorage->delFriend($currentUserId, $friendId);

            $this->sendResponse(self::RESULT_SUCCESS);

        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            if ($ex->getCode() === self::ERROR_NO_USER_WITH_SUCH_ID) {
                $message = "Invalid friend id";
            }
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }
}