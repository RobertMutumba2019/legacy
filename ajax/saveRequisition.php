<?php
error_reporting(null);
include(__DIR__ . "/../classes/init.inc");	
$system_name="CENTENARY";	

$title = $_POST['title'];
$division = $_POST['division'];
$totalItems = $_POST['totalItems'];
$reqId = $_POST['reqId'];

$activeDelegate = 1;

$t = new BeforeAndAfter();

$db = new Db();

function requisition_number(){   
    $dm = date('y').date('m');
    $suffix = "RQN".$dm;
    $db = new Db();
    $sql = "SELECT TOP 1 req_number FROM requisition WHERE req_number  <> '' AND req_number IS NOT NULL ORDER BY req_id DESC";
    $select = $db->select($sql);
    $x = "";
    if($db->num_rows()){
      extract($select[0]);
      $x = explode($suffix, $req_number);
      $x = end($x);
    }
    //echo '>>'.$req_number;
    $f = (int)$x+1;
    return $suffix.str_pad($f, 5, "0", STR_PAD_LEFT); 
  }


$rg = requisition_number();
$ref = user_id().time();

if($reqId){
  $insert = $db->update("requisition", ["req_number"=>$rg, "req_title"=>$title, "req_division"=>$division, "req_ref"=>$ref, "req_added_by"=>user_id(), "req_date_added"=>time(), "req_status"=>1, "req_hod_id"=>$t->hod(user_id()), "req_delegate1"=>$activeDelegate, "req_delegator1"=>$t->hod(user_id())],["req_id"=>$reqId]);

    $db = new Db();
    $by = user_id();
    $by = $db->select("SELECT TOP 1 req_id as reqIDID, req_date_added AS dateadded FROM requisition WHERE req_status = 1 AND req_added_by = '$by' AND req_division = '$division' AND req_ref = '$ref' AND req_id = '$reqId'");
    extract($by[0]);
    $number = 'RQN'.date('y', $dateadded).date('m', $dateadded).str_pad($reqIDID, 6, '0', STR_PAD_LEFT);
    $db->update("requisition",["req_number"=>$number],["req_id"=>$reqIDID]);

}else{
  $insert = $db->insert("requisition", ["req_number"=>$rg, "req_title"=>$title, "req_division"=>$division, "req_ref"=>$ref, "req_added_by"=>user_id(), "req_date_added"=>time(), "req_status"=>1, "req_hod_id"=>$t->hod(user_id()), "req_delegate1"=>$activeDelegate, "req_delegator1"=>$t->hod(user_id())]);  

  //get and assign requisition number
  $db = new Db();
  $by = user_id();
  $by = $db->select("SELECT TOP 1 req_id as reqIDID FROM requisition WHERE req_status = 1 AND req_added_by = '$by' AND req_division = '$division' AND req_ref = '$ref' ORDER BY req_id ASC");
  extract($by[0]);
  $number = 'RQN'.date('y').date('m').str_pad($reqIDID, 6, '0', STR_PAD_LEFT);
  $db->update("requisition",["req_number"=>$number],["req_id"=>$reqIDID]);
}

//req_division === 
$db = new Db();
$db->query("DELETE FROM requisition_item WHERE ri_ref = '$req_ref'");

$select = $db->select("SELECT gr_name, apg_user, user_email FROM approval_group, approval_matrix, groups, sysuser WHERE ap_id = '$division' AND gr_matrix = ap_id AND gr_id = apg_name AND apg_user = user_id");

$users_emails = array();
//print_r($select);
foreach($select as $row){
  extract($row);
  $users_emails[]=$user_email;
}

$rg = $number;

//======================================================
$to = $hod_email;
$subject = "PENDING APPROVAL $x";

$link = return_url()."requisition/view-requisition/".$rg;

$message = "Hello ".$gr_name.",\n";
$message .= "\r\n<br/><br/>You have a pending requisition $x with No.: <b>$rg</b>\r\n<br/><br/> ";
$message .= "\r\nYou can use this link: <a href='$link'>$link</a>";

FeedBack::sendmailz($users_emails,$subject,$message,$hod_name);

for($i=0; $i<$totalItems; $i++){
          
  $item = $_POST['item_code'.$i];
  $qty = $_POST['qty'.$i];
  $measure = $_POST['uom'.$i];
  $desc = $_POST['item_description'.$i];

  $insert = $db->insert("requisition_item", [
    "ri_code"=>$item,
    "ri_quantity"=>$qty,
    //"ri_price"=>$price,
    "ri_uom"=>$measure,
    "ri_description"=>$desc,
    "ri_ref"=>$ref,
  ]);
  echo $db->error();
}

$db->query("UPDATE attachments SET at_req_id = '$ref' WHERE at_req_id IS NULL");
//echo $db->error();

if(!$db->error()){
 echo 'Success';
}else{
  echo 'Error'.$db->error();
}

//echo json_encode($x);