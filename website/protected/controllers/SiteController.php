<?php

/**
 * SiteController is the default controller to handle user requests.
 */
class SiteController extends EGPWebFrontendController
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
        $redirect = Yii::app()->getRequest()->getParam('bar', false);
        if ($redirect !== false)
            $this->render("Landing");
        else
            $this->redirect('https://angel.co/learzing');
    }

    /**
     * Action to render website search page
     */
    public function actionSearch()
    {
        $this->render("Search");
    }
}
