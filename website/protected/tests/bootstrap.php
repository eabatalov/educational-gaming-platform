<?php

/**
 * @author eugene
 */
$protectedDir = \dirname(__FILE__)."/../";
$testDir = \dirname(__FILE__) . "/";

set_include_path(get_include_path() . PATH_SEPARATOR .
        $protectedDir ."/utils/" . PATH_SEPARATOR .
        $protectedDir ."/controllers/" . PATH_SEPARATOR .
        $protectedDir ."/controllers/api/" . PATH_SEPARATOR .
        $protectedDir ."/controllers/api/v1/" . PATH_SEPARATOR .
        $protectedDir ."/models/" . PATH_SEPARATOR .
        $protectedDir ."/models/api/" . PATH_SEPARATOR .
        $protectedDir ."/models/api/v1/" . PATH_SEPARATOR .
        $protectedDir ."/models/auth/" . PATH_SEPARATOR .
        $protectedDir ."/models/auth/hybrid_auth/" . PATH_SEPARATOR .
        $protectedDir ."/models/auth/hybrid_auth/postgres/" . PATH_SEPARATOR .
        $protectedDir ."/models/entities/" . PATH_SEPARATOR .
        $protectedDir ."/services/" . PATH_SEPARATOR .
        $protectedDir ."/services/postgres/" . PATH_SEPARATOR .
        $protectedDir ."/services/auth/" . PATH_SEPARATOR .
        $protectedDir ."/services/auth/hybrid_auth/" . PATH_SEPARATOR .
        $protectedDir ."/services/auth/hybrid_auth/postgres/" . PATH_SEPARATOR .
        $testDir . "protected/services/postgres/" . PATH_SEPARATOR
);

define('YII_DEBUG',true);
require_once $protectedDir . '/external/yii/framework/yii.php';

require_once('PHPUnit/Runner/Version.php');
require_once('PHPUnit/Autoload.php');
?>