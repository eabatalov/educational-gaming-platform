<?php

/**
 * Description of UserController
 *
 * @author eugene
 */
class UserController extends EGPControllerBase
{
    
    public function actionIndex()
    {
        $this->actionRegister();
    }
    
    public function actionRegister($name="", $pass="")
    {
        $this->pageTitle = "register";
        if(Yii::app()->request->isPostRequest)
        {
            $this->render("RegistrationSuccess");
        } else
            $this->render("RegistrationForm");
    }

}
