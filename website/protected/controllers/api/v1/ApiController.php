<?php

/**
 * Basic fucntionality for all API handling controllers.
 *
 * @author eugene
 */
class ApiController extends EGPControllerBase {

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
    protected function sendResponse($result, $texts = NULL, $body = NULL, $sendPaging = FALSE)
    {
        assert(is_string($result));
        if ($texts === NULL)
             $texts = self::resultToHumanReadableText ($result);

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

        if ($sendPaging) {
            $pagingApi = new PagingApiModel();
            $pagingApi->initFromPaging($this->paging);
            $paging = ", \"paging\" : " . CJSON::encode($pagingApi);
        } else $paging = "";

        echo
        "{ " .
            "\"status\" : " . $result . ", " .
            "\"texts\" : " . $texts . ", " .
             "\"data\" : " . $body .
             $paging .
        " }";
        
        Yii::app()->end();
    }

    protected function addHttpHeaderToResponse($header) {
        $this->headers[] = $header;
    }

    private $headers = array();

    protected function sendInternalError(Exception $exception = NULL) {
        $message = NULL;
        if (YII_DEBUG && ($exception != NULL)) {
            $message = $exception->getMessage() . PHP_EOL .
                "Error code: " . $exception->getCode();
        }
        $this->sendResponse(self::RESULT_INTERNAL_ERROR, $message);
    }

    protected function sendBadRequest(\InvalidArgumentException $exception = NULL) {
        $message = NULL;
        if (/*YII_DEBUG && */($exception != NULL)) {
            $message = $exception->getMessage() . PHP_EOL .
                "Error code: " . $exception->getCode();
        }
        $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
    }

    protected function sendUnAuthorized($message, $errorCode = NULL) {
        if (YII_DEBUG && ($errorCode != NULL)) {
            $message = $message . PHP_EOL .
                "Error code: " . $errorCode;
        }
        $this->sendResponse(self::RESULT_AUTHORIZATION_FAILED, $message);
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
        return $this->fields;
    }

    /*
     * Returns collection Paging instance which client has requested
     */
    protected function getPaging() {
        return $this->paging;
    }

    protected function beforeAction($action) {
        try {
            parent::beforeAction($action);
            $request = NULL;
            if (Yii::app()->request->getRequestType() === "GET") {
                $request = Yii::app()->request->getParam("request", NULL);
                if ($request === NULL)
                    throw new InvalidArgumentException(
                        "GET request should contain 'request' parameter with REQUEST JSON object");
            } else {
                $request = Yii::app()->request->getRawBody();
            }

            $this->request = CJSON::decode($request, TRUE);

            if (isset($this->request["fields"])) {
                $this->fields = new FieldsFilterApiModel();
                $this->fields->initFromArray($this->request["fields"]);
            }

            if (isset($this->request["paging"])) {
                $pagingApi = new PagingApiModel();
                $pagingApi->initFromArray($this->request["paging"]);
                $this->paging = $pagingApi->toPaging();
            } else {
                $this->paging = new Paging();
            }

            //echo var_export($request, true);
        } catch(InvalidArgumentException $ex) {
            $this->sendBadRequest($ex);
        } catch(Exception $ex) {
            $this->sendInternalError($ex);
        }
        return TRUE;
    }

    private $request = NULL;
    private $fields = NULL;
    private $paging = NULL;

    private static $RESULT_TO_HTTP_STATUS_CODE = Array(
        self::RESULT_SUCCESS => HTTPStatusCodes::HTTP_OK,
        self::RESULT_AUTHORIZATION_FAILED => HTTPStatusCodes::HTTP_UNAUTHORIZED,
        self::RESULT_INVALID_ARGUMENT => HTTPStatusCodes::HTTP_BAD_REQUEST,
        self::RESULT_INTERNAL_ERROR => HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR,
    );

    private static function resultToHttpStatusMessage($result)
    {
        if (isset(self::$RESULT_TO_HTTP_STATUS_CODE[$result])) {
            $code = self::$RESULT_TO_HTTP_STATUS_CODE[$result];
            return HTTPStatusCodes::getMessageForCode($code);
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
        throw new InvalidArgumentException('Unknown API result value: '. $result);
    }

    private static $RESULT_TO_HUMAN_READABLE_TEXT = array(
        self::RESULT_SUCCESS => "Operation has completed successfully",
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