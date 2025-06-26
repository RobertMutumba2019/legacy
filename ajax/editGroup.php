<?php
error_reporting(null);
include_once __DIR__ . "/classes/init.inc";	
$t = new BeforeAndAfter();	

			$groupname = $_POST['groupname'];
			$matrix = $_POST['matrix'];
			$gr_id = $_POST['gr_id'];
			$time = time();
			$user = user_id();
			
			$errors = array();
			$xx = array();
			
			if($errors === []){
				$db->update("groups",["gr_matrix"=>$matrix,"gr_date_added"=>time(),"gr_name"=>$groupname,"gr_added_by"=>user_id()],["gr_id"=>$gr_id]);
			$xx['message'] = 'Successfully Saved';
        }
        //else{
        	
        //   $xx['message'] = ('Not Saved, '.$db->error());
        // }
		else{
      	$xx['message']='Error';
        
      }
          
echo json_encode($xx);