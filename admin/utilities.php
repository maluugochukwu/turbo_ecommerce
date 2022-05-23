<?php
session_start();
header("strict-transport-security: max-age=600");
header('X-Frame-Options: SAMEORIGIN');
header("Pragma: no-cache");
// ini_set( 'session.cookie_httponly', 1 );
// ini_set('session.cookie_secure', 1);
if(!isset($_SESSION['username_sess']))
{
    header('location: logout.php');
}
if($_SESSION['username_sess'] == "")
{
    header('location: logout.php');
}


require_once('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable("./");
$dotenv->load();
include_once("libs/dbfunctions.php");
// use Mailgun\Mailgun;
// use Aws\S3\S3Client;  
// use Aws\Exception\AwsException;


//error_reporting(1);
// Include all classes in the classes folder
//var_dump(glob("class/*.php"));

foreach (glob("class/*.php") as $filename) {
    
	include_once($filename);
}

// User.login
$op = $_REQUEST['op'];
//user.register
//$op =  $dbobject->DecryptData("pacific",$op);
$operation  = array();
$operation = explode(".", $op);


// getting data for the class method
$params = array();
if(count($_FILES) > 0)
{
    $_REQUEST['_files'] = $_FILES;
}

$params = $_REQUEST;
$data   = [$params];
//file_put_contents("kkk.txt",json_encode($_FILES)." -- ".json_encode($_REQUEST));

//////////////////////////////
/// callling the method of  the class
//function util_array_trim(array &$array, $filter = false)
//{
//    
//    array_walk_recursive($array, function (&$value) use ($filter) {
//        $value = trim($value);
//        if ($filter) {
//            $value = mysqli_real_escape_string($value);
//        }
//    });
//
//    return $array;
//}

$foo = new $operation[0];
echo call_user_func_array(array($foo, trim($operation[1])), $data);
//}else
//{
//	echo "invalid token";
//}