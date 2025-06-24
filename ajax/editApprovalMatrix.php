<?php
error_reporting(null);
include(__DIR__ . "/../classes/init.inc");	
$t = new BeforeAndAfter();
			$id = $_POST['id'];
			$code = $_POST['code'];
			$unit_name = $_POST['unit_name'];
			$time = time();
			$user = user_id();
			$errors = array();
			$xx = array();
			if($errors === []){
				 $db->update("approval_matrix",["ap_date_added"=>time(),"ap_code"=>$code,"ap_unit_code"=>$unit_name,"ap_added_by"=>user_id()],["ap_id"=>$id]);

			$xx['message'] = 'Successfully Saved';
        }
        //else{
        	
        //   $xx['message'] = ('Not Saved, '.$db->error());
        // }
		else{
      	$xx['message']='Error';
        
      }
          
echo json_encode($xx);