<?php



define('SNAME',"CENTENARY");
// ======================= ACTIVE DIRECTORY ============================
define('AD_DOMAIN', '@flaxem.local');
define('AD_DNS_NAME', 'FLAXEM-DC');
define('AD_DN', 'DC=FLAXEM,DC=LOCAL');

//================================================================
define('EMAIL_SMTP_DEBUG', '0');                //2
define('EMAIL_SERVER', 'smtp.gmail.com');       //HOST
define('EMAIL_SMTP_AUTH', TRUE);        
define('EMAIL_PORT', 587);
define('EMAIL_USERNAME', '');
define('EMAIL_PASSWORD', '');
define('EMAIL_SMTP_SECURE', 'tls');
define('EMAIL_SET_FROM', '');
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

session_start();
date_default_timezone_set("Africa/Kampala");

include ABSPATH."Db.php";
include ABSPATH."Feedback.php";	

spl_autoload_register(function ($class_name){
	$root = str_replace('\\','/', ABSPATH);
	$file = str_replace('\\','/', $root).''.$class_name.'.inc';
	if(is_readable($file)){
		include $file;
	}
	
});

$n = new Db();

function static_hod_id(){ 	return 56; }
function user_id(){
	return @$_SESSION['CENTENARY_USER_ID'];
}

function return_url($url=""){
	$x = explode("/", $_SERVER["REQUEST_URI"]);
	return "https://".$_SERVER["HTTP_HOST"].'/'.$x[1].'/'.$url;		
}
function display_url($url=""){
	$x = explode("/", $_SERVER["REQUEST_URI"]);
	echo "https://".$_SERVER["HTTP_HOST"].'/'.$x[1].'/'.$url;		
}
function activeClass($p=""){
	$portion = portion(1);

	if($portion == $p){
		echo ' active ';
	}
}

function active($p=""){
	$portion = portion(1);
	$show = false;

	$className = ucwords(str_replace('-', ' ', $p));
	$className = ucwords(str_replace(' ','',$className));
	
	if(class_exists($className)){
		foreach($className::getLinks() as $link){
			extract($link);
			$a = new AccessRights();
			$show = (bool) $a->sectionAccess(user_id(), $link_page, $link_right);
		}
	}else{
		$show = true;
	}
	
	if ($p == $portion && $show == true) {
        echo ' class="active " ';
    } elseif ($p == $portion && $show == false) {
        echo ' class="active hiddenLink" ';
    } elseif ($p != $portion && $show == true) {
        //echo $p.'===>'.$portion;
        //echo ' class=" hiddenLink" ';
    } elseif ($p != $portion && $show == false) {
        echo ' class=" hiddenLink" ';
    }
}

$portion = 0;
function portion($segment){
	$uri_array = explode('/',$_SERVER['REQUEST_URI']);
	$uri_count = count($uri_array);
	$returning_uri = array();

	for($i = 0;$i<$uri_count;$i++)
	{
		if (empty($uri_array[$i]) || $uri_array[$i] == "index.php") {
            unset($uri_array[$i]);
        } else {
            $returning_uri[] = $uri_array[$i];
        }
	}

	if ($segment < count($returning_uri)) {
        return $returning_uri[$segment];
    } else {
        return false;
    }
}

//$u = new Users();

$user_id = user_id();
$db = new Db();
$select = $db->select("SELECT user_forgot_password FROM sysuser WHERE user_id='$user_id'");

if($db->num_rows()){
	if (is_array($select) && isset($select[0]) && is_array($select[0])) {
	extract($select[0]);
	}
	if($user_forgot_password){		

		if( portion(2) == "logout"){

		}elseif(portion(2)!="changed-password"){
			
		}
	}
}

function system_mode($mode=0){

	$production = array(
		'p', 
		'pro', 
		'production'
	);

	if(in_array(strtolower($mode), $production)){
		//error_reporting(null);
		ini_set('log_errors', 1);
		ini_set('display_errors',0);
	}else{
		ini_set('display_errors', 0);
		ini_set('display_startup_errors', 0);
		//error_reporting(null);
	}
}

system_mode('p');


?>