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
        foreach ($this->headers as $header)
            header($header);
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
        $message = NULL;
        if (YII_DEBUG && ($exception != NULL)) {
            $message = $exception->getMessage() . PHP_EOL .
                "Error code: " . $exception->getCode();
        }
        $this->sendResponse(self::RESULT_INTERNAL_ERROR, $message);
    }

    /*
     * Sends AUTHORIZATION_FAILED to client if user is not authentificated
     */
    protected function requireAuthentification()
    {
        if (LearzingAuth::getCurrentAccessToken() == NULL)
            $this->sendResponse (self::RESULT_AUTHORIZATION_FAILED,
                'Current user should be authentificated');
    }

    /*
     * Sends AUTHORIZATION_FAILED to client if user is authentificated
     */
    protected function requireNoAuthentification()
    {
        if (LearzingAuth::getCurrentAccessToken() != NULL)
            $this->sendResponse (self::RESULT_AUTHORIZATION_FAILED,
                "Current user shouldn't be authentificated");
    }

    /*
     * Returns parsed to PHP array current API REQUEST object
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

    protected function addHttpHeaderToResponse($header) {
        $this->headers[] = $header;
    }

    protected function beforeAction($action) {
        parent::beforeAction($action);
        try {
            $request = NULL;
            if (Yii::app()->request->getRequestType() === "GET") {
                $request = Yii::app()->request->getParam("request", NULL);
                if ($request === NULL)
                    throw new InvalidArgumentException(
                        "GET request should contain 'request' parameter with REQUEST JSON object");
            } else {
                $request = Yii::app()->request->getRawBody();
            }
            //echo var_export($request, true);
            $this->request = CJSON::decode($request, TRUE);
            $auth = new LearzingAuth();
            $auth->authenticateRequest();
        } catch(Exception $ex) {
            $this->sendInternalError($ex);
        }
        return TRUE;
    }

    private $request = NULL;
    private $headers = array();

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