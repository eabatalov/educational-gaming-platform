<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/protected/external/yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

require_once(dirname(__FILE__).'/protected/config/local.config.php');
require_once($yii);
Yii::createWebApplication($config)->run();
