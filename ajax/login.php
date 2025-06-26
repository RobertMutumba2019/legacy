<?php
error_reporting(null);

include_once __DIR__ . "/classes/init.php";	
$system_name = "CENTENARY";
$xx = array(); 
if(1 !== 0){
                        
    $username = $_POST['username'];
    $password = $_POST['password'];
   // $captcha = $_POST['captcha'];

    if(!empty($username) && !empty($password)){

        if(0 !== 0){
            //Feedback::error("Entered Captcha code does not match");
            $xx['message']="Error";
        }else{
            $us = new Users();
            $p = $us->penc($password);

            $ad = new ActiveDirectory();
            $ad_results = $ad->login($username, $password);                        
                
            if($ad_results['status']){
                //print_r($ad_results['message']);

                $name = $ad_results['message']['name'];
                $surname = $othername = "";
                if($name){
                    $names = explode('.', $name);
                    $surname = $names[0];

                    if (count($names)>=2) {
                        $othername = $names[1];
                    }
                }
                $email = $ad_results['message']['email'];
                $ur = $ad_results['message']['user_role'];
                $user_id = $ad_results['message']['user_id'];

                $db = new Db();
                //$select = $db->select("SELECT * FROM sysuser WHERE user_name = '$username' AND user_email ");

                $select = $db->select("SELECT * FROM sysuser WHERE user_email = '$email' AND user_id = '$user_id'");

                if($db->num_rows()){
                    if (is_array($select) && isset($select[0]) && is_array($select[0])) {
                    extract($select[0]);
                    }
                }else{                      
                    $time = time();          
                    $db->insert("sysuser", [
                        //"check_number"=>$username,     
                        "user_surname"=>$surname,
                        "user_othername"=>$othername,
                        "user_email"=>$email,
                        //"user_gender"=>'',
                        "user_role"=>1084,
                        "user_name"=>$email, 
                        "user_status"=>1,       
                        //"user_password"=>$this->penc($password),
                        "user_date_added"=>$time,
                        //"user_added_by"=>$user_id,
                        "user_forgot_password"=>0,
                        "user_active"=>1,
                    ]);

                    $select = $db->select("SELECT * FROM sysuser WHERE user_email = '$email'");
                    if($db->num_rows()){
                        if (is_array($select) && isset($select[0]) && is_array($select[0])) {
                        extract($select[0]);
                        }
                    }
                }

                //Feedback::success("Successfully Logged in. Please wait while redirecting");

                $query_string = (empty(end(explode("=", $_SERVER['QUERY_STRING']))))? "dashboard/index":end(explode("=", $_SERVER['QUERY_STRING']));

               //FeedBack::redirect(return_url().$query_string);
                $_SESSION[$system_name.'_USER_ID'] = $user_id;
                $_SESSION[$system_name.'_ROLE_ID'] = $user_id;
                //"user_role"=>$ur,
                $update = $db->update("sysuser", ["user_forgot_password"=>0, "user_last_logged_in"=>time(), "user_online"=>1, "user_last_active"=>time(), "user_session"=>Feedback::ip_address()], ["user_id"=>$user_id]);

                AuditTrail::registerTrail("LOGIN-SUCCESSFULL", $db_id="",  "LOGIN-SUCCESSFULL", "LOGIN-SUCCESSFULL");
                $_SESSION['LOGIN-ATTEMPTS'] = 0;
                
                $xx['message']="Success";                         
            }else{
                $xx['message']="Error";
                //Feedback::warning("Wrong Username or Password");

                AuditTrail::registerTrail("LOGIN-FAILED", $db_id="",  "LOGIN-FAILED", "LOGIN-FAILED: username_entered->$username AND password->$password");
                //print_r($_SESSION['LOGIN-ATTEMPTS']);
                // $username;
                if(empty($_SESSION[$system_name.'_LOGIN-ATTEMPTS'])){
                    $_SESSION[$system_name.'_LOGIN-ATTEMPTS'] = 1;
                }else{
                    $_SESSION[$system_name.'_LOGIN-ATTEMPTS'] += 1;
                }

                $xx['message']="Error";
            }
        }
    }else{
        //Feedback::error("Please Enter Username, Password & Captcha Code");
        $xx['message']="Error";
    }
}else{
  $xx['message']="Error";
  //Feedback::error("Please Enter Username, Password & Captcha Code");
}

echo json_encode($xx);