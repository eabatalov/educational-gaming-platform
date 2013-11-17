<?php

/**
 * @author eugene
 */
$protectedDir = \dirname(__FILE__)."/../";

set_include_path(get_include_path() . PATH_SEPARATOR .
        $protectedDir ."/utils/" . PATH_SEPARATOR .
        $protectedDir ."/controllers/" . PATH_SEPARATOR .
        $protectedDir ."/controllers/api/" . PATH_SEPARATOR .
        $protectedDir ."/modules/hybridauth/controllers/" . PATH_SEPARATOR .
        $protectedDir ."/models/" . PATH_SEPARATOR .
        $protectedDir ."/models/api/" . PATH_SEPARATOR .
        $protectedDir ."/models/auth/" . PATH_SEPARATOR .
        $protectedDir ."/models/auth/hybrid_auth/" . PATH_SEPARATOR .
        $protectedDir ."/models/auth/hybrid_auth/postgres/" . PATH_SEPARATOR .
        $protectedDir ."/models/entities/" . PATH_SEPARATOR .
        $protectedDir ."/models/services/" . PATH_SEPARATOR .
        $protectedDir ."/models/services/postgres/" . PATH_SEPARATOR
);

define('YII_DEBUG',true);
require_once $protectedDir . '/external/yii/framework/yii.php';
require_once $protectedDir . '/modules/hybridauth/HybridauthModule.php';

require_once('PHPUnit/Runner/Version.php');
require_once('PHPUnit/Autoload.php');
?>