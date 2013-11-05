<?php

/**
 * @author eugene
 */
$protectedDir = \dirname(__FILE__)."/../";
//all the paths are relative to website/protected/tests
set_include_path(get_include_path() . PATH_SEPARATOR .
        $protectedDir ."/models/" . PATH_SEPARATOR .
        $protectedDir . "/models/postgres/");

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