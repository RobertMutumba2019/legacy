<?php 
include __DIR__ . "/../classes/init.inc";
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];


header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=StoreRequisitionUsers.xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//header("Cache-Control: private",false);
$t = new Requisition();
?>
<style type="text/css">
	.table tbody tr td, .table tbody tr th {
  padding: 10px;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee; }

.table tbody tr.primary td, .table tbody tr.primary th {
  background-color: #1f91f3;
  color: #fff; }

.table tbody tr.success td, .table tbody tr.success th {
  background-color: #2b982b;
  color: #fff; }

.table tbody tr.info td, .table tbody tr.info th {
  background-color: #00b0e4;
  color: #fff; }

.table tbody tr.warning td, .table tbody tr.warning th {
  background-color: #ff9600;
  color: #fff; }

.table tbody tr.danger td, .table tbody tr.danger th {
  background-color: #fb483a;
  color: #fff; }

.table thead tr th {
  padding: 10px;
  border-bottom: 1px solid #eee; }

.table-bordered {
  border-top: 1px solid #eee; }
  .table-bordered tbody tr td, .table-bordered tbody tr th {
    padding: 10px;
    border: 1px solid #eee; }
  .table-bordered thead tr th {
    padding: 10px;
    border: 1px solid #eee; }

</style>
<?php

$searchWord = $_GET['searchWord'];
$item = $_GET['item'];
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ SEARCH COLUMNS @@@@@@@@@@@@@@@@@@@@@@@@@@@@
$searchColumns = array(
	'check_number', 
	'user_surname',
	'user_othername',
	'user_email',
	'ur_name',
);

if($searchWord){
	$v = array();
	$sw = $searchWord;
	$searchwords = explode(' ', $searchWord);
	foreach($searchColumns as $column){
		$v[] = $column." LIKE '%".$searchWord."%' ";
	}
	foreach($searchColumns as $column){
		foreach($searchwords as $searchWord){
			$v[] = $column." LIKE '%".$searchWord."%' ";
		}
	}
	$search = ' AND ('.implode(" OR ", $v).')';

}



//@@@@@@@@@@@@@@@@@@@@@@@  TOTOAL RECORDS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

$sql = "SELECT * FROM requisition,sysuser WHERE req_added_by = user_id AND req_added_by IS NOT NULL $search";
$db = new Db();
$select = $db->select($sql);


echo '<table class="code-eagle-table" cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
	//echo '<thead>';
	echo '<tr>';
	echo '<th style="width:30px">No.</th>';
	echo '<th id="date" class="'.$val1.'">Date</th>';
	echo '<th id="number" class="'.$val2.'">Req. No./Item Code</th>';
	echo '<th id="title" class="'.$val3.'">Title/Item Description</th>';
	echo '<th title="Lines in the requisition">L/Qty</th>';
	echo '<th >Status</th>';
	echo '<th >Requested/Approved By</th>';
	// if($access->sectionAccess(1, $t->page, 'E') || $access->sectionAccess(1, $t->page, 'D')){
		//echo '<th width="">Action</th>';
	// }
	echo '</tr>';
	//echo '</thead>';


	
		////////////////////////////REPORT STEP 1//////////////////
	
	//////////////////////////////////////////////////////////////
	$i=1;
	echo '<tbody>';	
	$start = ($start)?$start:1;			
	$i = ($eagleActivePage-1)*$rowsPerPage;
	foreach($select as $row){
		$i = $i++;
		$i++;

		extract($row);
		echo '<tr>';
		echo '<td style="width:30px;"><center>'.($i).'.</center></td>';
		echo '<td>'.Feedback::date_fm($req_date_added).'</td>';
		echo '<td>'.$req_number.'</td>';
		echo '<td>'.$req_title.'</td>';
		echo '<td>'.$t->total("requisition_item", "ri_ref", $req_ref).'</td>';
		echo '<td>'.$t->status($req_id).'</td>';
		echo '<td>'.$t->full_name($req_added_by).'</td>';
	//	echo '<td><a class="eagle-load" href="'.return_url().'requisition/view-requisition/'.$req_id.'">View Details</a></td>';
		echo '</tr>';
		$vw = new Db();
		if($item){
			echo '<table id="table2" border="1" style="width:100%">';
           
            $vv = $vw->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
            $non =1;
            foreach($vv as $r){
                extract($r);
                $color = "";//($non%2==0)?"blue":"black";
                echo '<tr>';
                
                //echo '<td>'.($non++).'.</td>';
                echo '<td></td>';
                echo '<td></td>';
                echo '<td>'.$ri_code.'</td>';
                echo '<td>'.$ri_description.'</td>';
                echo '<td>'.$ri_quantity.'</td>';
				echo '<td>'.$t->status($req_id).'</td>';
				if($req_app1_user_id){
					echo '<td>'.$t->full_name($req_app1_user_id).'</td>';
				}else{
                	echo '<td></td>';
				}
                //echo '<td>'.$ri_uom.'</td>';
                //echo '<td>'.$ri_price.'</td>';
                echo '</tr>';
            }

            $non++;

            //echo '</table>';
            
		}

		
		//////////////////////////////////REPORT STEP 2//////////////////////////////////	
		 
		
		/////////////////////////////////////////////////////////////////////////////
		
	}
	echo '</tbody>';
	
	echo '</table>';
