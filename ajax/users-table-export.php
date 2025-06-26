<?php 
include_once __DIR__ . "/classes/init.php";	
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];

$t = new BeforeAndAfter();

header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=StoreRequisitionUsers.xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//header("Cache-Control: private",false);

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
$sql = "SELECT * FROM sysuser, user_role WHERE ur_id=user_role $search";
$db = new Db();
$select = $db->select($sql);



	echo '<table class="table" cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
	//echo '<thead>';
	echo '<tr>';
	echo '<th style="width:30px">No.</th>';
	echo '<th id="">PF</th>';
	echo '<th id="name" class="'.$val2.'">Name</th>';
	echo '<th id="email" class="'.$val3.'">Email</th>';
	echo '<th id="userRole" class="'.$val8.'">User Role</th>';
	echo '<th >Location/Division</th>';
	echo '<th>Code</th>';
	echo '<th id="lastLoggedIn" class="'.$val4.'">Last Logged in</th>';
	echo '<th id="online_offline" class="'.$val5.'">Online/Offline</th>';
	echo '<th id="status" class="'.$val6.'">Status</th>';
	// if($access->sectionAccess(1, $t->page, 'E') || $access->sectionAccess(1, $t->page, 'D')){
//		echo '<th width="">Action</th>';
	// }
	echo '</tr>';
	//echo '</thead>';
	
		////////////////////////////REPORT STEP 1//////////////////
	
	//////////////////////////////////////////////////////////////
	$i=1;
	echo '<tbody>';	
	$start = ($start)?$start:1;			
	$i = ($eagleActivePage-1)*$rowsPerPage;
	if(is_array($select)){

	
	foreach($select as $row){
		$i = $i++;
		$i++;

		extract($row);
		$user_online = ($user_online)?'Online':'Offline';
		$user_active = ($user_active)?'Active':'Disabled';

		$gr_name = "";
		$ap_code = "";
		$sql = "SELECT gr_name,ap_code FROM approval_matrix, groups, sysuser, approval_group WHERE groups.gr_id=approval_group.apg_name AND sysuser.user_id=approval_group.apg_user AND gr_matrix = ap_id AND user_id = '$user_id' $search";
		$g = new Db();
		$gg = $g->select($sql);
		if ($g->num_rows()) {
			if (is_array($gg) && isset($gg[0]) && is_array($gg[0])) {
            extract($gg[0]);
			}
        }

		echo '<tr>';
		echo '<td style="width:30px;"><center>'.($i).'.</center></td>';
		echo '<td>'.($check_number).'</td>';
		echo '<td>'.($user_surname).' '.($user_othername).'</td>';
		echo '<td>'.($user_email).'</td>';
		echo '<td>'.$ur_name.'</td>';
		if($gr_name !== ''){
			echo '<td>'.$gr_name.'</td>';
			echo '<td>'.$ap_code.'</td>';
		}else{
			echo '<td></td>';
			echo '<td></td>';
		}

		echo '<td>'.date('M dS, Y h:i:s a', $user_last_logged_in).'</td>';
		echo '<td>'.$user_online.'</td>';
		echo '<td>'.$user_active.'</td>';

		//if($access->sectionAccess(1, $t->page, 'E') || $access->sectionAccess(1, $t->page, 'D')){
		
			//echo '<td>';
			// if($access->sectionAccess(1, $t->page, 'E'))
			 //   echo'<a class="eagle-load btn btn-primary btn-xs" href="'.return_url().'users/edit-user/'.$user_id.'">View / Edit </a>';
			    //echo'<a class="eagle-load btn btn-primary btn-xs" href="'.return_url().'users/edit-user/'.$user_id.'">View</a>';
			   // echo '<a '
				//echo $t->action('edit','users/edit-user/'.$user_id, 'Edit');
			// if($access->sectionAccess(1, $t->page, 'D'))
				//echo $t->action('delete','Customers/delete-customer/'.$cust_id, 'Delete');

			//echo '</td>';
		//}
		echo '</tr>';
		
		//////////////////////////////////REPORT STEP 2//////////////////////////////////	
		 
		
		/////////////////////////////////////////////////////////////////////////////
	}
	}
	echo '</tbody>';
	
	echo '</table>';
