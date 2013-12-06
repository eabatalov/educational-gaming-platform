<?php

/**
 * Basic fucntionality for all API handling controllers.
 *
 * @author eugene
 */
class ApiController extends CController {

    //Operation results as defined in api specification
    const RESULT_SUCCESS = "SUCCESS";
    const RESULT_AUTHORIZATION_FAILED = "AUTHORIZATION_FAILED";
    const RESULT_INVALID_ARGUMENT = "INVALID_ARGUMENT";
    const RESULT_INTERNAL_ERROR = "INTERNAL_ERROR";

    /*
     * Outputs operation result to user as it is defined in "JSON API response format"
     * @returns: nothing
     * @throws: InvalidArgumentException
     */
    protected function sendResponse($result, $texts = NULL, $body_field = NULL, $body = NULL)
    {
        assert(is_string($result));
        if ($texts === NULL)
             $texts = self::resultToHumanReadableText ($result);
        if ($body_field !== NULL && $body === NULL)
            throw new InvalidArgumentException("body_field != NULL but body === NULL");

        //Output header
        header('HTTP/1.1 ' . self::resultToHttpStatusCode($result) . ' ' .
            self::resultToHttpStatusMessage($result));
        header('Content-type: application/json');
        //Output body
        if (is_array($texts)) {
            //Tweak only for validation errors
            $textsTemp = array();
            foreach ($texts as $fieldErrors)
                foreach ($fieldErrors as $fieldError)
                    $textsTemp[] = $fieldError;
            $texts = $textsTemp;
        } else $texts = array($texts);

        $result = CJSON::encode($result);
        $body = CJSON::encode($body);
        $texts = CJSON::encode($texts);

        echo
        "{ " .
            "result : " . $result . ", " .
            "texts : " . $texts . ", " .
             ($body_field != NULL ? $body_field . " : " . $body : '') .
        " }";
        
        Yii::app()->end();
    }

    /*
     * Call this func when unrecoverable error occured.
     * For example when exception was caught.
     * @returns: nothing
     * @throws: nothing
     */
    protected function sendInternalError(Exception $exception = NULL)
    {
        $this->sendResponse(self::RESULT_INTERNAL_ERROR,
            $exception != NULL ? $exception->getMessage() . PHP_EOL .
                "Error code: " . $exception->getCode() : NULL);
    }

    /*
     * Sends AUTHORIZATION_FAILED to client if user is not authentificated
     */
    protected function requireAuthentification()
    {
        if (!$this->isCurrentUserAuthentificated)
            $this->sendResponse (self::RESULT_AUTHORIZATION_FAILED,
                'Current user should be authentificated');
    }

    /*
     * Sends AUTHORIZATION_FAILED to client if user is authentificated
     */
    protected function requireNoAuthentification()
    {
        if ($this->isCurrentUserAuthentificated)
            $this->sendResponse (self::RESULT_AUTHORIZATION_FAILED,
                "Current user shouldn't be authentificated");
    }

    /*
     * returns: current user's id or NULL of not authentificated
     */
    protected function getUserId()
    {
        return $this->userId;
    }
    
    /*
     * returns: current user's email or NULL of not authentificated
     */
    protected function getUserEmail()
    {
        return $this->userEmail;
    }

    /*
     * returns: current user's password or NULL of not authentificated
     */
    protected function getUserPassword()
    {
        return $this->userPassword;
    }

    /*
     * Returns not parsed request
     */
    protected function getRequest() {
        return $this->request;
    }

    /*
     * Returns fields which client has requested
     * NULL if no fields are filtered
     */
    protected function getFields() {
        return NULL;
    }

    protected function beforeAction($action) {
        parent::beforeAction($action);
        try {
            $json = NULL;
            if (Yii::app()->request->getRequestType() === "GET") {
                $json = Yii::app()->request->getParam("data", NULL);
                if ($json === NULL)
                    throw new InvalidArgumentException("GET request should contain data parameter with json");
            } else {
                $json = Yii::app()->request->getRawBody();
            }
            $this->request = CJSON::decode($json, TRUE);
            //echo var_export($this->request, true);

            if (AuthUtils::authUser() != NULL)
            {
                /*Temporary workaround for website authentification done using php session */
                $this->userId = AuthUtils::authUser()->getId();
                $this->userEmail = AuthUtils::authUser()->getEmail();
                $this->userPassword = AuthUtils::authUser()->getPassword();
                $this->isCurrentUserAuthentificated = TRUE;
            }

            $this->userEmail = TU::getValueOrThrow("email", $this->request);
            $this->userPassword = TU::getValueOrThrow("password", $this->request);
            $userStorage = new PostgresUserStorage();
            try {
                $authUser = $userStorage->getAuthentificatedUser(
                    $this->getUserEmail(), $this->getUserPassword());
                $this->isCurrentUserAuthentificated = TRUE;
            } catch (Exception $ex) {
                $this->isCurrentUserAuthentificated = FALSE;
            }
            //Set application level request data
            $this->request = TU::getValueOrThrow("request", $this->request);
        } catch(Exception $ex) {
            $this->sendInternalError($ex);
        }
        return TRUE;
    }
    /* ============  NON INTERFACE PART OF CLASS DEFINITION  ============ */
    /* Setting up exceptions handling for all the actions here.
       But it doesn't work for some reason. */
    /*public function init() {        
        parent::init();
        Yii::app()->errorHandler->errorAction = $this->actionError();
    }

    public function actionError(){
        $exception = NULL;
        $error = Yii::app()->errorHandler->getError();
        echo var_export($error);
        if ($error != NULL) {
            $exception = new Exception($error['message'], $error['code']);
            $this->sendInternalError($exception);
        }
    }*/

    private $userId = NULL;
    private $userEmail = NULL;
    private $userPassword = NULL;
    private $isCurrentUserAuthentificated = FALSE;
    private $request = NULL;

    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_BAD_REQUEST = 400;
    const HTTP_STATUS_UNAUTHORIZED = 401;
    const HTTP_STATUS_PAYMENT_REQUIRED = 402;
    const HTTP_STATUS_FORBIDDEN = 403;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_INTERNAL_ERROR = 500;
    const HTTP_STATUS_NOT_IMPLEMENTED = 501;

    private static $RESULT_TO_HTTP_STATUS_CODE = Array(
        self::RESULT_SUCCESS => self::HTTP_STATUS_OK,
        self::RESULT_AUTHORIZATION_FAILED => self::HTTP_STATUS_UNAUTHORIZED,
        self::RESULT_INVALID_ARGUMENT => self::HTTP_STATUS_BAD_REQUEST,
        self::RESULT_INTERNAL_ERROR => self::HTTP_STATUS_INTERNAL_ERROR,
    );

    private static $HTTP_STATUS_CODE_TO_HTTP_MESSAGE = Array(
        self::HTTP_STATUS_OK => 'OK',
        self::HTTP_STATUS_BAD_REQUEST => 'Bad Request',
        self::HTTP_STATUS_UNAUTHORIZED => 'Unauthorized',
        self::HTTP_STATUS_PAYMENT_REQUIRED => 'Payment Required',
        self::HTTP_STATUS_FORBIDDEN => 'Forbidden',
        self::HTTP_STATUS_NOT_FOUND => 'Not Found',
        self::HTTP_STATUS_INTERNAL_ERROR => 'Internal Server Error',
        self::HTTP_STATUS_NOT_IMPLEMENTED => 'Not Implemented',
    );

    private static function resultToHttpStatusMessage($result)
    {
        if (isset(self::$RESULT_TO_HTTP_STATUS_CODE[$result])) {
            $code = self::$RESULT_TO_HTTP_STATUS_CODE[$result];
            if (isset(self::$HTTP_STATUS_CODE_TO_HTTP_MESSAGE[$code]))
                return self::$HTTP_STATUS_CODE_TO_HTTP_MESSAGE[$code];
        }
        self::throwUnknownResultValue($result);
    }

    private static function resultToHttpStatusCode($result)
    {
        if (isset(self::$RESULT_TO_HTTP_STATUS_CODE[$result]))
            return self::$RESULT_TO_HTTP_STATUS_CODE[$result];
        self::throwUnknownResultValue($result);
    }

    private static function throwUnknownResultValue($result)
    {
        throw new InvalidArgumentException('Unknown result value: '. $result);
    }

    private static $RESULT_TO_HUMAN_READABLE_TEXT = array(
        self::RESULT_SUCCESS => "Operation is completed successfully",
        self::RESULT_AUTHORIZATION_FAILED => "Current user doesn't have sufficient permissions 
to perform requested operation",
        self::RESULT_INVALID_ARGUMENT => "Passed argument has invalid value",
        self::RESULT_INTERNAL_ERROR => "Internal server error occured",
    );

    private static function resultToHumanReadableText($result)
    {
        if (isset(self::$RESULT_TO_HUMAN_READABLE_TEXT[$result]))
            return self::$RESULT_TO_HUMAN_READABLE_TEXT[$result];
        self::throwUnknownResultValue($result);
    }
}