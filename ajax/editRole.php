<?php
error_reporting(null);
include_once __DIR__ . "/classes/init.php";	
$t = new BeforeAndAfter();
			$id=portion(3);
			$designation = $_POST['designation'];
			$ur_id = $_POST['ur_id'];
			$time = time();
			$errors = array();
			$xx = array();
			if($errors === []){
				$insert = $db->update("user_role", ["ur_name"=>$designation, "ur_date_added"=>$time],["ur_id"=>$ur_id]);

			$xx['message'] = 'Successfully Saved';
        }
        //else{
        	
        //   $xx['message'] = ('Not Saved, '.$db->error());
        // }
		else{
      	$xx['message']='Error';
        
      }
          
echo json_encode($xx);