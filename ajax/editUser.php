<?php
error_reporting(null);
include(__DIR__ . "/../classes/init.inc");	
$t = new BeforeAndAfter();
      $check_number = addslashes(ucwords(strtolower($_POST['check_number'])));  
      $surname = addslashes(ucwords(strtolower($_POST['surname'])));      
      $othername = addslashes(ucwords(strtolower($_POST['othername'])));        
      $email = addslashes($_POST['email']);       
      $change = $_POST['change'];    
      $status = $_POST['status'];
      //$telephone = addslashes($_POST['telephone']);

      //$user_department_id = addslashes($_POST['user_department_id']); 
      //$user_branch_id = addslashes($_POST['user_branch_id']);

      
      $user_gender = addslashes($_POST['user_gender']);
      //$user_designation = addslashes($_POST['designation']);
      
      $user_role = addslashes($_POST['user_role']);
      $username = $check_number;
      $user_id = $_POST['user_id'];
      $time = time();
      
      $errors = array();
      $xx = array();


$xx['change']=$change;

      if($t->isThereEdit("sysuser", ["check_number"=>$check_number, "user_id"=>$user_id])){
        $errors[] = "PF number $check_number already exists";
      }
            if($errors === []){

        if($change){
          $password = Feedback::password_generator();
          $pass = $t->penc($password);
        }else{
          $pass = $t->rgf("sysuser", $user_id, "user_id", "user_password");
        }

        $insert = $db->update("sysuser", [
        "user_name"=>$username, 
        "user_surname"=>$surname,
        "user_othername"=>$othername,
        "user_status"=>1,
        //"user_designation"=>$user_designation,
        //"user_branch_id"=>$user_branch_id,
        "user_email"=>$email,
        "user_telephone"=>$telephone,
        "user_gender"=>$user_gender,
        //"user_section_id"=>$user_section_id,
        //"user_department_id"=>$user_department_id,
        "user_password"=>$pass,
        "user_date_added"=>$time,
        "user_added_by"=>$user_id,
        "user_role"=>$user_role,
        "user_forgot_password"=>1,
        "user_active"=>$status,
        "check_number"=>$check_number,
        ],["user_id"=>$user_id]);

        
        $db->error();
        if($insert){

          if($change){
          $msg = array();
                  $msg[] = "Hello $othername, ";
                  $msg[] = "";
                  $msg[] = "Your Account has been editted by :".$t->full_name(user_id());
                  $msg[] = "";
                  $msg[] = "Username: $username";
                  
                  $msg[] = !empty($password) && $change ? "Password: $password" : "Your password has not been changed";
                 
                  $mssg[] = "";
                  $message[] = "Thank You.";

                  $to = $email;
                  $subject = "ACCOUNT RESET";
                  $message = implode("\r \n <br/>", $msg);
                  Feedback::sendmail($to,$subject,$message,$name);

          }

          $xx['message'] = 'Successfully Saved';
        }else{
        	
          $xx['message'] = ('Not Saved, '.$db->error());
        }
      }else{
      	$xx['message']='Error';
        $xx['details']=implode('<br/>', $errors);
      }
          
echo json_encode($xx);