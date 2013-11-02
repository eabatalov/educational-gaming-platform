<?php

/**
 * @author eugene
 */

//all the paths are relative to website/protected/tests
set_include_path(get_include_path() . PATH_SEPARATOR .
        "../models/");

require_once \dirname(__FILE__).'/../external/yii/framework/yii.php';

function autoloader($className) {
    //$classPath =
        //"/data/github/educational-gaming-platform/website/protected/models/"
        //. $className;
    print($className . "\n");
    include_once($className . ".php");
}

spl_autoload_register('autoloader'); 
?>