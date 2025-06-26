<?php
error_reporting(null);
include_once __DIR__ . "/classes/init.inc";	
$t = new BeforeAndAfter();

$groupuser = $_POST['user'];
$groupname = $_POST['group'];

$ggid = $_POST['ggid'];

$time = time();
$user = user_id();
$db = new Db();

$errors = array();
if(empty($groupname)){
	$errors[]="Select Group Name";
}
if(empty($groupuser)){
	$errors[]="Select Users Name";
}

if($errors === []){
	if(!$ggid){
		$db->insert("approval_group",["apg_date_added"=>time(),"apg_name"=>$groupname,"apg_user"=>$groupuser,"apg_added_by"=>user_id()]);
		$update = $db->update("sysuser", ["user_role"=>1082], ["user_id"=>$groupuser]);
	}else{
		$db->update("approval_group",["apg_date_added"=>time(),"apg_name"=>$groupname,"apg_user"=>$groupuser,"apg_added_by"=>user_id()],["apg_id"=>$ggid]);
		$update = $db->update("sysuser", ["user_role"=>1082], ["user_id"=>$groupuser]);
	}
	
	if(empty($db->error())){
		$xx['message']='User Added';
		$xx['details']='';
	}else{
		$xx['message']='Error';
		$xx['details']=$db->error();
	}
}else{
	$xx['message']='Error';
	$xx['details']=implode(' ', $errors);
}
          
echo json_encode($xx);