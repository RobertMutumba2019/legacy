
<?php
Class Stores extends BeforeAndAfter{
	public $page = "STORES";
	
	public function __construct(){
		new AccessRights();
		
	}
	
	
	public static function getLinks(){
		$page = "STORES";
		
		return array(
			array(
				"link_name"=>"pending", 
				"link_address"=>"stores/pending",
				"link_icon"=>"fa-circle",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"Pushed to SUN", 
				"link_address"=>"stores/pushed",
				"link_icon"=>"fa-check",
				"link_page"=>$page,
				"link_right"=>"V",
			),
		);
	}
	
	public function deletegroupAction(){
		$id = portion(3);
		$this->deletor("groups", "gr_id",  $id, 'groups/all-groups');
	}

	public function pendingAction(){
	$db = new Db();

	$errors = array();

	if(isset($_POST['monthDetails'])){
    		$month = $_POST['month'];
    		$year = $_POST['year'];

    		if(empty($year)||empty($month)){
    			FeedBack::error("Please select Year and Month");
    		}else{

    			if($year == "all"){
    				$start_date = strtotime('2020-10-20 12:00:00 am');
    			}elseif($year !="all" && $month=="all"){    				
    				$start_date = strtotime("$year-01-01 12:00:00 am");
    			}else{    				
    				$start_date = strtotime("$year-$month-01 12:00:00 am");
    			}

    			if($month == "all" && $year == "all"){
    				$end_date = time();
    			}elseif($month=="all"){
    				$end_date = strtotime("$year-12-01 12:00:00 am");
    				$end_date = strtotime(date('Y-m-t', $end_date).' 11:59:59 pm');
    			}else{
    				$end_date = strtotime("$year-$month-01 12:00:00 am");
    				$end_date = strtotime(date('Y-m-t', $end_date).' 11:59:59 pm');
    			}
    		}

    		// echo 'Start:'.FeedBack::date_fm($start_date);
    		// echo ' <br/>';
    		// echo 'End :'.FeedBack::date_fm($end_date);

    	}elseif(isset($_POST['searchPeroid'])){
    		$from = $_POST['from'];
    		$to = $_POST['to'];

    		$errors = array();

    		if(empty($from) || empty($to) ){
    			FeedBack::error("Please Enter Both Dates (From and To)");
    		}elseif($from && $to){
    			$start_date = strtotime($from.' 12:00:00 am');
    			$end_date = strtotime($to.' 11:59:59 pm');
    		}

    		if($start_date>$end_date){
    			$errors[] = "From Date <b>(".date('Y-m-d', $start_date).")</b> cannot be greater than To Date <b>(".date('Y-m-d', $end_date).")</b>";
    		}

    		if ($errors !== []) {
                FeedBack::errors($errors);
            }

    	}elseif(isset($_POST['searchRequisition'])){
    		$search_type = $_POST['search_type'];
    		$search_name = trim($_POST['search_name']);
    		
    		$errors = array();
    		if ($search_name === '' || $search_name === '0') {
                $errors[] = "Please Enter Invoice Number";
            }

    		if ($errors !== []) {
                FeedBack::errors($errors);
            }
    	}

    	if(($start_date === 0 || $start_date === false) && ($end_date === 0 || $end_date === false)){
    		$start_date = strtotime(date('Y-m-d').' 12:00:00 am');
    		$end_date = time();
    	}

    	if(empty($year) && empty($month)){
    		$year = date('Y');
    		$month = date('m');
    	}

    	$from = date('Y-m-d', $start_date);
    	$to = date('Y-m-d', $end_date);

    	//echo $from;

	echo '<form action="" method="post">';
	?>
	<div class="row">
			<div class="col-md-12">
	<button type="button" class="switch" data-id="rangeDetails">Date Range</button>
	<button type="button" class="switch" data-id="numberDetails">Requisition Number</button>
	<button type="button" class="switch" data-id="monthDetails">Month</button>

	<div id="rangeDetails" class="hide2">
	<?php
	echo '<div style="margin-top:20px;">';
	echo '<div style="float:left; margin-right:20px;">';
    	echo '<label>From&nbsp;&nbsp;</label>';
    	echo '<input id="date1" type="date" name="from" value="'.$from.'"><br/>';
    	echo '</div>';
    	echo '<div style="float:left; margin-right:20px;">';
    	echo '<label>To&nbsp;&nbsp;</label>';
    	echo '<input id="date2" type="date" name="to" value="'.$to.'">';
    	echo '</div>';
    	   	
    	echo '<div style="float:left; margin-right:20px;">';
    	echo '<button type="submit" name="searchPeroid" class="btn btn-primary btn-xs pt-0 pb-0"><i class="fa fa-fw fa-search"></i> Show</button>';
    	echo '</div>';
    	echo '</div>';
	?>

	<script type="text/javascript">
		$(document).ready(function(){
			$('input[type ="date"]').bootstrapMaterialDatePicker({ 
                weekStart : 0, 
                time: false, 
                //minDate : new Date() 
            });
		});
	</script>
	</div>
	<div id="numberDetails" class="hide2">
	<?php
	echo '<div style=" margin-top:20px;">';
    	echo '<label>Search All Requisition which &nbsp;</label>';
    	$st = array('Contain', 'Start With', 'Ends With', 'Are Equal to');
    	echo '<select name="search_type">';
    	$v =0;
    	foreach($st as $t){
    		if ($search_type == $v) {
                echo '<option selected="selected" value="'.($v++).'">'.$t.'</option>';
            } else {
                echo '<option value="'.($v++).'">'.$t.'</option>';
            }
    	}
    	echo '</select> &nbsp; ';
    	echo '<input type="text" value="'.$search_name.'" name="search_name" placeholder="Enter Invoice number" />';
    	echo '<button name="searchRequisition"><i class="fa fa-fw fa-search"></i> Search</button>';
    	echo '</div>';

	?>
	</div>
	<div id="monthDetails" class="hide2">
	<?php
	echo '<div style="float:left; margin-top:20px;">';
    	echo '<label>Year&nbsp;&nbsp;</label>';
    	echo '<select name="year">';
    	echo '<option value="">Select</option>';
    	// if($year == "all")
    	// 	echo '<option selected value="all">All</option>';
    	// else
    	// 	echo '<option value="all">All</option>';
    	for($i=2020; $i<=date('Y'); $i++){
    		if ($year == $i) {
                echo '<option selected value="'.$i.'">'.$i.'</option>';
            } else {
                echo '<option value="'.$i.'">'.$i.'</option>';
            }
    	}
    	echo '</select>';
    	echo '</div>';
	echo '<div style="float:left; margin-top:20px;">';
    	echo '<label>&nbsp; Month&nbsp;&nbsp;</label>';
    	echo '<select name="month">';
    	echo '<option value="">Select</option>';
    	if ($month == "all") {
            echo '<option selected value="all">All</option>';
        } else {
            echo '<option value="all">All</option>';
        }
    	for($i=1; $i<=12; $i++){
    		if ($month == $i) {
                echo '<option selected value="'.$i.'">'.$this->month_name($i).'</option>';
            } else {
                echo '<option value="'.$i.'">'.$this->month_name($i).'</option>';
            }
    	}
    	echo '</select>';
    	echo '</div>';
    	echo '<div style="float:left; margin-left:20px; margin-top:20px;">';
    	echo '<button type="submit" name="monthDetails" class="btn btn-primary btn-xs pt-0 pb-0"><i class="fa fa-fw fa-search"></i> Show</button>';
    	echo '</div>';
    	echo '</div>';
		echo '<div class="clearfix"></div>';

    	$db = new Db();

    	if (isset($_POST['searchRequisition'])) {
            if ($search_type==0) {
                $sql = "SELECT * FROM requisition WHERE req_number LIKE '%$search_name%' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==1) {
                $sql = "SELECT * FROM requisition WHERE req_number LIKE '$search_name%' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==2) {
                $sql = "SELECT * FROM requisition WHERE req_number LIKE '%$search_name' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==3) {
                $sql = "SELECT * FROM requisition WHERE req_number = '$search_name' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            }
        } elseif (isset($_POST['sun'])) {
            
        } else{
				$sql = "SELECT * FROM requisition WHERE req_date_added >= '$start_date' AND req_date_added <= '$end_date' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
			}

        $s = array();

		if(isset($_POST['sun'])){
			$s = $_POST['req'];
			$sql = $_POST['sql2'];
			$z = $_POST['z'];

			echo '<pre>';
			print_r($s);
			echo '</pre>';
		}

		$select = $db->select($sql);
		echo '<button type="button" id="checker" onclick="return multipleChecker();">Check All</button>';
		echo '<table border="1" id="table">';
		echo '<tr>';
		echo '<th></th>';
		echo '<th>No.</th>';
		echo '<th>Date<br/>Requested</th>';
		echo '<th>Item Code</th>';
		echo '<th>Quantity</th>';
		echo '<th>UOM</th>';
		echo '<th>Price</th>';
		echo '<th>Division <br/>/Location</th>';
		echo '<th>Requisition <br/>Number</th>';
		echo '<th>Requested By</th>';
		echo '<th>Approved By</th>';
		echo '</tr>';
		$no=1;
		$non = 1;
        if($db->num_rows()){
    		foreach($select as $row){
    			extract($row);
    			$v = new Db();
    			$vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
    			foreach($vv as $r){
    				extract($r);
    				$color = ($non%2==0)?"blue":"black";
    				echo '<tr style="color:'.$color.'">';
    				if(in_array($ri_id, $s)){
    					echo '<td><input checked="checked" type="checkbox" class="AllCheckBoxes"  id="jj'.$no.'" name="req[]" value="'.$ri_id.'"/><label for="jj'.$no.'"></label></td>';
    				}else{
    					echo '<td><input type="checkbox" class="AllCheckBoxes"  id="jj'.$no.'" name="req[]" value="'.$ri_id.'"/><label for="jj'.$no.'"></label></td>';
    				}
    				echo '<td>'.($no++).'.</td>';
    				echo '<td>'.FeedBack::date_fm($req_date_added).'</td>';
    				echo '<td>'.$ri_code.'</td>';
    				echo '<td>'.$ri_quantity.'</td>';
    				echo '<td>'.$ri_uom.'</td>';
    				echo '<td>'.$ri_price.'</td>';
    				echo '<td>'.$this->rgf("approval_matrix", $req_division, "ap_id", "ap_code").'</td>';
    				echo '<td>'.$req_number.'</td>';
    				echo '<td>'.$this->full_name($req_added_by).'</td>';
    				echo '<td>'.$this->full_name($req_app1_user_id).'</td>';
    				echo '</tr>';
    			}

    			$non++;
    		}
        }
		//echo '</table>';



		if(!$db->num_rows()){
			echo '<tr><td colspan="11" style="font-weight:bold;"><center>No Data</center></td></tr>';
		}
		echo '</table>';


		if($db->num_rows()){
			echo '<br/><button type="submit" name="sun" class="btn btn-primary"><i class="fa fa-fw fa-plane"></i> Push to SUN</button>';
			echo '<input type="hidden" value="'.$sql.'" name="sql2"/>';

			if (isset($_POST['monthDetails'])) {
                echo '<input type="hidden" value="monthDetails" name="z"/>';
            } elseif (isset($_POST['searchRequisition'])) {
                echo '<input type="hidden" value="searchRequisition" name="z"/>';
            } elseif (isset($_POST['searchPeroid'])) {
                echo '<input type="hidden" value="searchPeroid" name="z"/>';
            } else {
                echo '<input type="hidden" value="searchPeroid" name="z"/>';
            }
			
		}

		echo '</form>';

	?>
	</div>
</div>
</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.hide2').hide();
			<?php 
			if (isset($_POST['monthDetails']) || isset($monthDetails)) {
                echo "$('#monthDetails').show();";
            } elseif (isset($_POST['searchRequisition']) || isset($searchRequisition)) {
                echo "$('#numberDetails').show();";
            } elseif (isset($_POST['searchPeroid']) || isset($searchPeroid)) {
                echo "$('#rangeDetails').show();";
            } else {
                echo "$('#rangeDetails').show();";
            }
			?>

			$('.switch').click(function(){
				$('.hide2').hide();
				$('.hide2').css("font-weight", "100");
				var id = $(this).attr('data-id');
				$(this).css("font-weight", "500");				
				$('#'+id).show();
			});
			
		});
	</script>

	<?php	
	}

	public function pushedAction(){
	$db = new Db();

	$errors = array();

	if(isset($_POST['monthDetails'])){
    		$month = $_POST['month'];
    		$year = $_POST['year'];

    		if(empty($year)||empty($month)){
    			FeedBack::error("Please select Year and Month");
    		}else{

    			if($year == "all"){
    				$start_date = strtotime('2020-10-20 12:00:00 am');
    			}elseif($year !="all" && $month=="all"){    				
    				$start_date = strtotime("$year-01-01 12:00:00 am");
    			}else{    				
    				$start_date = strtotime("$year-$month-01 12:00:00 am");
    			}

    			if($month == "all" && $year == "all"){
    				$end_date = time();
    			}elseif($month=="all"){
    				$end_date = strtotime("$year-12-01 12:00:00 am");
    				$end_date = strtotime(date('Y-m-t', $end_date).' 11:59:59 pm');
    			}else{
    				$end_date = strtotime("$year-$month-01 12:00:00 am");
    				$end_date = strtotime(date('Y-m-t', $end_date).' 11:59:59 pm');
    			}
    		}

    		// echo 'Start:'.FeedBack::date_fm($start_date);
    		// echo ' <br/>';
    		// echo 'End :'.FeedBack::date_fm($end_date);

    	}elseif(isset($_POST['searchPeroid'])){
    		$from = $_POST['from'];
    		$to = $_POST['to'];

    		$errors = array();

    		if(empty($from) || empty($to) ){
    			FeedBack::error("Please Enter Both Dates (From and To)");
    		}elseif($from && $to){
    			$start_date = strtotime($from.' 12:00:00 am');
    			$end_date = strtotime($to.' 11:59:59 pm');
    		}

    		if($start_date>$end_date){
    			$errors[] = "From Date <b>(".date('Y-m-d', $start_date).")</b> cannot be greater than To Date <b>(".date('Y-m-d', $end_date).")</b>";
    		}

    		if ($errors !== []) {
                FeedBack::errors($errors);
            }

    	}elseif(isset($_POST['searchRequisition'])){
    		$search_type = $_POST['search_type'];
    		$search_name = trim($_POST['search_name']);
    		
    		$errors = array();
    		if ($search_name === '' || $search_name === '0') {
                $errors[] = "Please Enter Invoice Number";
            }

    		if ($errors !== []) {
                FeedBack::errors($errors);
            }
    	}

    	if(($start_date === 0 || $start_date === false) && ($end_date === 0 || $end_date === false)){
    		$start_date = strtotime(date('Y-m-d').' 12:00:00 am');
    		$end_date = time();
    	}

    	if(empty($year) && empty($month)){
    		$year = date('Y');
    		$month = date('m');
    	}

    	$from = date('Y-m-d', $start_date);
    	$to = date('Y-m-d', $end_date);

    	//echo $from;

	echo '<form action="" method="post">';
	?>
	<div class="row">
			<div class="col-md-12">
	<button type="button" class="switch" data-id="rangeDetails">By Date Range</button>
	<button type="button" class="switch" data-id="numberDetails">By Requisition Number</button>
	<button type="button" class="switch" data-id="monthDetails">By Month</button>

	<div id="rangeDetails" class="hide2">
	<?php
	echo '<div style="margin-top:20px;">';
	echo '<div style="float:left; margin-right:20px;">';
    	echo '<label>From&nbsp;&nbsp;</label>';
    	echo '<input id="date1" type="date" name="from" value="'.$from.'"><br/>';
    	echo '</div>';
    	echo '<div style="float:left; margin-right:20px;">';
    	echo '<label>To&nbsp;&nbsp;</label>';
    	echo '<input id="date2" type="date" name="to" value="'.$to.'">';
    	echo '</div>';
    	   	
    	echo '<div style="float:left; margin-right:20px;">';
    	echo '<button type="submit" name="searchPeroid" class="btn btn-primary btn-xs pt-0 pb-0"><i class="fa fa-fw fa-search"></i> Show</button>';
    	echo '</div>';
    	echo '</div>';

	?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('input[type ="date"]').bootstrapMaterialDatePicker({ 
                weekStart : 0, 
                time: false, 
                //minDate : new Date() 
            });
		});
	</script>
	</div>
	<div id="numberDetails" class="hide2">
	<?php
	echo '<div style=" margin-top:20px;">';
    	echo '<label>Search All Requisition which &nbsp;</label>';
    	$st = array('Contain', 'Start With', 'Ends With', 'Are Equal to');
    	echo '<select name="search_type">';
    	$v =0;
    	foreach($st as $t){
    		if ($search_type == $v) {
                echo '<option selected="selected" value="'.($v++).'">'.$t.'</option>';
            } else {
                echo '<option value="'.($v++).'">'.$t.'</option>';
            }
    	}
    	echo '</select> &nbsp; ';
    	echo '<input type="text" value="'.$search_name.'" name="search_name" placeholder="Enter Invoice number" />';
    	echo '<button name="searchRequisition"><i class="fa fa-fw fa-search"></i> Search</button>';
    	echo '</div>';

	?>
	</div>
	<div id="monthDetails" class="hide2">
	<?php
	echo '<div style="float:left; margin-top:20px;">';
    	echo '<label>Year&nbsp;&nbsp;</label>';
    	echo '<select name="year">';
    	echo '<option value="">Select</option>';
    	// if($year == "all")
    	// 	echo '<option selected value="all">All</option>';
    	// else
    	// 	echo '<option value="all">All</option>';
    	for($i=2020; $i<=date('Y'); $i++){
    		if ($year == $i) {
                echo '<option selected value="'.$i.'">'.$i.'</option>';
            } else {
                echo '<option value="'.$i.'">'.$i.'</option>';
            }
    	}
    	echo '</select>';
    	echo '</div>';
	echo '<div style="float:left; margin-top:20px;">';
    	echo '<label>&nbsp; Month&nbsp;&nbsp;</label>';
    	echo '<select name="month">';
    	echo '<option value="">Select</option>';
    	if ($month == "all") {
            echo '<option selected value="all">All</option>';
        } else {
            echo '<option value="all">All</option>';
        }
    	for($i=1; $i<=12; $i++){
    		if ($month == $i) {
                echo '<option selected value="'.$i.'">'.$this->month_name($i).'</option>';
            } else {
                echo '<option value="'.$i.'">'.$this->month_name($i).'</option>';
            }
    	}
    	echo '</select>';
    	echo '</div>';
    	echo '<div style="float:left; margin-left:20px; margin-top:20px;">';
    	echo '<button type="submit" name="monthDetails" class="btn btn-primary btn-xs pt-0 pb-0"><i class="fa fa-fw fa-search"></i> Show</button>';
    	echo '</div>';
    	echo '</div>';


    	$db = new Db();

    	if(isset($_POST['searchRequisition'])){
    		if ($search_type==0) {
                $sql = "SELECT * FROM requisition WHERE req_number LIKE '%$search_name%' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==1) {
                $sql = "SELECT * FROM requisition WHERE req_number LIKE '$search_name%' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==2) {
                $sql = "SELECT * FROM requisition WHERE req_number LIKE '%$search_name' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==3) {
                $sql = "SELECT * FROM requisition WHERE req_number = '$search_name' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            }
		}else{
			$sql = "SELECT * FROM requisition WHERE req_date_added >= '$start_date' AND req_date_added <= '$end_date' AND req_app1_user_id IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";		
		}

		$select = $db->select($sql);
		echo '<table border="1" id="table">';
		echo '<tr>';
		echo '<th>No.</th>';
		echo '<th>Date<br/>Requested</th>';
		echo '<th>Item Code</th>';
		echo '<th>Quantity</th>';
		echo '<th>UOM</th>';
		echo '<th>Price</th>';
		echo '<th>Division <br/>/Location</th>';
		echo '<th>Requisition <br/>Number</th>';
		echo '<th>Requested By</th>';
		echo '<th>Approved By</th>';
		echo '</tr>';
		$no=1;
		foreach($select as $row){
			extract($row);
			$v = new Db();
			$vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
			foreach($vv as $r){
				extract($r);
				echo '<tr>';
				echo '<td>'.($no++).'.</td>';
				echo '<td>'.FeedBack::date_fm($req_date_added).'</td>';
				echo '<td>'.$ri_code.'</td>';
				echo '<td>'.$ri_quantity.'</td>';
				echo '<td>'.$ri_uom.'</td>';
				echo '<td>'.$ri_price.'</td>';
				echo '<td>'.$this->rgf("approval_matrix", $req_division, "ap_id", "ap_code").'</td>';
				echo '<td>'.$req_number.'</td>';
				echo '<td>'.$this->full_name($req_added_by).'</td>';
				echo '<td>'.$this->full_name($req_app1_user_id).'</td>';
				echo '</tr>';
			}
		}

		if(!$db->num_rows()){
			echo '<tr><td colspan="10" style="font-weight:bold;"><center>No Data</center></td></tr>';
		}
		echo '</table>';



    	echo '</form>';
	?>
	</div>
</div>
</div>
	<script type="text/javascript">
            
		$(document).ready(function(){

			$('.hide2').hide();
			<?php 
			if (isset($_POST['monthDetails'])) {
                echo "$('#monthDetails').show();";
            } elseif (isset($_POST['searchRequisition'])) {
                echo "$('#numberDetails').show();";
            } elseif (isset($_POST['searchPeroid'])) {
                echo "$('#rangeDetails').show();";
            } else {
                echo "$('#rangeDetails').show();";
            }
			?>
			$('.switch').click(function(){
				$('.hide2').hide();
				$('.hide2').css("font-weight", "100");
				var id = $(this).attr('data-id');
				$(this).css("font-weight", "500");				
				$('#'+id).show();
			});
			
		});
	</script>

	<?php	
	}
	
	
}