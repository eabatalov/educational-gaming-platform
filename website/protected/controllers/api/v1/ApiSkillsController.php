<?php

/**
 * skills API service request handler
 *
 * @author eugene
 */
class ApiSkillsController extends ApiController {

    public function actionGetUserSkills() {
        try {
            $this->requireAuthentification();
            $userIdApi = TU::getValueOrThrow("user_id", $this->getRequest());

            $userSkillsService = new UserSkillsService();
            if (AU::arrayHasKey($this->getRequest(), "skill_id")) {
                $skillIdApi = TU::getValueOrThrow("skill_id", $this->getRequest());
                $userSkills = array();
                $userSkills[] = $userSkillsService->getUserSkill(
                    UserSkillApiModel::userIdFromApi($userIdApi),
                    UserSkillApiModel::skillIdFromApi($skillIdApi));
                 $this->getPaging()->setTotal(1);
            } else {
                $userSkills = $userSkillsService->getUserSkills(
                    UserSkillApiModel::userIdFromApi($userIdApi), $this->getPaging());
            }

            $userSkillsApi = array();
            foreach ($userSkills as $userSkill) {
                $userSkillApi = new UserSkillApiModel();
                $userSkillApi->initFromUserSkill($userSkill);
                $userSkillsApi[] = $userSkillApi->toArray($this->getFields());
            }

            $this->sendResponse(self::RESULT_SUCCESS, NULL, $userSkillsApi, TRUE);
        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }

    public function actionModifyCurrentUserSkill() {
        try {
            $this->requireAuthentification();

            $userStorage = new PostgresUserStorage();
            $user = $userStorage->getAuthentificatedUserByAccessToken(
                LearzingAuth::getCurrentAccessToken());
            $apiValues = $this->getRequest();
            $apiValues["user_id"] = strval($user->getId());

            $userSkillApi = new UserSkillApiModel();
            $userSkillApi->initFromArray($apiValues);
            $userSkillsService = new UserSkillsService();
            $userSkillsService->setUserSkillValue($userSkillApi->toUserSkill());

            $this->sendResponse(self::RESULT_SUCCESS);
        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }
}