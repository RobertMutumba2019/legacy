<?php
Class ApprovalMatrix extends BeforeAndAfter{
	public $id = 0;
	public $page = "APPROVAL MATRIX";

	public function __construct(){
		$access = new AccessRights();
		
		if(portion(2) == "all-delegation"){
			if(!$access->sectionAccess(user_id(), $this->page, 'V')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url()."dashboard/index");
			}
		}else if(portion(2)=="add-delegation"){
			if(!$access->sectionAccess(user_id(), $this->page, 'A')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url()."dashboard/index");
			}
		}
	}
	
	public function id($id){
	 	$this->id = $id;
	}

	
	public static function getLinks(){
		$page = "APPROVAL MATRIX";
		$links = array(
			array(
				"link_name"=>"Add Approval Matrix", 
				"link_address"=>"approvalMatrix/add-approval-Matrix",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"All Approval", 
				"link_address"=>"approvalMatrix/all-approval-Matrix",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Import Approval Matrix", 
				"link_address"=>"approvalMatrix/import-approval-Matrix",
				"link_icon"=>"fa-upload",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
		
		return $links;
	}
	public function deleteApprovalMatrixAction(){
		$id = portion(3);
		$this->deletor("approval_matrix", "ap_id",  $id, 'approvalMatrix/all-approval-Matrix');
	}
	
	public function importApprovalMatrixAction(){
		$db = new Db();
		$time = time();
		$user = user_id();
		$errors = array();
		if (isset($_POST['uploaddata'])) { 
			$filename=$_FILES["uploadFile"]["tmp_name"];
			if($_FILES["uploadFile"]["size"] > 0){

				$valid_name = "CODE";
				$file = fopen($filename, "r");
				while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
					$count++;
					$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));
					$col_2 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[1]))));

					if($count == 1){
						//checking template
						if($col_1 != $valid_name){
							$errors[] = "Invalid Template";
						}
					}else{
						//checking if there is not empty field
						if(empty($col_1)){
							$errors[] = "Cell <b>A".$count."</b> should not be empty";
						}
						if(empty($col_2)){
							$errors[] = "Cell <b>B".$count."</b> should not be empty";
						}

						//checking duplicates
						$application_id = $this->rgf("approval_matrix", $col_1, "ap_code", "ap_id");
						if($application_id){
							$errors[] = "Cell <b>A".$count."</b> already exists <b>".$col_1."-".$col_2."</b>";
						}
					}
					
					echo $db->error();
				}
				fclose($file);
				$count = 0;
				if(empty($errors)){
					$db = new Db();

					$file = fopen($filename, "r");
					while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
						$count++;
						if($count >= 2){
							$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));
							$col_2 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[1]))));
							$db->insert("approval_matrix",["ap_date_added"=>$time, "ap_code"=>$col_1,"ap_unit_code"=>$col_2,"ap_added_by"=>$user]);
						}
					}
					
					if(!$db->error()){
						FeedBack::success();
						FeedBack::refresh(3, return_url().'approvalMatrix/all-approval-Matrix');
					}else{
						FeedBack::error($errors);
					}
				}else{
					FeedBack::errors($errors);
				}
			}else{
				$errors[] = "Please Attach file";
			}

		
			}

		?>

      <div class="container">
			<div class="col-md-4">
				<form method="post" enctype="multipart/form-data" action="" >
					<input type="file" name="uploadFile" class="form-control">
					<br>
					<button type="submit" class="btn btn-primary" name="uploaddata"><i style="font-size:12px;" class="fa fa-upload"></i> Upload</button>
				</form>
				<br><br>
				<a href="../import file/ApprovalMatrix.csv">Download Template</a>
			</div>
		</div>
	<?php	
	} 
	public function AddApprovalMatrixAction(){
		if(isset($_POST['submit'])){
			$code = $_POST['code'];
			$unit_name = $_POST['unit_name'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();

			if(empty($code)){
				$errors[] = "Enter Code";
			}
			if(empty($unit_name)){
				$errors[] = "Enter Unit Code";
			}
		
			
			if($this->isThere("approval_matrix", ["ap_code"=>$code])){
				$errors[]="Code($code)already exists";
			}
			
			if(empty($errors)){
				$db->insert("approval_matrix",["ap_date_added"=>time(),"ap_code"=>$code,"ap_unit_code"=>$unit_name,"ap_added_by"=>user_id()]);
				
				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'approvalMatrix/all-approval-Matrix');
				}else{
					FeedBack::error($db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
		}
		?>
		<div class="col-md-5">
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Add Approval Matrix
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
							<div id="must">All fields with asterisk(*) are mandatory.</div>
							<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label>Code<span class="must">*</span></label>
									<div class="form-line">
									<input type="text" name="code" id="code" value="<?php echo @$code; ?>" class="form-control" placeholder="Enter Code">
									</div>
								</div>
								<div class="form-group">
									<label>Unit Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" id="unit_name" type="text" name="unit_name" placeholder="Enter Unit Name" value="<?php echo @$unit_name; ?>">
									</div>
								</div>
								<br>
							<button type="button" id="addApprovalMatrixBtn" name="" style="width: 100px;" class="form-control btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><span id="addApprovalMatrixStatus"></span>
							
							</div>
				
								
								
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public function AllApprovalMatrixAction(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM approval_matrix");
			//echo$db->error();
			if($db->error()){
				echo $db->error();
			}else{
				//print_r($select);
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>Code</th>';
				echo '<th>Unit Code</th>';
				echo '<th>Date Added</th>';
				echo '<th>Added By</th>';
				echo '<th width="100px">Action</th>';
				echo '</tr>';
				echo '</thead>';
				
				$i=1;
				echo '<tbody>';
				foreach($select as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.($ap_code).'</td>';
					echo '<td>'.($ap_unit_code).'</td>';
					
					echo '<td>'.FeedBack::date_fm($ap_date_added).'</td>';
					echo '<td>'.$this->full_name($ap_added_by).'</td>';
					echo '<td>';
							echo '<a class="eagle-load btn btn-success btn-xs" href="'.return_url().'approvalMatrix/edit-approval-matrix/'.$ap_id.'">Edit</a>'."  ";
							echo '<a class="btn btn-xs btn-danger" href="'.return_url().'approvalMatrix/delete-approval-matrix/'.$ap_id.'">Delete</a>';
					echo '</td>';

				}
				echo '</tbody>';
				
				echo '</table>';

				
			}
			
			?>
		</div>
		<?php
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
	}
	
	public function EditApprovalMatrixAction(){
		$id = portion(3);
		$id = $this->id;
		$db = new Db();
		$select = $db->select("SELECT * FROM approval_matrix WHERE ap_id ='$id'");
		
		extract($select[0]);
			if(isset($_POST['submit'])){
			$code = $_POST['code'];
			$unit_name = $_POST['unit_name'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();

			if(empty($code)){
				$errors[] = "Enter Code";
			}
			if(empty($unit_name)){
				$errors[] = "Enter Unit Code";
			}
		
			
			// if($this->isThere("department", ["dept_name"=>$department])){
			// 	$errors[]="department($department) already exists";
			// }
			
			if(empty($errors)){
				$db->update("approval_matrix",["ap_date_added"=>time(),"ap_code"=>$code,"ap_unit_code"=>$unit_name,"ap_added_by"=>user_id()],["ap_id"=>$id]);
				
				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'approvalMatrix/all-approval-Matrix');
				}else{
					FeedBack::error($db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
		}
		?>
		<div class="col-md-5">
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Edit Approval Matrix
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
							<div id="must">All fields with asterisk(*) are mandatory.</div>
							<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label>Code<span class="must">*</span></label>
									<div class="form-line">
									<input type="hidden" id="id" value="<?php echo($id);?>" name="">	
									<input type="text" name="code" id="code" value="<?php echo @$ap_code; ?>" class="form-control" placeholder="Enter Code">
									</div>
								</div>
								<div class="form-group">
									<label>Unit Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" id="unit_name" type="text" name="unit_name" placeholder="Enter Item Name" value="<?php echo @$ap_unit_code; ?>">
									</div>
								</div>
								<br>
							<button type="button" id="editApprovalMatrixBtn" name="" style="width: 100px;" class="form-control btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><span id="editApprovalMatrixStatus"></span>	
							
							</div>
				
								
								
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	}
	
	