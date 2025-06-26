<?php 
include_once __DIR__ . "/classes/init.php";	
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];
$attach_id = $_POST['attachmentID'];

$t = new BeforeAndAfter();

$label = $_POST['label'];
$value = $_POST['value'];

$rowsPerPage = $_POST['rowsPerPage'];
$searchWord = urldecode(trim($_POST['searchWord']));
$eagleActivePage = $_POST['eagleActivePage'];
if ($eagleActivePage<=1||empty($eagleActivePage)) {
    $eagleActivePage = 1;
}
//echo $id.' '.$value;

echo '<input type="hidden" value="'.$searchWord.'" id="searchWord"/>';

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ ORDER BY COLUMNS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@
if($label=="pf"){ 
	$orderby = 'ORDER BY check_number '.$value;
	$val1 = strtolower('sort-'.$value);
}else{
	$val1 = 'eagle-sort';
}
if($label=="name"){ 
	$orderby = 'ORDER BY user_surname '.$value;//.', cust_last_name '.$value;
	$val2 = strtolower('sort-'.$value);
}else{
	$val2 = 'eagle-sort';
}
if($label=="email"){ 
	$orderby = 'ORDER BY user_email '.$value;
	$val3 = strtolower('sort-'.$value);
}else{
	$val3 = 'eagle-sort';
}
if($label=="lastLoggedIn"){ 
	$orderby = 'ORDER BY user_last_logged_in '.$value;
	$val4 = strtolower('sort-'.$value);
}else{
	$val4 = 'eagle-sort';
}
if($label=="userRole"){ 
	$orderby = 'ORDER BY ur_name '.$value;
	$val8 = strtolower('sort-'.$value);
}else{
	$val8 = 'eagle-sort';
}

if ($orderby) {
    $orderby .= ', check_number ASC';
} else {
    $orderby .= ' ORDER BY check_number ASC';
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ SEARCH COLUMNS @@@@@@@@@@@@@@@@@@@@@@@@@@@@
$searchColumns = array(
	'check_number', 
	'user_surname',
	'user_othername',
	'user_email',
	'ur_name',
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
$sql = "SELECT * FROM sysuser, user_role WHERE ur_id=user_role $search";
$db = new Db();
$select = $db->select($sql);

$totalRecords = $db->num_rows();


//@@@@@@@@@@@@@@@@@@@@@@@  FILTEER RECORDS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//' OFFSET '.($eagleActivePage-1).' ROWS FETCH NEXT '.$rowsPerPage.' ROWS ONLY';
if ($rowsPerPage) {
    $limit = ' OFFSET '.(($eagleActivePage-1)*$rowsPerPage).' ROWS FETCH NEXT '.$rowsPerPage.' ROWS ONLY';
}

if($searchWord !== '' && $searchWord !== '0'){
	$sql = "SELECT * FROM sysuser, user_role WHERE ur_id=user_role $search $orderby $limit";
	$db = new Db();
	$select = $db->select($sql);
	$filtered = $db->num_rows();
	$f = "<b>Filter: '".$sw."'&nbsp; </b>";
	//$f = "Filtered:&nbsp;".number_format($filtered).'&nbsp;out&nbsp;of&nbsp;'.number_format($totalRecords);
}else{
	//$f = "Total&nbsp;Records:&nbsp;".number_format($totalRecords);

	$sql = "SELECT * FROM sysuser, user_role WHERE ur_id=user_role $search $orderby $limit";
	$db = new Db();
	$select = $db->select($sql);
	//$f = "Filtered:&nbsp;".number_format($db->num_rows()).'&nbsp;out&nbsp;of&nbsp;'.number_format($totalRecords);
}

//echo $sql;

	echo '<table class="code-eagle-table" cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
	//echo '<thead>';
	echo '<tr>';
	echo '<th style="width:30px">No.</th>';
	//echo '<th id="">PF</th>';
	echo '<th id="name" class="'.$val2.'">Name</th>';
	echo '<th id="email" class="'.$val3.'">Email</th>';
	echo '<th id="userRole" class="'.$val8.'">User Role</th>';
	echo '<th >Location/Division</th>';
	echo '<th>Code</th>';
	echo '<th id="lastLoggedIn" class="'.$val4.'">Last Logged in</th>';
	echo '<th id="online_offline" class="'.$val5.'">Online/Offline</th>';
	//echo '<th id="status" class="'.$val6.'">Status</th>';
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
	if(is_array($select)){

	
	foreach($select as $row){
		$i = $i++;
		$i++;

		extract($row);
		$user_online = ($user_online)?'Online':'Offline';
		$user_active = ($user_active)?'Active':'Disabled';

		$gr_name = "";
		$ap_code = "";
		$sql = "SELECT gr_name,ap_code FROM approval_matrix, groups, sysuser, approval_group WHERE groups.gr_id=approval_group.apg_name AND sysuser.user_id=approval_group.apg_user AND gr_matrix = ap_id AND user_id = '$user_id'";
		$g = new Db();
		$gg = $g->select($sql);
		if ($g->num_rows()) {
			if (is_array($gg) && isset($gg[0]) && is_array($gg[0])) {
            extract($gg[0]);
			}
        }

		echo '<tr>';
		echo '<td style="width:30px;"><center>'.($i).'.</center></td>';
		//echo '<td>'.($check_number).'</td>';
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
		//echo '<td>'.$user_active.'</td>';

		//if($access->sectionAccess(1, $t->page, 'E') || $access->sectionAccess(1, $t->page, 'D')){
		
			echo '<td>';
			// if($access->sectionAccess(1, $t->page, 'E'))
			    echo'<a class="eagle-load btn btn-primary btn-xs" href="'.return_url().'users/edit-user/'.$user_id.'">View / Edit </a>';
			    //echo'<a class="eagle-load btn btn-primary btn-xs" href="'.return_url().'users/edit-user/'.$user_id.'">View</a>';
			   // echo '<a '
				//echo $t->action('edit','users/edit-user/'.$user_id, 'Edit');
			// if($access->sectionAccess(1, $t->page, 'D'))
				//echo $t->action('delete','Customers/delete-customer/'.$cust_id, 'Delete');

			echo '</td>';
		//}
		echo '</tr>';
		
		//////////////////////////////////REPORT STEP 2//////////////////////////////////	
		 
		
		/////////////////////////////////////////////////////////////////////////////
	}	
	}
	echo '</tbody>';
	
	echo '</table>';


//================================

$pages = ceil($totalRecords/$rowsPerPage);

echo links($f, $eagleActivePage, $rowsPerPage, $pages, $totalRecords);



echo '<button id="export" style="margin-top:10px;" class="btn btn-xs btn-primary"><i class="fa fa-fw fa-download"></i> Export All Users to excel</button><span  style="margin-top:10px;" id="status2"></span>';
?>
<script type="text/javascript">
	$(document).off('click','#export').on('click','#export', function(e){
		e.preventDefault();

		// if(!confirm('Are sure you want to export all users')){
		// 	return false;
		// }
		$(this).attr('disabled', true);
		$(this).html('Processing. Please wait...');
		
		{

			var urlPath = $('#urlPath').val();
			$('#status2').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
				
			var form_data = new FormData();
			form_data.append('searchWord', $('#searchWord').val());
			$.ajax({
				url: urlPath+"ajax/users-table-export.php",
				type: "POST",
				data: form_data,
				contentType: false,
				cache: false,
				processData:false,
				success: function(data){
					$('#export').attr('disabled', false);
					$('#status2').html('');
					window.open(urlPath+"ajax/users-table-export.php?searchWord="+$('#searchWord').val(),'_blank');

					$('#export').html('<i class="fa fa-fw fa-download"></i> Export All Users to excel');
				}

			});
		}
	});
</script>
<?php
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
