<?php

/**
 * @author eugene
 */
$protectedDir = \dirname(__FILE__)."/../";

set_include_path(get_include_path() . PATH_SEPARATOR .
        $protectedDir ."/models/" . PATH_SEPARATOR .
        $protectedDir . "/models/postgres/" . PATH_SEPARATOR .
        $protectedDir . "/models/hybrid_auth/" . PATH_SEPARATOR .
        $protectedDir . "/modules/hybridauth/controllers/" . PATH_SEPARATOR
        );

define('YII_DEBUG',true);
require_once $protectedDir . '/external/yii/framework/yii.php';
require_once $protectedDir . '/modules/hybridauth/HybridauthModule.php';

require_once('PHPUnit/Runner/Version.php');
require_once('PHPUnit/Autoload.php');
?>