<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// change the following paths if necessary
$yii= dirname(__FILE__).require(dirname(__FILE__).'/protected/config/local-path.php');
$yii=$yii."yii.php";
//$yii=dirname(__FILE__).'/../yii/framework/yii.php'; 
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
if (isset($_GET['debug'])) defined('YII_DEBUG') or define('YII_DEBUG',$_GET['debug']);
else defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
//are we on testing server
defined('YII_TESTING') or define('YII_TESTING',false);

require_once($yii);
Yii::createWebApplication($config)->run();
