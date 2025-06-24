<?php
///error_reporting(null);
include(__DIR__ . "/../classes/init.inc");	
$system_name="CENTENARY";	

$reqId = $_POST['reqId'];
$comment = $_POST['comment'];
$status = $_POST['status'];
$reqId = $_POST['reqId'];

$t = new BeforeAndAfter();

//echo $status.' == '.$comment.' === '.$reqId;
$req_id = $reqId;
$req = 1;
$db = new Db();
$select = $db->select("SELECT * FROM requisition WHERE req_id = '$req_id'");
if($db->num_rows){
  extract($select[0]);
}

if ($status=="Rejected") {
    $n = new Db();
    $n->update("requisition", ["req_status"=>0, "req_app1_user_id"=>NULL, "req_app2_user_id"=>NULL, "req_app4_user_id"=>NULL, "req_app3_user_id"=>NULL], ["req_id"=>$req_id]);
    $n = new Db();
    $nn = $n->query("DELETE FROM comment WHERE comment_to = '$req_added_by' AND comment_part_id = '$req_id' AND comment_type = 'REQ' ");
    $nn = $n->insert("rejected_copy_master", [
      "rcm_comment"=>$comment,
      "rcm_date_added"=>time(),
      "rcm_added_by"=>user_id(),
      "rcm_rejected_by"=>user_id(), 
      "rcm_type_id"=>$req_id,
      "rcm_type"=>"REQ",
    ]);
    //==============================
    $select = $db->select("SELECT * FROM requisition WHERE req_id = '$req_id'");
    if($db->num_rows()){
      extract($select[0]);
    }
    //email notification 
    $next_name = $t->ruf($req_added_by, "user_othername");
    $next_telephone = $t->ruf($req_added_by, "user_telephone");
    $next_email = $t->ruf($req_added_by, "user_email");
    $message = "Hello ".ucwords(strtolower($next_name)).",\n\r <br/><br/>";
    $message .= "Your Requistion with No.: <b>$req_number</b> has been rejected by <b>".$t->full_name(user_id())."</b><br/>";
    if($t->check_de_status($next_id, "EMAIL")){
      $to = $next_email;
      $subject = " ".$req_number." REJECTED";
      
      $link = return_url()."requisition/view-requisition/".$req_number;
      
      $message .= "\r\nYou can use this link: <a href='$link'>$link</a>";
      
      FeedBack::sendmailz(array($to),$subject,$message,$next_name);
    }
    //-----------------------------------------------------------------
    echo 'Rejection Completed, notification has been sent to the Requisitioner';
} else{
  $db = new Db();

  $comment = $_POST['comment'];
  

  $insert = $db->insert("comment", 
[
  "comment_from"=>user_id(),
  "comment_to"=>$req_added_by,
  "comment_message"=>"$comment",
  "comment_type"=>"REQ",
  "comment_date"=>time(),
  "comment_part_id"=>$req_id,
  "comment_level"=>$req,
]
);
  //echo $req = 1;
  $level["req_app".$req."_user_id"]=user_id();
  $level["req_app".$req."_designation_id"]=$t->ruf(user_id(), "user_designation");
  $level["req_app".$req."_date"]=time();

 
  $update = $db->update("requisition", $level, ["req_id"=>$req_id]);  
  //echo $db->error();
  
  //======== creating file ==============================
  $list = array();
  $list[] = array(
    "Date",
    "Item Code",
    "Quantity",
    "UoM",
    //"Price",
    "Division",
    "Req. No.",
  );
  
  $db = new Db();
$select = $db->select("SELECT * FROM requisition WHERE req_id = '$req_id'");
extract($select[0]);

$select = $db->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
      $no = 1;
      $total = 0;
      foreach($select as $row){
  extract($row);
  $list[]=array(
    date('d/M/Y', $req_date_added),
    $ri_code,
    $ri_quantity,
    $ri_uom,
    //$ri_price,
    $t->rgf("approval_matrix", $req_division, "ap_id", "ap_code"),
    $req_number,
  );
}


  $fp = fopen('../StoreRequisitions/'.$req_number.'.csv', 'w');

  foreach ($list as $fields) {
    fputcsv($fp, $fields);
  }

  fclose($fp);
  //==================================================

  if(!$db->error()){
    $next =$t->nextApproval();
    if(0 !== 0){
      $hod_name = $t->ruf($next, "user_othername");
      $hod_telephone = $t->ruf($next, "user_telephone");
      $hod_email = $t->ruf($next, "user_email");

      $message = "Hello ".ucwords(strtolower($hod_name)).",\n";
                
      if($t->check_de_status($hod_id, "EMAIL")){
        $to = $hod_email;
        $subject = " ".$req_number." APPROVED";
        
        $link = return_url()."requisition/view-requisition/".$req_number;
        $message .= "\r\n<br/><br/>Please Check Requisition with No.: <b>$req_number</b> is pending approval.\r\n<br/><br/> ";
        $message .= "\r\nYou can use this link: <a href='$link'>$link</a>";
        
        FeedBack::sendmailz(array($to),$subject,$message,$hod_name);
      }
    }else{
      $next = $req_added_by;
      $hod_name = $t->ruf($next, "user_othername");
      $hod_telephone = $t->ruf($next, "user_telephone");
      $hod_email = $t->ruf($next, "user_email");

      $message = "Hello ".ucwords(strtolower($hod_name)).",\n";

        
      if($t->check_de_status($hod_id, "EMAIL")){
        $to = $hod_email;
        $subject = " ".$req_number." APPROVED";
        
        $link = return_url()."requisition/view-requisition/".$req_number;
        $message .= "\r\n<br/><br/>Your requisition with No.: <b>$req_number</b> is successfully Approved.\r\n<br/><br/> ";
        $message .= "\r\nYou can use this link: <a href='$link'>$link</a>";

        FeedBack::sendmailz(array($to),$subject,$message,$hod_name);
      }
    }

    // FeedBack::success();
    // FeedBack::refresh();
    echo "Successfully Approved";

  }else{
    //FeedBack::error($db->error());
  }     
}
