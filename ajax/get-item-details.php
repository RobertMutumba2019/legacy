<?php 
include_once __DIR__ . "/classes/init.inc";
include_once __DIR__ . "/classes/DbSunServer.php";
error_reporting(null);
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];

$itemSelected = $_POST['itemSelected'];
$sun_db = new DbSunServer();
$sun_select = $sun_db->select("SELECT ITEM_CODE as db_item_code,UNIT_OF_WGHT as db_uom, DESCR as db_description FROM GTF_ITEM WHERE ITEM_CODE = '$itemSelected'");
if($sun_db->num_rows()){
	if (is_array($sun_select) && isset($sun_select[0]) && is_array($sun_select[0])) {
	extract($sun_select[0]);
	}
	$sun_db2 = new Db();
	$sun_select2 = $sun_db2->select("SELECT WAV_COST as db_price FROM GTF_ITEM_COST WHERE ITEM_CODE = '$itemSelected'");
	if($sun_db2->num_rows()){
		if (is_array($sun_select2) && isset($sun_select2[0]) && is_array($sun_select2[0])) {
		extract($sun_select2[0]);
		}
	}
}

$x = array('code'=>$db_item_code,'uom'=>$db_uom,'description'=>$db_description, 'price'=>$db_price);

echo json_encode($x);

