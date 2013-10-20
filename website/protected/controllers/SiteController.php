<?php

/**
 * SiteController is the default controller to handle user requests.
 */
class SiteController extends EGPControllerBase
{

    function __construct($id,$module=null)
    {
        parent::__construct($id, $module);
        $this->pageTitle = "home";
    }
    
    /**
     * Index action is the default action in a controller.
     */
    public function actionIndex()
    {
        $this->render("Landing");
    }
}