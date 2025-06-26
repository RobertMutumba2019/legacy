<?php 
include_once __DIR__ . "/classes/init.php";

include_once __DIR__ . "/classes/DbSunServer.php";
error_reporting(null);
$user_id = $_SESSION['CENTENARY_USER_ID'];

$value = $_POST['value'];
$id = $_POST['id'];
echo '<input type="hidden" id="idid" value="'.$id.'">';
$sun_db = new DbSunServer();

$sql = "SELECT A.ITEM_CODE as db_item_code,A.DESCR as db_description,A.DFLT_INVY_UNIT as db_uom,A.DFLT_LOCN_ID 
FROM GTF_ITEM A, GTF_ITEM_ACNT_RECOG B
WHERE A.ITEM_CODE = B.ITEM_CODE
AND (B.ACNT_RECOG_CODE ='IM_INVENTORY' AND B.ACNT_CODE BETWEEN '4100002' AND '4100006')";

//$sql = "SELECT ITEM_CODE as db_item_code,UNIT_OF_WGHT as db_uom, DESCR as db_description FROM GTF_ITEM";

$sun_select = $sun_db->select($sql);
echo $db->error();
echo '<div class="row">';
echo '<div class="col-md-12">';
echo '<h3>Search & Select Item Code or Name</h3>';
echo '<select id="itemSelected" style="width:100%;" class=" select4" name="">';
echo '<option value="">--Select--</option>';
if(is_array($sun_select)){


foreach($sun_select as $row){
	extract($row);
	if($db_description == $value){
		echo '<option selected value="'.$db_item_code.'" name="">'.$db_item_code." - ".ucwords(strtolower($db_description)).'</option>';
	}else{
		echo '<option value="'.$db_item_code.'" name="">'.$db_item_code." - ".ucwords(strtolower($db_description)).'</option>';
	}
}
}
echo '</select>';
echo '</div>';

echo '<div class="col-md-12" style="margin-top:40px;">';
echo  '<button type="button" class="ok-btn btn btn-primary" style="width:100px;">Ok</button>';
echo '&nbsp; &nbsp;';
echo  '<button type="button" class="cancel-btn btn btn-danger" style="width:100px;">Cancel</button>';
echo '</div>';

echo '</div>';
?>

	<script type="text/javascript">
		$(document).ready(function(){	
	        $('.select4').select2();
	        $('.select2').select2();
	        $('.cancel-btn').click(function(){
	        	$('.search-area').hide();
	        });
	        $('.ok-btn').click(function(){
	        	var itemSelected = $('#itemSelected').val();       	
	       		form_data = new FormData();
				form_data.append('itemSelected', itemSelected);
				if(itemSelected != ""){
			        $.ajax({
				        url: '<?php echo return_url(); ?>ajax/get-item-details.php',
				        type: "POST",
			            data: form_data,
			            contentType: false,
			            cache: false,
			            processData:false,
			            success: function(data){
			            	var id=$('#idid').val();
			            	var dat = JSON.parse(data);	

			            	$('#item_description'+id).html('<option value="'+dat.description+'">'+dat.description+'</option>');		              	
			            	$('#uom'+id).val(dat.uom);	              	
			            	$('#price'+id).val(dat.price);		              	
			            	$('#item_code'+id).html('<option value="'+dat.code+'">'+dat.code+'</option>');

	        				$('.search-area').hide();
				        }
				    });
		    	}

	    	});
	        ////////////////////////////////////
		});
    </script>

