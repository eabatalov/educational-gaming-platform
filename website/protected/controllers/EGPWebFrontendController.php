<?php

/**
 * Basic functionality needed only for controllers which render HTML views to clients
 * @author eugene
 */
class EGPWebFrontendController extends EGPControllerBase {
    public static $DEFAULT_PAGE_TITLE = "Learzing";

    public function __construct($id, $module=null)
    {
        parent::__construct($id, $module);
        $this->pageTitle = self::$DEFAULT_PAGE_TITLE;
    }

    protected function sendBadRequest(\InvalidArgumentException $exception = NULL) {
        $message = $exception != NULL ? $exception->getMessage() : NULL;
        $code = $exception != NULL ? $exception->getCode() : NULL;

        Yii::app()->getErrorHandler()->handle(
            new CExceptionEvent($this,
                new CHttpException(HTTPStatusCodes::HTTP_BAD_REQUEST, $message, $code)));
        Yii::app()->end();
    }

    protected function sendInternalError(\Exception $exception = NULL) {
        $message = $exception != NULL ? $exception->getMessage() : NULL;
        $code = $exception != NULL ? $exception->getCode() : NULL;

        Yii::app()->getErrorHandler()->handle(
            new CExceptionEvent($this,
                new CHttpException(HTTPStatusCodes::HTTP_INTERNAL_SERVER_ERROR, $message, $code)));
        Yii::app()->end();
    }

    protected function sendUnAuthorized($message,  $errorCode = NULL) {
        Yii::app()->getErrorHandler()->handle(
            new CExceptionEvent($this,
                new CHttpException(HTTPStatusCodes::HTTP_UNAUTHORIZED,
                    $message, $errorCode)));
        Yii::app()->end();
    }

    protected $pageTitle;
}
