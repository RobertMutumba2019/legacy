<?php 
include_once __DIR__ . "/classes/init.php";	
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];

$t = new Requisition();


$id = $_POST['req'];

if($id){
//require_once("http://localhost:8080/myPHPapp/java/Java.inc");
//$world = new java("HelloWorld");
$xml = array();
$push = array();
   $sqlu = "SELECT * FROM requisition WHERE req_id = '$id'";
                    $sel = $db->select($sqlu);                    
                    $db = new Db();

                    if (is_array($sel) && isset($sel[0]) && is_array($sel[0])) {
                    foreach($sel as $row){
                        extract($row);
                        $v = new Db(); 
$str = "<SSC><User><Name>PK1</Name></User><SunSystemsContext><BusinessUnit>CDB</BusinessUnit></SunSystemsContext><Payload><MovementOrder><MovementOrderDefinitionCode>SRO_ISSUE</MovementOrderDefinitionCode><MovementOrderReference></MovementOrderReference><SecondReference>$req_number</SecondReference><Status></Status><TransactionReferenceNumber></TransactionReferenceNumber>";
                            
$p = 0;

$vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
$non =1;
if(is_array($vv)){


foreach($vv as $r){
    extract($r);
    $p++;
$p1 = $p;
$p = str_pad($p, 2, "0", STR_PAD_LEFT);
$req_date_added = time();//+24*60*60;
$str .= "<MovementOrderLine><AccountCode></AccountCode><DemandQuantity>$ri_quantity</DemandQuantity><Description></Description><FromLocationIdentifier>L01</FromLocationIdentifier><ItemCode>".trim($ri_code)."</ItemCode><LineNumber></LineNumber><OrderDate>".date('dmY',$req_date_added)."</OrderDate><TransactionPeriod></TransactionPeriod><UnitOfMovement></UnitOfMovement><UserLineNumber>".$p1."</UserLineNumber><AnalysisQuantity><Analysis><VMolCatAnalysis_AnlCode>".$t->rgf("approval_matrix", $req_division, "ap_id","ap_code")."</VMolCatAnalysis_AnlCode></Analysis></AnalysisQuantity><VLAB1><Base><VMolVlabEntry_Val>$ri_quantity</VMolVlabEntry_Val></Base></VLAB1></MovementOrderLine>";
}
}
}

$str .= "</MovementOrder></Payload></SSC>";
//echo '<pre>';
//echo htmlspecialchars($str);
//echo '</pre>';


        //$xml[$req_id] = simplexml_load_string($world->sendToSun(array($str)));


        $endpoint ='http://sunesis:8338/myPHPapp/sunint.php';
        $params = array('strxml' => $str);
        $url = $endpoint . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);

        $xml[$req_id] = simplexml_load_string($output);
    }   


foreach($xml as $id=>$string){
    $status = $string->Payload->MovementOrder->attributes()['status'];
    if($status=="fail"){
        $errors[] = '<b>'.$t->rgf("requisition", $id, "req_id", "req_number").'</b> => '. $string->Payload->MovementOrder->Messages->Message->Application->Message;
    }elseif($status == "success"){
        $ref = $string->Payload->MovementOrder->MovementOrderReference;
        //$issueNumber[$id] = $ref;
        $push[$t->rgf("requisition", $id, "req_id", "req_number")] = $ref;
      	$db->update("requisition",["req_pushed"=>1, "req_date_pushed"=>time(), "req_pushed_by"=>time(), "req_issue_reference"=>$ref],["req_id"=>$id]);
    }
}

if(!empty($errors)){
	$message = array("message"=>"Error", "details"=>implode(', ', $errors));
}
if($push !== []){
    $message = array("message"=>"Success", "details"=>'Requisition '.$t->rgf("requisition", $id, "req_id", "req_number").' and Issue Reference: '.$push[$t->rgf("requisition", $id, "req_id", "req_number")]);       
}

}else{
	$message = array("message"=>"Error", "details"=>"Unknown Error");
}


echo json_encode($message);