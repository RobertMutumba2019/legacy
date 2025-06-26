<?php
error_reporting(null);
include_once __DIR__ . "/classes/init.php";	
$t = new BeforeAndAfter();
      $check_number = addslashes(ucwords(strtolower($_POST['check_number'])));  
      $surname = addslashes(ucwords(strtolower($_POST['surname'])));      
      $othername = addslashes(ucwords(strtolower($_POST['othername'])));        
      $email = addslashes($_POST['email']);       
      //$telephone = addslashes($_POST['telephone']);

      //$user_department_id = addslashes($_POST['user_department_id']); 
      //$user_branch_id = addslashes($_POST['user_branch_id']);

      
      $user_gender = addslashes($_POST['user_gender']);
      //$user_designation = addslashes($_POST['designation']);
      
      $user_role = addslashes($_POST['user_role']);

      $username = $check_number;

      $user_id = user_id();
      $time = time();
      
      $errors = array();
      $xx = array();

      if($t->isThere("sysuser", ["check_number"=>$check_number])){
        $errors[] = "PF number $check_number already exists";
      }

      if($errors === []){
        $password = Feedback::password_generator();

        $insert = $db->insert("sysuser", [
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
        "user_password"=>$t->penc($password),
        "user_date_added"=>$time,
        "user_added_by"=>$user_id,
        "user_role"=>$user_role,
        "user_forgot_password"=>1,
        "user_active"=>1,
        "check_number"=>$check_number,
        ]);

        
        echo $db->error();
        if($insert){

          $msg = array();
                  $msg[] = "Hello $othername, ";
                  $msg[] = "";
                  $msg[] = "Your Account has been successfully created by :".$t->full_name(user_id());
                  $msg[] = "";
                  $msg[] = "Username: $username";
                  
                  if(!empty($password)){
                     $msg[] = "Password: $password";
                }
                 
                  $mssg[] = "";
                  $message[] = "Thank You.";

                  $to = $email;
                  $subject = "ACCOUNT CREDENTIALS";
                  $message = implode("\r \n <br/>", $msg);
                  Feedback::sendmail($to,$subject,$message,$name);
                  $xx['message'] = 'Successfully Saved';
        }else{
            $xx['message'] = ('Not Saved, '.$db->error());
        }
      }else{
        $xx['message']='Error';
        $xx['details']=implode('<br/>', $errors);
      }
          
echo json_encode($xx);