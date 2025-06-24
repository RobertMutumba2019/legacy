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
if($label=="date"){ 
	$orderby = 'ORDER BY gr_date_added '.$value;
	$val1 = strtolower('sort-'.$value);
}else{
	$val1 = 'eagle-sort';
}
if($label=="number"){ 
	$orderby = 'ORDER BY req_number '.$value;//.', cust_last_name '.$value;
	$val2 = strtolower('sort-'.$value);
}else{
	$val2 = 'eagle-sort';
}
if($label=="title"){ 
	$orderby = 'ORDER BY req_title '.$value;
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
    $orderby .= ' ORDER BY gr_date_added DESC';
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ SEARCH COLUMNS @@@@@@@@@@@@@@@@@@@@@@@@@@@@
$searchColumns = array(
	'req_title', 
	'req_number',
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
$sql = "SELECT * FROM groups, approval_matrix WHERE gr_matrix = ap_id $search";
$db = new Db();
$select = $db->select($sql);

$totalRecords = $db->num_rows();



//@@@@@@@@@@@@@@@@@@@@@@@  FILTEER RECORDS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//' OFFSET '.($eagleActivePage-1).' ROWS FETCH NEXT '.$rowsPerPage.' ROWS ONLY';
if ($rowsPerPage) {
    $limit = ' OFFSET '.(($eagleActivePage-1)*$rowsPerPage).' ROWS FETCH NEXT '.$rowsPerPage.' ROWS ONLY';
}

if($searchWord !== '' && $searchWord !== '0'){
	$sql = "SELECT * FROM groups, approval_matrix WHERE gr_matrix = ap_id $search $orderby $limit";
	$db = new Db();
	$select = $db->select($sql);
	$filtered = $db->num_rows();
	$f = "<b>Filter: '".$sw."'&nbsp; </b>";
	//$f = "Filtered:&nbsp;".number_format($filtered).'&nbsp;out&nbsp;of&nbsp;'.number_format($totalRecords);
}else{
	//$f = "Total&nbsp;Records:&nbsp;".number_format($totalRecords);

	$sql = "SELECT * FROM groups, approval_matrix WHERE gr_matrix = ap_id $search $orderby $limit";
	$db = new Db();
	$select = $db->select($sql);
	//$f = "Filtered:&nbsp;".number_format($db->num_rows()).'&nbsp;out&nbsp;of&nbsp;'.number_format($totalRecords);
}

//echo $sql;


	echo '<table class="code-eagle-table" cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
	//echo '<thead>';
	echo '<tr>';
	echo '<th style="width:30px">No.</th>';
	echo '<th id="groupname" class="'.$val1.'">Group Name</th>';
	echo '<th id="number" class="'.$val2.'">Division/Location</th>';
	echo '<th id="title" class="'.$val3.'">Date Added</th>';
	echo '<th id="addedBy" class="'.$val3.'">Added By</th>';	// if($access->sectionAccess(1, $t->page, 'E') || $access->sectionAccess(1, $t->page, 'D')){
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
		echo '<td>'.$gr_name.'</td>';
		echo '<td>'.$ap_unit_code.' - <b>'.$ap_code.'</b></td>';
		echo '<td>'.FeedBack::date_fm($gr_date_added).'</td>';
		echo '<td>'.$t->full_name($gr_added_by).'</td>';
		echo '<td>';
		echo '<a href="'.return_url().'groups/edit-group/'.$gr_id.'" class="eagle-load btn btn-xs btn-primary">Edit</a>';
		echo '</td>';
		echo '</tr>';
		
		//////////////////////////////////REPORT STEP 2//////////////////////////////////	
		 
		
		/////////////////////////////////////////////////////////////////////////////
		
	}
	echo '</tbody>';
	
	echo '</table>';

//================================
?>
<script type="text/javascript">		
	$(document).off('click','.eagle-load').on('click','.eagle-load',function(e){
		var urlPath = $('#urlPath').val();
		e.preventDefault();
		var href = $(this).attr('href');
		$('#EagleContainer').fadeTo('normal', 0.4).append('<img class="eaglepreview" src="'+urlPath+'images/loading3.gif" alt="loading..."/>');
		
		var form_data = new FormData();
		
		form_data.append('href', href);
		$.ajax({
			url: urlPath+"ajax/__EAGLE_route.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
		        $('#EagleContainer').fadeTo('normal', 1);
				$('#EagleContainer').html(data);
			}
		});
	});
</script>
<?php

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
