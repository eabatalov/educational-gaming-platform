<?php

abstract class EGPControllerBase extends CController
{
    public static $DEFAULT_PAGE_TITLE = "NONE";

    public function __construct($id,$module=null)
    {
        parent::__construct($id, $module);
        $this->pageTitle = EGPControllerBase::$DEFAULT_PAGE_TITLE;
    }

    //Utils
    /*
     * @throws CHttpException
     */
    protected function getPOSTVal($name) {
        assert(is_string($name));

        if (!isset($_POST[$name]))
                throw new CHttpException(404);
        else return $_POST[$name];
    }

    protected function getGETVal($name) {
        assert(is_string($name));

        if (!isset($_GET[$name]))
                throw new CHttpException(404);
        else return $_GET[$name];
        
    }
}
