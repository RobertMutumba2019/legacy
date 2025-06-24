<?php
ob_start();

error_reporting(null);

//include "../classes/Db.php";
//include "../classes/Feedback.php";		
include __DIR__ . "/../classes/init.inc";
//include "../classes/AuditTrail.inc";	

$t = new BeforeAndAfter();

$email_password = $_POST['email_password'];
$password = $_POST['password'];
$cpassword = $_POST['cpassword'];
$username = $_POST['username'];

$user_id = $_SESSION['CENTENARY_USER_ID'];

$errors = array();
$ajaxArray = array();

function passwordStrength($password){		
	// Validate password strength
	$uppercase = preg_match('@[A-Z]@', $password);
	$lowercase = preg_match('@[a-z]@', $password);
	$number    = preg_match('@[0-9]@', $password);
	$specialChars = preg_match('@[^\w]@', $password);
	
	if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 7) {
	    return true;
	}else{
	    return false;
	}

}

if (passwordStrength($password) && !empty($password)) {
    $msg_pass = "Password should be at least 7 characters in length and should include at least an upper case letter(A-Z), lower case letter(a-z), number(0-9), and special character.(@ ? / # $ %) ";
    $errors[] = "$msg_pass";
} elseif ($password != $cpassword) {
    $errors[] = "New Password does not match the confirmed Password";
}
			
if($t->isThereEdit("sysuser", ["user_name"=>$username, "user_id"=>$user_id])){
	//$errors[] = "username $username already exists";
}
			
if($errors === []){
	$db = new Db();
    $ep = $t->penc($email_password);
	$select = $db->select("SELECT * FROM sysuser WHERE user_id = $user_id AND user_password = '$ep';");

	if($db->num_rows()){
		extract($select[0]);
		//$db = new Db();
		$x = new Db();
		if(empty($password)){
			$x = $db->update("sysuser", ["user_forgot_password"=>0, "user_last_changed"=>time()],["user_id"=>$user_id]);
		}else{
    		$p = $t->penc($password);
			$x = $db->update("sysuser", ["user_forgot_password"=>0, "user_password"=>$p,"user_last_changed"=>time()],["user_id"=>$user_id]);
		}

		$ajaxArray['message']='Successfully Changed';
		$ajaxArray['details']='Successfully Changed';

		$msg = array();
        $msg[] = "Hello $user_surname $user_othername, ";
        $msg[] = "";
        $msg[] = "Your Login credentials have been successfully changed by :".$t->full_name(user_id());
        $msg[] = "";
        $msg[] = "Username: $user_name";

        $msg[] = empty($password) ? "Your Password is not changed" : "Password: $password";
       
        $mssg[] = "";
        $message[] = "Thank You.";

        $to = $user_email;
        $subject = "CENTENARY ACCOUNT CHANGES";
        $message = implode("\r \n <br/>", $msg);
        Feedback::sendmail($to,$subject,$message,$name);
		$ajaxArray['details']=implode('\n', $errors);
		$ajaxArray['message'] = 'Successfully Saved';
		session_destroy();
	}else{
		$ajaxArray['message']='Error';
		$ajaxArray['details']="Username or Old Password donot match ".$user_id;
	}
}else{
	$ajaxArray['message']='Error';
	$ajaxArray['details']=implode('\n', $errors); }
          
echo json_encode($ajaxArray);