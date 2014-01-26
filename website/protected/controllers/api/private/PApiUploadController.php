<?php

/**
 * Description of PApiUploadController
 *
 * @author eugene
 */
class PApiUploadController extends ApiController {

    public function actionAvatar() {
        try {
            $this->requireAuthentification();
            $avatarFile = CUploadedFile::getInstanceByName("avatar");
            TU::throwIf($avatarFile === NULL, TU::INVALID_ARGUMENT_EXCEPTION,
                "File should have form name 'avatar'");

            $userStorage = new PostgresUserStorage();
            $user = $userStorage->getAuthentificatedUserByAccessToken(
                LearzingAuth::getCurrentAccessToken());
            $oldName = $user->getAvatar();
            $newName = "avatar_" . strval($user->getId());

            $saveSuccess = $avatarFile->saveAs($this->mkAvatarPath($newName));
            TU::throwIfNot($saveSuccess === TRUE, "Couldn't save uploaded file to server");

            if ($oldName !== $newName) {
                $user->setAvatar($newName);
                $userStorage->saveAuthUser($user);
            }
            $this->sendResponse(self::RESULT_SUCCESS, NULL, array(
                "new_avatar" => $newName
            ));
        } catch(InvalidArgumentException $ex) {
            $this->sendBadRequest($ex);
        } catch(Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    const UPLOADS_PATH = "/media/images/uploads/";
    private function mkAvatarPath($name) {
        $dir = Yii::getPathOfAlias('webroot') . self::UPLOADS_PATH;
        return $dir . $name;
    }
}