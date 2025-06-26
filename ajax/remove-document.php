<?php 

include_once __DIR__ . "/classes/init.php";	
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];
$attach_id = $_POST['attachmentID'];

if(empty($user_id)){
	echo 'Invalid Approach';
}else{

$db = new Db();
$select = $db->select("SELECT at_path FROM attachments WHERE at_id = '$attach_id'");
if (is_array($select) && isset($select[0]) && is_array($select[0])) {
extract($select[0]);
}
$return_url = return_url();
echo $at_path;

unlink("../".str_replace(return_url(), '', $at_path));

$delete = $db->query("DELETE FROM attachments WHERE at_id = '$attach_id'");
if(empty($db->error())){
	echo "deleted";
}

echo $db->error();

}