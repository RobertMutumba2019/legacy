<?php 

include __DIR__ . "/classes/init.php";

error_reporting(null);


$user_id = $_SESSION['CENTENARY_USER_ID'];
if(empty($user_id)){
	Feedback::refresh(1, return_url()."users/logout");
}

$db = new Db();
$sql = "SELECT * FROM sysuser WHERE user_id = '$user_id'";
$select = $db->select($sql);
extract($select[0]);

if(time() - $user_last_active > 30*60){
    //echo '<script>alert("Timeout You have been inactive for 1 minute");</script>';
    Feedback::error('<h1><center>Time Out<br/> You have been inactive for morethan 5 Minutes</center></h1>');
    Feedback::refresh(1, return_url()."users/logout");
    $update = $db->update("sysuser", ["user_last_logged_in"=>time(), "user_online"=>0], ["user_id"=>$user_id]);
}else{
    $time = time();
    $update = $db->update("sysuser", ["user_last_active"=>$time], ["user_id"=>$_SESSION[$system_name.'_USER_ID'], "user_session"=>Feedback::ip_address()]); 
}

$user_id = $_SESSION['CENTENARY_USER_ID'];
$attach_id = $_POST['attachmentID'];

$href = $_POST['href'];



$portion = explode('/',str_replace(return_url(), '', $href));
//print_r($portion);
$id = $portion[2];

$class = ($portion[0]=="")? 'Dashboard':$portion[0];
$method = ($portion[1]=="")? 'index':$portion[1];

$class_name = convertToStudlyCaps($class);
$method_name = convertToCamelCase($method);

 // echo "$class_name and $method_name and $id";

if(class_exists($class_name)){
	$class = new $class_name;
	
	if(is_callable([$class, $method_name])){
		if ($id !== '' && $id !== '0') {
            $class->id($id);
        }

		$class->$method_name();	


	}else{
		echo '<b>'.$method_name.'</b> METHOD DOES NOT EXIST in CLASS: '.$class;
	}


	
}else{
	echo '<b>'.$class.'</b> CLASS DOES NOT EXIST';
}



function convertToStudlyCaps($string){
	return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
}

function convertToCamelCase($string){
	return lcfirst(convertToStudlyCaps($string));
}