<?php
session_start();
include_once("libs/dbfunctions.php");

require_once('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable("./");
$dotenv->load();


include_once('class/users.php');
//include_once('class/merchant.php');
//include_once('class/notification.php');

// User.login
$op = $_REQUEST['op'];
//user.register
//$op =  $dbobject->DecryptData("pacific",$op);
$operation  = array();
$operation = explode(".", $op);


// getting data for the class method
$params = array();
$params = $_REQUEST;
$data = [$params];


//////////////////////////////
/// callling the method of  the class
$foo = new $operation[0];
echo call_user_func_array(array($foo, trim($operation[1])), $data);
//}else
//{
//	echo "invalid token";
//}