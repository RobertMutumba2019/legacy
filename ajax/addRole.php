<?php
error_reporting(null);
include(__DIR__ . "/../classes/init.inc");	
$t = new BeforeAndAfter();
			$designation = $_POST['designation'];
			$time = time();
			$user = user_id();
			$errors = array();
			$xx = array();
			if($errors === []){
				$insert = $db->insert("user_role", ["ur_name"=>$designation, "ur_added_by"=>$user_id, "ur_date_added"=>$time]);

			$xx['message'] = 'Successfully Saved';
        }
        //else{
        	
        //   $xx['message'] = ('Not Saved, '.$db->error());
        // }
		else{
      	$xx['message']='Error';
        
      }
          
echo json_encode($xx);