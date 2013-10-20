<?php

abstract class EGPControllerBase extends CController
{
    public static $DEFAULT_PAGE_TITLE = "NONE";

    function __construct($id,$module=null)
    {
        parent::__construct($id, $module);
        $this->pageTitle = EGPControllerBase::$DEFAULT_PAGE_TITLE;
    }
}
