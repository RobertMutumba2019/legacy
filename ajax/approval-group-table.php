<?php 
include __DIR__ . "/../classes/init.inc";
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];
$attach_id = $_POST['attachmentID'];

$t = new Requisition();

$div = $t->findDiv($user_id);

$label = $_POST['label'];
$value = $_POST['value'];

$rowsPerPage = $_POST['rowsPerPage'];
$searchWord = urldecode(trim($_POST['searchWord']));
$eagleActivePage = $_POST['eagleActivePage'];
if ($eagleActivePage<=1||empty($eagleActivePage)) {
    $eagleActivePage = 1;
}
//echo $id.' '.$value;


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ ORDER BY COLUMNS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@
if($label=="PF"){ 
	$orderby = 'ORDER BY user_name '.$value;
	$val0 = strtolower('sort-'.$value);
}else{
	$val0 = 'eagle-sort';
}
if($label=="date"){ 
	$orderby = 'ORDER BY apg_date_added '.$value;
	$val1 = strtolower('sort-'.$value);
}else{
	$val1 = 'eagle-sort';
}
if($label=="gname"){ 
	$orderby = 'ORDER BY gr_name '.$value;//.', cust_last_name '.$value;
	$val2 = strtolower('sort-'.$value);
}else{
	$val2 = 'eagle-sort';
}
if($label=="name"){ 
	$orderby = 'ORDER BY user_surname '.$value;
	$val3 = strtolower('sort-'.$value);
}else{
	$val3 = 'eagle-sort';
}
// if($label=="lastLoggedIn"){ 
// 	$orderby = 'ORDER BY user_last_logged_in '.$value;
// 	$val4 = strtolower('sort-'.$value);
// }else{
// 	$val4 = 'eagle-sort';
// }
// if($label=="userRole"){ 
// 	$orderby = 'ORDER BY ur_name '.$value;
// 	$val8 = strtolower('sort-'.$value);
// }else{
// 	$val8 = 'eagle-sort';
// }

if ($orderby) {
    $orderby .= '';
} else {
    $orderby .= ' ORDER BY apg_date_added DESC';
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ SEARCH COLUMNS @@@@@@@@@@@@@@@@@@@@@@@@@@@@
$searchColumns = array(
	'user_name', 
	'gr_name',
	'user_surname',
	'user_othername',
);

if($searchWord !== '' && $searchWord !== '0'){
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
//AND req_division = '$div' AND req_app1_user_id IS NOT NULL
$sql = "SELECT * FROM groups, sysuser, approval_group WHERE groups.gr_id=approval_group.apg_name AND sysuser.user_id=approval_group.apg_user $search";
$db = new Db();
$select = $db->select($sql);

$totalRecords = $db->num_rows();



//@@@@@@@@@@@@@@@@@@@@@@@  FILTEER RECORDS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//' OFFSET '.($eagleActivePage-1).' ROWS FETCH NEXT '.$rowsPerPage.' ROWS ONLY';
if ($rowsPerPage) {
    $limit = ' OFFSET '.(($eagleActivePage-1)*$rowsPerPage).' ROWS FETCH NEXT '.$rowsPerPage.' ROWS ONLY';
}

if($searchWord !== '' && $searchWord !== '0'){
	$sql = "SELECT * FROM groups, sysuser, approval_group WHERE groups.gr_id=approval_group.apg_name AND sysuser.user_id=approval_group.apg_user $search $orderby $limit";
	$db = new Db();
	$select = $db->select($sql);
	$filtered = $db->num_rows();
	$f = "<b>Filter: '".$sw."'&nbsp; </b>";
	//$f = "Filtered:&nbsp;".number_format($filtered).'&nbsp;out&nbsp;of&nbsp;'.number_format($totalRecords);
}else{
	//$f = "Total&nbsp;Records:&nbsp;".number_format($totalRecords);

	$sql = "SELECT * FROM groups, sysuser, approval_group WHERE groups.gr_id=approval_group.apg_name AND sysuser.user_id=approval_group.apg_user $search $orderby $limit";
	$db = new Db();
	$select = $db->select($sql);
	//$f = "Filtered:&nbsp;".number_format($db->num_rows()).'&nbsp;out&nbsp;of&nbsp;'.number_format($totalRecords);
}

//echo $sql;


	echo '<table class="code-eagle-table" cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
	//echo '<thead>';
	echo '<tr>';
	echo '<th style="width:30px">No.</th>';
	echo '<th id="PF" class="'.$val0.'">PF</th>';
	echo '<th id="gname" class="'.$val2.'">Group Name</th>';
	echo '<th id="name" class="'.$val3.'">Approver\'s Name</th>';
	echo '<th id="date" class="'.$val1.'">Date Added</th>';
	echo '<th>Added By</th>';
	// if($access->sectionAccess(1, $t->page, 'E') || $access->sectionAccess(1, $t->page, 'D')){
		echo '<th width="">Action</th>';
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
		echo '<td>'.$user_name.'</td>';
		echo '<td>'.$gr_name.'</td>';
		echo '<td>'.$user_surname.' '.$user_othername.'</td>';
		echo '<td>'.Feedback::date_fm($apg_date_added).'</td>';
		echo '<td>'.$t->full_name($apg_added_by).'</td>';
		//echo '<td><a class="eagle-load" href="'.return_url().'requisition/view-requisition/'.$req_id.'">View Details</a></td>';

		echo '<td>';
		echo '<a href="'.return_url().'approval-group/edit-approval-group/'.$apg_id.'" class="eagle-load btn btn-xs btn-success">Edit</a> &nbsp;';
		echo '<a title="Remove '.($user_surname).' '.($user_othername).'" onclick="return confirm(\'Do you really want to remove'.($user_surname).' '.($user_othername).'\');" class="btn btn-xs btn-danger" href="'.return_url().'approval-group/delete-approval-group/'.$apg_id.'">Remove User</a>';
		//echo $this->action('delete','approvalgroup/delete-approval-group/'.$apg_id, 'Delete');
		echo '</td>';

		echo '</tr>';
		
		//////////////////////////////////REPORT STEP 2//////////////////////////////////	
		 
		
		/////////////////////////////////////////////////////////////////////////////
		
	}
	echo '</tbody>';
	
	echo '</table>';

//================================

$pages = ceil($totalRecords/$rowsPerPage);

echo links($f, $eagleActivePage, $rowsPerPage, $pages, $totalRecords);

function status($req){
	return 1;
}

function links($f, $active, $numPerPage, $pages,$totalRecords, $color='blue', $active_color='black'){
    echo "<style>.pagination-new{margin:0; margin-top:10px; padding-left:0;} .pagination-new li{margin:1px; margin-left:0;padding:1px;}.page-active{color:white; background-color:$color; }.page-color{color:white; background-color:$active_color;} .page-color, .page-active{padding:2px 10px; border-radius:5px;margin-left:0; margin-right:2px;}.page-color li a{text-decoration:none;}</style>";

    $v = "";

    if (empty($active)) {
        $active = 1;
    }
    $record1 = ($active-1)*$numPerPage+1;
    $record2 = $active != $pages ? $active*$numPerPage : $totalRecords;

	$v .= $f.'<b>Showing '.number_format($record1).' to '.number_format($record2).' of '.number_format($totalRecords).'</b>';
    $v .= '<ul class="pagination-new">';

    if (empty($active)) {
        $active = 1;
    }

    if ($active > 1) {
        $v .= '<li style="display:inline"><span data-number="'.($active-1).'" class="eagle-page-link page-color">Previous</span></li>';
    } else {
        $v .= '<li style="display:inline"><span style="opacity:0.5" class="page-color" >Previous</a></li>';
    }

    if ($active > 1) {
        $v .= '<li style="display:inline"><span data-number="1" class="eagle-page-link page-color">First</span></li>';
    } else {
        $v .= '<li style="display:inline"><span class="page-color" style="opacity:0.5">First</span></li>';
    }

    if(empty($active)){
        $first = 1; 
        $last = 10;
    }else{
        $first = $active-5;
        $last = $active+5;
        if($last > $pages){
            $last = $pages;
            $first += ($last-$pages);
            if ($first < 1) {
                $first = 1;
            }
        }

        if($first<1){
            $last += abs($first);
            $first = 1;

            if($last > $pages){
                $last = $pages;
            }
        }

         
        //$first = 1; 
        //$last = 10;
    }

    for($i=$first; $i<=$last; $i++){
        if ($i == $active) {
            $v .= '<li style="display:inline"><span data-number="'.$i.'" class="page-active">'.$i.'</span></li>';
        } else {
            $v .= '<li style="display:inline"><span data-number="'.$i.'" class="eagle-page-link page-color">'.$i.'</span></li>';
        }
    }

    if ($active < $pages) {
        $v .= '<li style="display:inline"><span data-number="'.($active+1).'" class="eagle-page-link page-color">Next</span></li>';
    } else {
        $v .= '<li style="display:inline"><span class="page-color" style="opacity:0.5">Next</a></li>';
    }

    if ($active < $pages) {
        $v .= '<li style="display:inline"><span data-number="'.($pages).'" class="eagle-page-link page-color">Last</span></li>';
    } else {
        $v .= '<li style="display:inline"><span class="page-color" style="opacity:0.5">Last</span></li>';
    }

    $v .= '</ul>';

    if ($pages >= 2) {
        return $v;
    } else {
        return "";
    }

}
