<?php 
include __DIR__ . "/../classes/init.inc";
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];

$t = new Users();
$pf_number = $t->rgf("sysuser", $user_id, "user_id", "check_number");

if(empty($pf_number)){
	echo 'Invalid Approach';
}else{

$db = new Db();
$path = str_replace('\\','/', ABSPATH).'../';
$dir = array('attachments','attachments/'.$pf_number);
foreach($dir as $item){
	if (!file_exists($item)) {
	    @mkdir($path.$item, 0777, true);
	}
}
$type = $_POST['type'];
$req_ref = $_POST['req_ref'];
$fileName = str_replace(' ', '-', $_POST['fileName']);
$src = $_FILES['file']['tmp_name'];
$targ = "uploads/".$_FILES['file']['name'];
$targ = @$path.end($dir).'/'.$fileName.'.'.end(explode('.', $targ));
move_uploaded_file($src, $targ);
$file_path = @return_url().end($dir).'/'.$fileName.'.'.end(explode('.', $targ));


$insert = $db->insert("attachments",[
	"at_name"=>$fileName,
	"at_added_by"=>$user_id,
	"at_date_added"=>time(),
	"at_path"=>$file_path,
]);

$db = new Db();
$select = $db->select("SELECT * FROM attachments WHERE at_added_by = '$user_id' AND at_req_id IS NULL OR at_req_id = '$req_ref'");
if(!$db->num_rows()){
	echo 'There are no attachments';
}else{
	echo '<ol class="attachment-list">';
	foreach($select as $row){
		extract($row);
		echo '<li>';
		echo '<button type="button" data-id="'.$at_id.'" data-name="'.$at_name.'" class="btn-attachment-remove bg-danger text-danger"><i class="fa fa-fw fa-times"></i></button>';
		echo '&nbsp; ';
		echo '<a href="'.$at_path.'" target="_blank">'.$at_name.'</a>';
		
		echo '</li>';
	}
	echo '</ol>';
}

}