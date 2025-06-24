<?php
error_reporting(null);
include(__DIR__ . "/../classes/init.inc");	
$t = new BeforeAndAfter();			

			$groupname = $_POST['groupname'];
			$matrix = $_POST['matrix'];

			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			$xx = array();
			
			if($errors === []){
				$db->insert("groups",["gr_date_added"=>time(),"gr_matrix"=>$matrix,"gr_name"=>$groupname,"gr_added_by"=>user_id()]);
				
			$xx['message'] = 'Successfully Saved';
        }
        //else{
        	
        //   $xx['message'] = ('Not Saved, '.$db->error());
        // }
		else{
      	$xx['message']='Error';
        
      }
          
echo json_encode($xx);