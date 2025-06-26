<?php 
include_once __DIR__ . "/classes/init.php";
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];
$attach_id = $_POST['attachmentID'];

// require_once("http://localhost:8080/myPHPapp/java/Java.inc");
// $world = new java("HelloWorld");

$t = new Requisition();

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
	$orderby = 'ORDER BY req_date_added '.$value;
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
    $orderby .= ' ORDER BY req_date_added DESC';
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
?>
<script type="text/javascript">		
	$('.eagle-load').off('click').on('click',function(e){
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

//@@@@@@@@@@@@@@@@@@@@@@@  TOTOAL RECORDS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
$sql = "SELECT * FROM requisition, sysuser WHERE req_app1_user_id IS NOT NULL AND req_pushed IS NOT NULL AND req_status = 1 AND user_id=req_added_by AND req_added_by IS NOT NULL $search";
$db = new Db();
$select = $db->select($sql);

$totalRecords = $db->num_rows();



//@@@@@@@@@@@@@@@@@@@@@@@  FILTEER RECORDS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//' OFFSET '.($eagleActivePage-1).' ROWS FETCH NEXT '.$rowsPerPage.' ROWS ONLY';
if ($rowsPerPage) {
    $limit = ' OFFSET '.(($eagleActivePage-1)*$rowsPerPage).' ROWS FETCH NEXT '.$rowsPerPage.' ROWS ONLY';
}

if($searchWord !== '' && $searchWord !== '0'){
	$sql = "SELECT * FROM requisition, sysuser WHERE req_app1_user_id IS NOT NULL AND req_pushed IS NOT NULL AND req_status = 1 AND user_id=req_added_by AND req_added_by IS NOT NULL $search $orderby $limit";
	$db = new Db();
	$select = $db->select($sql);
	$filtered = $db->num_rows();
	$f = "<b>Filter: '".$sw."'&nbsp; </b>";
	//$f = "Filtered:&nbsp;".number_format($filtered).'&nbsp;out&nbsp;of&nbsp;'.number_format($totalRecords);
}else{
	//$f = "Total&nbsp;Records:&nbsp;".number_format($totalRecords);

	$sql = "SELECT * FROM requisition, sysuser WHERE req_app1_user_id IS NOT NULL AND req_pushed IS NOT NULL AND req_status = 1 AND user_id=req_added_by AND req_added_by IS NOT NULL $search $orderby $limit";
	$db = new Db();
	$select = $db->select($sql);
	//$f = "Filtered:&nbsp;".number_format($db->num_rows()).'&nbsp;out&nbsp;of&nbsp;'.number_format($totalRecords);
}

echo '<input type="hidden" value="'.$searchWord.'" id="searchWord"/>';
echo '<table class="code-eagle-table" cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
	//echo '<thead>';
	echo '<tr>';
	echo '<th style="width:30px">No.</th>';
	echo '<th id="date" class="'.$val1.'">Date</th>';
	echo '<th id="number" class="'.$val2.'">Req. No.</th>';
	echo '<th id="number" class="">Issue Ref.</th>';
	echo '<th id="title" class="'.$val3.'">Title</th>';
	echo '<th title="Lines in the requisition">L</th>';
	echo '<th >Status</th>';
	echo '<th >Requested By</th>';
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

		$v = new Db();
        $vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");

		echo '<tr class="req'.$req_id.'">';
		echo '<td style="width:30px;"><center>'.($i).'.</center></td>';
		echo '<td>'.Feedback::date_fm($req_date_added).'</td>';
		echo '<td>'.$req_number.'</td>';
		echo '<td>'.$req_issue_reference.'</td>';
		echo '<td>'.$req_title.'</td>';
		echo '<td>'.$t->total("requisition_item", "ri_ref", $req_ref).'</td>';
		echo '<td>'.$t->status($req_id).'</td>';
		echo '<td>'.$t->full_name($req_added_by).'</td>';
		echo '<td>';
		echo '<span style="margin:5px;" data-id="'.$req_id.'" class="item-details btn btn-xs btn-primary"><span class="words-'.$req_id.'">Show Items</span> ('.number_format($v->num_rows()).')</span>';
		//echo '<span id="btn'.$req_id.'" style="margin:5px;" class="push-to-sun btn btn-xs btn-success" data-id="'.$req_id.'" data-req="'.$req_number.'">Push to SUN</span>';
		echo '</td>';
		echo '</tr>';
        echo '<tr class="req'.$req_id.'">';
        echo '<td colspan="8"  style="">';
        echo '<div class="details-of-items-'.$req_id.'" style="display:none;padding:20px">';
        echo '<table id="table2" border="1" style="width:100%">';
        echo '<tr>';
        echo '<th style="width:20px;">No.</th>';
        echo '<th>Item Code</th>';
        echo '<th>Item Description</th>';
        echo '<th>Quantity</th>';
        echo '<th>UOM</th>';
        // echo '<th>Price</th>';
        echo '</tr>';
        $vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
        $non =1;
		if(is_array($vv)){

		
        foreach($vv as $r){
            extract($r);
            $color = "";//($non%2==0)?"blue":"black";
            echo '<tr style="color:'.$color.'">';
            
            echo '<td>'.($non++).'.</td>';
            echo '<td>'.$ri_code.'</td>';
            echo '<td>'.$ri_description.'</td>';
            echo '<td>'.$ri_quantity.'</td>';
            echo '<td>'.$ri_uom.'</td>';
            //echo '<td>'.$ri_price.'</td>';
            echo '</tr>';
        }
	}
        $non++;
        echo '</table>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
		
		//////////////////////////////////REPORT STEP 2//////////////////////////////////	
		 
		
		
		/////////////////////////////////////////////////////////////////////////////
     }	
	}
	echo '</tbody>';
	
	echo '</table>';

//================================

?>
<script type="text/javascript">
    $(function(){
    	var urlPath = $('#urlPath').val();
        
       // $('.item-details').click(function(){
        $(document).off('click','.item-details').on('click', '.item-details', function(e){
            var req = $(this).attr('data-id');
            $('.details-of-items-'+req).toggle();
            $(this).toggleClass('btn-danger');
            $(this).toggleClass('btn-primary');
            if($('.words-'+req).html()=="Show Items"){
                $('.words-'+req).html("Hide Items");
            }else{                        
                $('.words-'+req).html("Show Items");
            }
         });
    //     $('.push-to-sun').click(function(){
    //         var reqNo = $(this).attr('data-req');
    //         if(confirm("Do you really want to push this requisition ("+reqNo+") to SUN")){
    //         	var req = $(this).attr('data-id');
    //         	$('.push-to-sun').attr("disabled", true);
				// $(this).html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/> Pushing...');
					
				// var form_data = new FormData();

				// form_data.append('req', req);

				// $.ajax({
				// 	url: urlPath+"ajax/push-to-sun.php",
				// 	type: "POST",
				// 	data: form_data,
				// 	contentType: false,
				// 	cache: false,
				// 	processData:false,
				// 	success: function(data){
				// 		var	values = JSON.parse(data);
				// 		$('.push-to-sun').attr('disabled', false);

				// 		if(values.message=="Error"){
				// 			swal({
				// 				title: "Please review the following:",
				// 				text:values.details,
				// 				type:"error",
				// 				icon:"error",
				// 			}).then(function (){
				// 				$('#btn'+req).html('Push to SUN');
				// 			});
				// 		}else{
				// 			swal({							    
				// 				text:values.details,
				// 			    type: "success",
				// 			    icon: "success",
				// 			}).then(function() {							    
				// 				$('.req'+req).hide();
				// 			});
				// 		}
				// 	}

				// });
    //         }else{
    //         	return false;
    //         }
    //     });
    });
</script>
<?php
$pages = ceil($totalRecords/$rowsPerPage);

echo links($f, $eagleActivePage, $rowsPerPage, $pages, $totalRecords);

//echo '<button id="export" style="margin-top:10px;" class="btn btn-xs btn-primary"><i class="fa fa-fw fa-download"></i> Export All Requisitions to excel</button>&nbsp;&nbsp;<button id="export2" style="margin-top:10px;" class="btn btn-xs btn-danger"><i class="fa fa-fw fa-download"></i> Export All Requisitions with Items to excel</button><span  style="margin-top:10px;" id="status2"></span>';
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
				url: urlPath+"ajax/_all-requisitions-table-export.php",
				type: "POST",
				data: form_data,
				contentType: false,
				cache: false,
				processData:false,
				success: function(data){
					$('#export').attr('disabled', false);
					$('#status2').html('');
					window.open(urlPath+"ajax/_all-requisitions-table-export.php?searchWord="+$('#searchWord').val()+"&item=0",'_blank');

					$('#export').html('<i class="fa fa-fw fa-download"></i> Export All Requisitions to excel');
				}

			});
		}
	});
	$(document).off('click','#export2').on('click','#export2', function(e){
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
				url: urlPath+"ajax/_all-requisitions-table-export.php",
				type: "POST",
				data: form_data,
				contentType: false,
				cache: false,
				processData:false,
				success: function(data){
					$('#export2').attr('disabled', false);
					$('#status2').html('');
					window.open(urlPath+"ajax/_all-requisitions-table-export.php?searchWord="+$('#searchWord').val()+"&item=1",'_blank');

					$('#export2').html('<i class="fa fa-fw fa-download"></i> Export All Requisitions to excel');
				}

			});
		}
	});
</script>
<?php


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

?>
<script type="text/javascript">		
	$('.eagle-load').off('click').on('click',function(e){
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
?>
