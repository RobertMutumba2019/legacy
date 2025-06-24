<?php ob_start(); 

error_reporting(E_ALL);
ini_set('display_errors', 1);




$system_name = "CENTENARY";

//====================SHOWING ONLY THE LAST TWO MONTHS=====================
$month = date('m');
$year = date('Y');
if($month == 1){
    $month = 12;
    $year -= 1;
}else{
    $month -= 1;
}
$tvt = strtotime($year.'-'.$month.'-01');//number for 2 months
define('MONTHS_ACTIVE', $tvt);
//=========================
$start_time = getrusage();


?>
<!DOCTYPE html>
<html>
<?php

include_once __DIR__ . "/classes/init.php";
include_once __DIR__ . "/classes/Users.php";
include_once __DIR__ . "/classes/Requisition.php";
include_once __DIR__ . "/classes/ActiveDirectory.php";
include_once __DIR__ . "/classes/AccessRights.php";
include_once __DIR__ . "/classes/BeforeAndAfter.php";
include_once __DIR__ . "/classes/Groups.php";
include_once __DIR__ . "/classes/AuditTrail.php";
include_once __DIR__ . "/classes/UserRole.php";
include_once __DIR__ . "/classes/ApprovalMatrix.php";
include_once __DIR__ . "/classes/ApprovalMatrix.php";
include_once __DIR__ . "/classes/AllRequisitions.php";

header("Last-Modified: Fri, 27 Aug 2021 ".date("h:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");

?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Stores Requisitioning</title>
    <!-- Favicon-->
    <link rel="shortcut icon" href="<?php echo return_url().'images/centenary-24.png'; ?>">
    <!-- Custom Fonts -->
    <link href="<?php display_url(); ?>css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="<?php display_url(); ?>css/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="<?php display_url(); ?>css/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="<?php display_url(); ?>css/animate-css/animate.css" rel="stylesheet" />

    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="<?php display_url(); ?>css/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />

    <!-- Bootstrap DatePicker Css -->
    <link href="<?php display_url(); ?>css/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="<?php display_url(); ?>css/select.css" />
    <!-- Custom Css -->
    <link href="<?php display_url(); ?>css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="<?php display_url(); ?>css/themes/all-themes.css" rel="stylesheet" />

 <!-- Jquery Core Js -->
    <script type="text/javascript" charset="utf8" src="<?php display_url(); ?>jquery-3.4.1.min.js"></script>

    
    <script type="text/javascript" src="<?php display_url();?>js/script2.js"></script>
    <script type="text/javascript" src="<?php display_url();?>js/sweetalert.js"></script>

    <link href="<?php display_url();?>css/select2.min.css" rel="stylesheet" />
    <script src="<?php display_url();?>js/select2.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="<?php display_url(); ?>css/bootstrap/js/bootstrap.js"></script>

    <script type="text/javascript" src="<?php display_url();?>css/bootstrap-material-datetimepicker/js/material.min.js"></script>
    
    <script type="text/javascript" src="<?php display_url();?>css/bootstrap-material-datetimepicker/js/moment-with-locales.min.js"></script>

    <script type="text/javascript" src="<?php display_url();?>css/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>

    <script type="text/javascript" src="<?php display_url();?>js/script2.js"></script>
    <script src="<?php display_url(); ?>/js/select.js"></script>
    <script>
        $(document).ready(function() {
        $("#itemName").select2();$("#itemName2").select2();$("#office").select2();$("#department").select2();$("#section").select2();$("#territory").select2();$("#area_office").select2();$("#designation").select2();$(".select3").select2();
        });
    </script>
 
   <style type="text/css">
       .input-group{ margin-bottom: 10px; }
       .input-group{ margin-bottom: 10px; }
       .form-group{margin-bottom: 10px;}
    /*    .btn{margin-top: 20px;}
      .form-group label{margin: 0; padding:0;}
       .form-group label{margin: 0; padding:0;}*/
   </style>

</head>
    <div id="wait" style="position:fixed; display: block; top:0; left:0; width:100%; height: 100%; z-index: 1000; background-color: rgba(255, 220,220, 0.5); padding:2%;text-align: center; overflow: hidden; box-shadow: 0 0 10px;"><div style="position:fixed; display: block; top:40%; left:40%; width:20%; height: 30%; z-index: 2000; background-color: rgba(255, 255,255, 1); padding:2%;text-align: center; overflow: hidden; box-shadow: 0 0 10px;">Please Wait... <br/><br/><img src="<?php echo return_url().'images/loading2.gif'; ?>" alt=""/></div></div>
    <?php  
    if(isset($_SESSION[$system_name.'_USER_FORGET'])){
    ?>
    <body class="login-page"><input type="hidden" id="urlPath" value="<?php echo return_url(); ?>" />
    <div class="login-box"><br/><br/><br/>       
        <div class="card" style="">
            <div class="body" style=" ">
                <form id="sign_in" method="POST"> 
                    <?php 
                    if(isset($_POST['passwordremembered'])){  
                                             
                        unset($_SESSION[$system_name.'_USER_FORGET']);
                        FeedBack::redirect(return_url());
                    }elseif(isset($_POST['send'])){
                         
                        $email = $_POST['email'];

                        $errors = array();

                        if(empty($email)){
                            $errors[] = "Enter Username";
                        }

                        if($errors === []){
                            $db = new Db();
                            $select = $db->select("SELECT * FROM sysuser WHERE user_name = '$email'");                 
                            if($db->num_rows()){

                                extract($select[0][0]);

                                if(!$user_active){
                                    echo("<div class='label-danger' style='font-size:12px;text-align:center;color:white;'>Your account is Locked, Password can not be reset. Contact Admin for help</div>");
                                }else{
                                    
                                    $new_password = Feedback::password_generator();
                                    $msg = array();
                                    $msg[] = "Hello $user_surname $user_othername, ";
                                    $msg[] = "";
                                    $msg[] = "Your Login credentials have been changed:";
                                    $msg[] = "Username: <b>$user_name</b>";
                                    $msg[] = "Password: <b>$new_password</b>";
                                    $msg[] = "";
                                    $msg[] = "You will be required to login with the above Username and Password and then change the password to one that you will easily remember.";
                                    $message[] = "Thank You.";

                                    $to = $user_email;
                                    $subject = "FORGOT PASSWORD";
                                    $message = implode("\r \n <br/>", $msg);


                                    $db = new Db();
                                    $us = new Users();
                                    $p = $us->penc($new_password);
                                    $update = $db->update("sysuser", 
                                    ["user_password"=>$p, "user_online"=>0, "user_forgot_password"=>1],
                                    ['user_id'=>$user_id]); //used quotes on user_id by Mutumba Robert since PHP upgraded versions use quotes when defining constatnts.

                                    Feedback::sendmail($to,$subject,$message,$name);

                                    Feedback::success("Please check your email to Proceed with resetting your password.");
                                    unset($_SESSION[$system_name.'_USER_FORGET']);
                                    Feedback::refresh("5");
                                    AuditTrail::registerTrail("FORGOT PSWD - SUCCESSFULL", $db_id="",  "LOGIN-SUCCESSFULL", "LOGIN-SUCCESSFULL");
                                }
                            }else{
                                Feedback::warning("Username does not exist in the system");

                                AuditTrail::registerTrail("FORGOT PSWD - FAILED", $db_id="",  "LOGIN-FAILED", "LOGIN-FAILED: username_entered->$username AND password->$password");
                            }
                        }else{
                            Feedback::errors($errors);
                        }
                    }

                     //AuditTrail::registerTrail("LOGIN - FAILED", $db_id="",  "LOGIN - SUCCESSFULL", "LOGIN - SUCCESSFULL");
                    ?>

                    <img src="<?php echo return_url().'images/centenary.png'; ?>" style="position:absolute;top:-70px; left:26%; width:150px;"/>
                    <div class="sys_name"><span style="font-size:2em;color:red;"></span><BR/>Stores Requisitioning</div>

                    <div class="msg">
                        <h5>Forgot Password?</h5>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-fx fa-user"></i>
                        </span>
                        <div class="form-line">
                            <input type="text" autocomplete="off" class="form-control" name="email" placeholder="Enter Your Username" value="<?php echo $email; ?>" autofocus>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="ignore" name="send" class="btn btn-block bg-pink waves-effect" type="submit"><i class="fa fa-fx fa-plane"></i> Send</button>
                        </div>
                    </div>
                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-12 align-right"> <button id="ignore2" name="passwordremembered" value="GOOD" class="forgot-password"><i class="fa fa-fw fa-lock"></i>
                           I have Remembered my password, Login</button>

                        </div>
                    </div>                    
                </form>
            </div>
        </div>
    </div>
    <?php }elseif(!isset($_SESSION[$system_name.'_USER_ID'])){
        
    ?>
    
    <body class="login-page"><input type="hidden" id="urlPath" value="<?php echo return_url(); ?>" />
    <div class="login-box"><br/><br/><br/>       
        <div class="card">
            <div class="body" style="">
                <form id="sign_in" method="POST">
                    <?php 
                    if(isset($_POST['forgetpassword'])){
                        
                        $_SESSION[$system_name.'_USER_FORGET'] = "FORGOT";  
                        Feedback::redirect(return_url());                   
                    }elseif(isset($_POST['login'])){
                        
                        $username = $_POST['username'];
                        $password = $_POST['password'];
                        $us = new Users();
                        $p = $us->penc($password);
                        $db = new Db();

                        $ad = new ActiveDirectory();
                        $ad_results = $ad->login($username, $password);

                        //print_r($ad_results);
                        
                        if($ad_results['status']){

                            $name = $ad_results['message']['name'];
                            $email = $ad_results['message']['email'];
                            $name1 = explode('.', $email);
                            $surname = $name1[0];
                            $othername = $name1[1];
                            $db = new Db();

                            $sql = "SELECT * FROM sysuser WHERE user_email = '$email'";
                            $select = $db->select($sql);
                            //extract($select[0]);              
                            if(!$db->num_rows()){
                                echo 'REACHED'; 
                                //insert in new user
                                $x = new Db();
                                $insert = $x->insert("sysuser", ["user_name"=>'sdf']);
                                echo '>>>'.$db->error();
                            }


                            $select = $db->select("SELECT * FROM sysuser WHERE user_email = '$email'");

                            if($db->num_rows()){
                                extract($select[0]); 
                                if(empty($user_active)){
                                    Feedback::error("Account is Locked");
                                }else{
                                    Feedback::success("Successfully Logged in. Please wait while redirecting");
                                    $query_string = '';//(empty(end(explode("=", $_SERVER['QUERY_STRING']))))? "dashboard/index":end(explode("=", $_SERVER['QUERY_STRING']));

                                    FeedBack::redirect(return_url().$query_string);
                                    $_SESSION[$system_name.'_USER_ID'] = $user_id;
                                    $_SESSION['centenary_ROLE_ID'] = $user_id;

                                    $update = $db->update("sysuser", ["user_last_logged_in"=>time(), "user_online"=>1, "user_last_active"=>time(), "user_session"=>Feedback::ip_address()], ["user_id"=>$user_id]);

                                    AuditTrail::registerTrail("LOGIN-SUCCESSFULL", $db_id="",  "LOGIN-SUCCESSFULL", "LOGIN-SUCCESSFULL");
                                    $_SESSION['LOGIN-ATTEMPTS'] = 0;

                                }  
                            }else{

                            }                          
                        }else{
                            Feedback::warning("Wrong User name or Password");

                            AuditTrail::registerTrail("LOGIN-FAILED", $db_id="",  "LOGIN-FAILED", "LOGIN-FAILED: username_entered->$username AND password->$password");
                        }
                    }

                    

                     //AuditTrail::registerTrail("LOGIN - FAILED", $db_id="",  "LOGIN - SUCCESSFULL", "LOGIN - SUCCESSFULL");
                    ?>

                    <img src="<?php echo return_url().'images/centenary.png'; ?>" style="position:absolute;top:-70px; left:26%; width:150px;"/>
                    <div class="sys_name"><span style="font-size:2em;color:red;"></span><BR/>Stores Requisitioning</div>
                    <div style="background-color:red;color:white;margin:14px 0; text-align: center;">(AD Credentials)</div>
                   
                    <div class="col-lg-12">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-fx fa-user"></i>
                            </span>
                            <div class="form-line">
                                <input autocomplete="off" id="username" type="text" class="form-control" name="username" placeholder="Enter Username / Email" autofocus>
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-fx fa-lock"></i>
                            </span>
                            <div class="form-line">
                                <input autocomplete="off" id="password" type="password" class="form-control" name="password" placeholder="Enter Password" >
                            </div>
                        </div>
                        <div id="capson" class="input-group">
                            <div class="form-line">
                                <div id="capson" class="btn btn-sm btn-warning btn-block"><i class="fa fa-fw fa-warning"></i> Caps Lock is On</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button name="login" value="GOOD" class="btn btn-block bg-pink waves-effect" id="loginBtn" type="button">SIGN IN</button>
                            <span id="loginStatus"></span>
                        </div>
                    </div>                        
                    <div class="row m-t-15 m-b--20">
                        
                    </div>                    
                </form>
                <script type="text/javascript">
                    var capscheck = document.getElementById("password");
                    var capson = document.getElementById("capson");
                    capson.style.display = "none";
                    capscheck.addEventListener("keyup", function(event) {
                        if (event.getModifierState("CapsLock")) {
                            capson.style.display = "block";
                        } else {
                            capson.style.display = "none"
                        }
                    });
                    </script>
            </div>
        </div>
    </div>
    <?php }else {

    ?>
    <body class="theme-red"><input type="hidden" id="urlPath" value="<?php echo return_url(); ?>" />
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header" style="margin-left:25px;padding-left:10px;padding-bottom:0; margin:0;">
                
                
                <a class="navbar-brand" href="<?php display_url()?>"><img src="<?php display_url();?>images/centenary-small.png" alt="" style="position:absolute; top:5px; left:5px; height:inherit;z-index:1000"/> <span style="margin-left:50px;">Stores Requisitioning</span></a> 
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"><i class="fa fa-fx fa-bars"></i></a>
            </div>


            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                    </li>
                    <li style="display:none;">
                        <a style="color:yellow"><div>You are logged in as :
                            
                            <?php 
                            $n = new BeforeAndAfter();

                            echo $n->full_name(user_id()); 
                            echo " (<b>". $n->rgf("designation", $n->rgf("sysuser", user_id(), "user_id", "user_designation"), "designation_id", "designation_name")."</b>) in <b>".$n->rgf("department", $n->rgf("sysuser", user_id(), "user_id", "user_department_id"), "dept_id", "dept_name")."</b>";

                            ?>
                            </div></a>
                    </li>
                    <li style="font-size: 12px; margin-top:2px;color:white; line-height: 16px;padding:0 20px;letter-spacing: 0.03em; color:#ccc;">
                        <?php             
                         // $v = new PublicHolidays();
                         // $v->getDay(time());
                       echo 'Name: <b style="color:#eee;">'.ucwords($n->rgf("sysuser", user_id(), "user_id", "user_othername")." ".$n->rgf("sysuser", user_id(), "user_id", "user_surname")).'</b><br/>';
                         //echo 'Department: <b style="color:#eee;">'.$n->rgf("department", $n->rgf("sysuser", user_id(), "user_id", "user_department_id"), "dept_id", "dept_name").'</b><br/>';

                        //echo 'Branch: <b style="color:#eee;">'.$n->rgf("branch", $n->rgf("sysuser", user_id(), "user_id", "user_branch_id"), "branch_id", "branch_name").'</b><br/>';

                        
                        echo 'Staff Number: <b style="color:#eee;">'.$n->rgf("sysuser", user_id(), "user_id", "check_number").'</b><br/>';
                        ?>
                    </li>
                    <li style="font-size: 12px; margin-top:2px;color:white; line-height: 16px;padding:0 20px;letter-spacing: 0.03em; color:#ccc;">
                        <?php
                        $n = new Users();
                         
                        
                        echo 'System Role: <b style="color:#eee;">'.$n->rgf("user_role", $n->rgf("sysuser", user_id(), "user_id", "user_role"), "ur_id", "ur_name").'</b><br/>';
                        // echo 'Branch: <b style="color:#eee;">'.$n->rgf("branch", $n->rgf("sysuser", user_id(), "user_id", "user_branch_id"), "branch_id", "branch_name").'</b><br/>';
                        // $ad = $n->myDelegate(user_id(), time());
                        // if($ad['delegate']){
                        //     echo '<span style="color:yellow">Active Delegate: <b style="color:yellow;">'.$n->full_name($ad['delegate']).'</b></span><br/>';
                        // }
                        
                        ?>
                    </li>
                    <li>
                        <a onclick = "return confirm('Do you intend to logout ?');" href="<?php display_url(); ?>users/logout">
                            <i class="fa fa-fx fa-sign-out"></i>
                            <span>Logout</span>
                        </a>
                    </li>                    
                </ul>
            </div>
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <div class="menu">
                <ul class="list">
                    <li class="">
                        <a class="eagle-load" href="<?php display_url()?>">
                            <i class="fa fa-fx fa-home"></i>
                            <span>Home</span>
                        </a>
                    </li>

                    <li <?php active('requisition'); ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="fa fa-fx fa-table"></i>
                            <span>My Requisitions</span>
                            <?php  
                            $avr = new Requisition();
                            if($avr->rejected(user_id())){  ?>
                            <div class="pull-right circle-number" style=""><?php echo $avr->rejected(user_id()); ?> </div>
                            <?php } ?>
                        </a>
                        <ul class="ml-menu">
                            <?php
                            $j=1;
                            foreach(Requisition::getLinks() as $link){
                                extract($link);
                                $a = new AccessRights();
                                if($a->sectionAccess(user_id(), $link_page, $link_right)){
                                    $xx = explode('/', $link_address);

                                    $active = (end($xx) == portion(2))? ' class="active" ':"";                          
                                    $eagleLoad = ($j != 1)?"eagle-load":"";
                                    $j++;      
                                    echo "<li $active>";
                                    echo '<a class="'.$eagleLoad.'" href="'.return_url().$link_address.'"><i class="fa fa-fx '.$link_icon.'"></i> '.$link_name.'</a>';
                                    echo '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </li>

                    <li <?php active('stores'); ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="fa fa-fx fa-building"></i>
                            <span>Stores</span>
                            <?php  
                            $avr = new Stores();
                            ?>
                        </a>
                        <ul class="ml-menu">
                            <?php
                            foreach(Stores::getLinks() as $link){
                                extract($link);
                                $a = new AccessRights();
                                if($a->sectionAccess(user_id(), $link_page, $link_right)){
                                    $xx = explode('/', $link_address);

                                    $active = (end($xx) == portion(2))? ' class="active" ':"";                                
                                    echo "<li $active>";
                                    echo '<a class="eagle-load" href="'.return_url().$link_address.'"><i class="fa fa-fx '.$link_icon.'"></i> '.$link_name.'</a>';
                                    echo '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </li>
                    <?php  
                        $avr = new PendingApprovals();
                        if($avr->notApproved2(user_id())){  
                    ?>
                    <li <?php active('pending-approvals'); ?>>
                        <a class="eagle-load" href="<?php echo return_url().'pending-approvals/requisitions'; ?>">
                            <i class="fa fa-fx fa-circle"></i>                            
                            <span>Pending Approvals</span> 
                            
                            <div class="pull-right circle-number" style=""><?php echo $avr->notApproved2(user_id()); ?> </div>                         
                        </a>
                    </li>
                    <?php } ?> 

                    <li <?php active('approved-requisitions'); ?>>
                        <a class="eagle-load" href="<?php echo return_url().'approved-requisitions/requisitions'; ?>">
                            <i class="fa fa-fx fa-check"></i>                            
                            <span>Approved Requisitions</span>                           
                        </a>
                    </li>
					
                    <li <?php active('all-requisitions'); ?>>
                        <a class="eagle-load" href="<?php echo return_url().'all-requisitions/all'; ?>">
                            <i class="fa fa-fx fa-table"></i>                            
                            <span>All Requisitions</span>                           
                        </a>
                    </li>

                    <li <?php active('audit-trail'); ?>>
                        <a class="eagle-load" href="<?php echo return_url().'audit-trail/view-audit-trail'; ?>">
                            <i class="fa fa-fx fa-circle"></i>
                            <span>Audit Trail</span>
                        </a>
                    </li>
                    <li <?php active('users'); ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="fa fa-fx fa-users"></i>
                            <span>Users</span>
                        </a>
                        <ul class="ml-menu">
                            <?php
                            foreach(Users::getLinks() as $link){
                                extract($link);
                                $a = new AccessRights();
                                if($a->sectionAccess(user_id(), $link_page, $link_right)){
                                    $xx = explode('/', $link_address);                              
                                    $active = (end($xx) == portion(2))? ' class="active" ':"";                                
                                    echo "<li $active>";
                                    echo '<a class="eagle-load" href="'.return_url().$link_address.'"><i class="fa fa-fx '.$link_icon.'"></i> '.$link_name.'</a>';
                                    echo '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </li>
                                        
                    <li <?php active('access-rights'); ?>>
                        <a class="eagle-load" href="<?php display_url();?>access-rights/all-user-rights-and-privileges">
                            <i class="fa fa-fx fa-building"></i>
                            <span>User Rights & Privileges</span>
                        </a>                        
                    </li>
                      
                    <li <?php active('user-role'); ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="fa fa-fx fa-user"></i>
                            <span>User Role</span>
                        </a>
                        <ul class="ml-menu">
                            <?php
                            foreach(UserRole::getLinks() as $link){
                                extract($link);
                                $a = new AccessRights();
                                if($a->sectionAccess(user_id(), $link_page, $link_right)){
                                    $xx = explode('/', $link_address);                              
                                    $active = (end($xx) == portion(2))? ' class="active" ':"";                                
                                    echo "<li $active>";
                                    echo '<a class="eagle-load" href="'.return_url().$link_address.'"><i class="fa fa-fx '.$link_icon.'"></i> '.$link_name.'</a>';
                                    echo '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </li> 

                    <li <?php active('approvalMatrix'); ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="fa fa-fx fa-user"></i>
                            <span>Approval Matrix</span>
                        </a>
                        <ul class="ml-menu">
                            <?php
                            foreach(ApprovalMatrix::getLinks() as $link){
                                extract($link);
                                $a = new AccessRights();
                                if($a->sectionAccess(user_id(), $link_page, $link_right)){
                                    $xx = explode('/', $link_address);                              
                                    $active = (end($xx) == portion(2))? ' class="active" ':"";                                
                                    echo "<li $active>";
                                    echo '<a class="eagle-load" href="'.return_url().$link_address.'"><i class="fa fa-fx '.$link_icon.'"></i> '.$link_name.'</a>';
                                    echo '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </li> 
                    <li <?php active('groups'); ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="fa fa-fx fa-users"></i>
                            <span>Groups</span>
                        </a>
                        <ul class="ml-menu">
                            <?php
                            foreach(Groups::getLinks() as $link){
                                extract($link);
                                $a = new AccessRights();
                                if($a->sectionAccess(user_id(), $link_page, $link_right)){
                                    $xx = explode('/', $link_address);                              
                                    $active = (end($xx) == portion(2))? ' class="active" ':"";                                
                                    echo "<li $active>";
                                    echo '<a class="eagle-load" href="'.return_url().$link_address.'"><i class="fa fa-fx '.$link_icon.'"></i> '.$link_name.'</a>';
                                    echo '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </li> 
                    <li <?php active('approvalgroup'); ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="fa fa-fx fa-table"></i>
                            <span>Approvers & Groups</span>
                        </a>
                        <ul class="ml-menu">
                            <?php
                            foreach(ApprovalGroup::getLinks() as $link){
                                extract($link);
                                $a = new AccessRights();
                                if($a->sectionAccess(user_id(), $link_page, $link_right)){
                                    $xx = explode('/', $link_address);                              
                                    $active = (end($xx) == portion(2))? ' class="active" ':"";                                
                                    echo "<li $active>";
                                    echo '<a class="eagle-load" href="'.return_url().$link_address.'"><i class="fa fa-fx '.$link_icon.'"></i> '.$link_name.'</a>';
                                    echo '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </li>                 
                
                    

                    <li>
                        <a class="eagle-load" href="<?php display_url(); ?>users/changed-password">
                            <i class="fa fa-fx fa-cogs"></i>
                            <span>User Account Settings</span>
                        </a>
                    </li>                    
                    <li>
                        <a class="eagle-load" onclick = "return confirm('Do you intend to logout ?');" href="<?php display_url(); ?>users/logout">
                            <i class="fa fa-fx fa-sign-out"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                    <li style="margin:40px 0;"></li>
                </ul>
                
            </div>
            
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                   
                </div>
                <div class="version">
                    <b>Version: </b> 4.7
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
    </section>

    <section class="content">
        <div class="container-fluid" id="EagleContainer">
            <?php 
            $t = new BeforeAndAfter();
            if($t->rgf("sysuser", $user_id, "user_id", "user_forgot_password")){
                //Feedback::error("Please change your password before using the system.");
            }
            // all users
            // $db = new Db();
            // $select = $db->select("SELECT user_last_active, user_id as uid FROM sysuser");
            // foreach($select as $row){
            //     extract($row);            
            //     if(time() - $user_last_active > 10*60){
            //         if($uid != user_id()){
            //             $update = $db->update("sysuser", ["user_online"=>0], ["user_id"=>$uid]);
            //         }
            //     }else{
            //         //
            //     }
            // }

            $user_id = $_SESSION[$system_name.'_USER_ID'];
            $db = new Db();
            $select = $db->select("SELECT user_last_active FROM sysuser WHERE user_id = '$user_id'");
            if($db->num_rows()){
                extract($select[0]);
            }
            
            if(time() - $user_last_active > 30*60){
                //echo '<script>alert("Timeout You have been inactive for 1 minute");</script>';
                Feedback::error('<h1><center>Time Out<br/> You have been inactive for morethan 5 Minutes</center></h1>');
                Feedback::refresh(1, return_url()."users/logout");
                $update = $db->update("sysuser", ["user_last_logged_in"=>time(), "user_online"=>0], ["user_id"=>$user_id]);
            }else{
                $time = time();
                $update = $db->update("sysuser", ["user_last_active"=>$time], ["user_id"=>$_SESSION[$system_name.'_USER_ID'], "user_session"=>Feedback::ip_address()]); 
            }
            /////////////////////////////////////////////////

            include __DIR__ . "/Admin_functioncalls.php";

            function rutime($ru, $rus, $index) {
                return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
                 -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
            }
                     
            $end_time = getrusage();

            //echo "This process used " . rutime($end_time, $start_time, "utime") ." ms for its computations\n";
            //echo "It spent " . rutime($end_time, $start_time, "stime") . " ms in system calls\n";

            ?>         
            <div class="clearfix"></div>
            <br/><br/>
        </div>
    </section>
    <?php } ?>
    
    <!-- Select Plugin Js -->
    <script src="<?php display_url(); ?>css/bootstrap-select/js/bootstrap-select.js">
 </script>
   

    <!-- Waves Effect Plugin Js -->
    <script src="<?php display_url(); ?>css/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="<?php display_url(); ?>css/jquery-countto/jquery.countTo.js"></script>


    <!-- Waves Effect Plugin Js -->
    <script src="<?php display_url(); ?>css/node-waves/waves.js"></script>
    
    <!-- Autosize Plugin Js -->

    
    
    <!-- Custom Js -->
    <script src="<?php display_url(); ?>js/admin.js"></script>
    <script src="<?php display_url(); ?>js/pages/index.js"></script>
    
    <!-- Demo Js -->
    <script src="<?php display_url(); ?>js/demo.js"></script>
    <script type="text/javascript" src="<?php display_url();?>js/number.js"></script>
    <script type="text/javascript">
        $(document).ready( function () {
           
            $('input[type ="date"]').bootstrapMaterialDatePicker({ 
                weekStart : 0, 
                time: false, 
                //minDate : new Date() 
            });

            $('#date2').bootstrapMaterialDatePicker({ 
                weekStart : 0, 
                time: false, 
                //minDate : new Date() 
            });

            $('#date1').bootstrapMaterialDatePicker({ 
                weekStart : 0, 
                time: false, 
                //minDate : new Date() 
            });
        } );


    </script>
 <script type="text/javascript">
        
    </script>

</body>

</html>
<?php 

?>
