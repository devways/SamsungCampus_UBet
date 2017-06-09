<?php
session_start();
define('URL', '/');
define('WEBROOT',str_replace('index.php','',$_SERVER['SCRIPT_NAME']));
define('ROOT',str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']));
require(ROOT.'core/model.php');
require(ROOT.'core/controller.php');

if(isset($_GET['p'])){
    $params = explode('/',$_GET['p']);
} else {
    $params[0] = '';
}
$controller = !empty($params[0]) ? $params[0] : 'Defaults';
$action = isset($params[1]) && !empty($params[1]) ? $params[1] : 'index';
if (!file_exists('controllers/'.$controller.'Controller.php')){
    require_once('error404.php') ;
}
else{
    require('controllers/'.$controller.'Controller.php');
    $controller = new $controller();
}
if (method_exists($controller, $action.'Action')){
    unset($params[0]);
    unset($params[1]);
    call_user_func_array(array($controller, $action.'Action'),$params);
} else {
    require_once('error404.php');
}
