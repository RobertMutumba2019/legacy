<?php

include_once __DIR__ . "/Exporter.inc";
Class Designation extends BeforeAndAfter{
	public $page = "DESIGNATION";
	
	public function __construct(){
		new AccessRights();
		//$access->pageAccess(user_id(), $this->page, 'V');
	}
	
	public static function getLinks(){
		$page = "DESIGNATION";
		
		return array(
			array(
				"link_name"=>"Add Designation", 
				"link_address"=>"designation/add-designation",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"View Designation", 
				"link_address"=>"designation/all-designation",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Import Designation", 
				"link_address"=>"designation/import-designation",
				"link_icon"=>"fa-upload",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
	}
	
	public function deleteDesignationAction(){
		$id = portion(3);
		$this->deletor("designation", "designation_id",  $id, 'designation/all-designation');
	}

	public function importDesignationAction(){
		$db = new Db();
		$time = time();
		$user = user_id();
		$errors = array();
		if (isset($_POST['uploaddata'])) { 
			$filename=$_FILES["uploadFile"]["tmp_name"];
			if($_FILES["uploadFile"]["size"] > 0){

				$valid_name = "DESIGNATION NAME";
				$file = fopen($filename, "r");
				while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
					$count++;
					$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));

					if($count == 1){
						//checking template
						if($col_1 !== $valid_name){
							$errors[] = "Invalid Template";
						}
					}else{
						//checking if there is not empty field
						if($col_1 === '' || $col_1 === '0'){
							$errors[] = "Cell <b>A".$count."</b> should not be empty";
						}

						//checking duplicates
						$designation_id = $this->rgf("designation", $col_1, "designation_name", "designation_id");
						if($designation_id){
							$errors[] = "Cell <b>A".$count."</b> already exists";
						}
					}
					
					echo $db->error();
				}
				fclose($file);
				$count = 0;
				if($errors === []){
					$db = new Db();

					$file = fopen($filename, "r");
					while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
						$count++;

						if($count > 1){
							$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));
							$db->insert("designation",["designation_date_added"=>$time, "designation_name"=>$col_1,"designation_added_by"=>$user]);
						}
					}
					
					if(!$db->error()){
						FeedBack::success();
						FeedBack::refresh(3, return_url().'designation/all-designation');
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
				<a href="../import file/Designation name.csv">Download Template</a>
			</div>
		</div>
	<?php	
	}
	
	public function addDesignationAction(){
		if(isset($_POST['submit'])){
		
			$designation = $_POST['designation'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($designation)){
				$errors[]="Enter designation";
			}
			if($this->isThere("designation", ["designation_name"=>$designation])){
				$errors[]="designation($designation) already exists";
			}
			
			if($errors === []){
				$x = $db->insert("designation",["designation_date_added"=>$time, "designation_name"=>"$designation","designation_added_by"=>$user]);
							
				$db = new Db();
				$insert = $db->insert("user_role", ["ur_name"=>$designation, "ur_added_by"=>$user_id, "ur_date_added"=>$time]);
					
				
				if(!$db->error()){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'designation/all-designation');
				}else{
					FeedBack::error();
				}
			}else{
				FeedBack::errors($errors);
			}
		}
		?>
		<div class="col-md-4">
			<h3>&nbsp;</h3>
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Add Designation Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Designation Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="designation" placeholder="Enter Designation Name" value="<?php echo @$designation; ?>">
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save that Designation ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	public function editDesignationAction(){
		$id=portion(3);
		$db=new Db();
		$select=$db->select("select * from designation WHERE designation_id='$id'");
		
		if (is_array($select) && isset($select[0]) && is_array($select[0])) {
											
		extract($select[0][0]);}
		if(isset($_POST['submit'])){
		
			$designation = $_POST['designation'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($designation)){
				$errors[]="Enter designation";
			}
			if($this->isThereEdit("designation", ["designation_name"=>$designation,"designation_id"=>$id])){
				$errors[]="designation($designation) already exists";
			}
			
			if($errors === []){
				$db = new Db();

				$uid = $this->rgf("user_role", $designation_name , "ur_name", "ur_id");
				
				$insert = $db->update("user_role", ["ur_name"=>$designation, "ur_added_by"=>$user_id, "ur_date_added"=>$time], ["ur_id"=>$uid]);

				$x = $db->update("designation",["designation_date_added"=>$time, "designation_name"=>"$designation","designation_added_by"=>$user],["designation_id"=>$id]);
							
				
					
				
				if(!$db->error()){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'designation/all-designation');
				}else{
					FeedBack::error();
				}
			}else{
				FeedBack::errors($errors);
			}
		}
		?>
		<div class="col-md-4">
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Edit Designation Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Designation Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="designation" placeholder="Enter Designation Name" value="<?php echo @$designation_name; ?>">
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save changes?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public function AllDesignationAction(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM designation ORDER BY designation_name ASC");
			
			if(!$select){
				$db->error();
			}else{
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>Designation Name</th>';
				echo '<th>Total</th>';
				if($access->sectionAccess(user_id(), $this->page, 'E') || $access->sectionAccess(user_id(), $this->page, 'D')){
					echo '<th width="100px">Action</th>';
				}
				echo '</tr>';
				echo '</thead>';
				
					////////////////////////////REPORT STEP 1//////////////////
				$db_values = array();
				$db_values[] = array(
				
					"No",
				    "Designation Name",
				    "Number"
								
				);
				//////////////////////////////////////////////////////////////
				$i=1;
				echo '<tbody>';

				if(is_array($select)){

				
				foreach($select as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.($designation_name).'</td>';
					echo '<td>'.$this->total("sysuser", "user_designation", $designation_id).'</td>';
					
					if($access->sectionAccess(user_id(), $this->page, 'E') || $access->sectionAccess(user_id(), $this->page, 'D')){
					
						echo '<td>';
						if ($access->sectionAccess(user_id(), $this->page, 'E')) {
                            echo $this->action('edit','designation/edit-designation/'.$designation_id, 'Edit');
                        }
						if ($access->sectionAccess(user_id(), $this->page, 'D')) {
                            echo $this->action('delete','designation/delete-designation/'.$designation_id, 'Delete');
                        }
						echo '</td>';
					}
					echo '</tr>';
					
					//////////////////////////////////REPORT STEP 2//////////////////////////////////	
					$db_values[] = array(
					
						($i-1),
						($designation_name),
						($this->total("sysuser", "user_designation", $designation_id))
						
					); 
					
					/////////////////////////////////////////////////////////////////////////////
				}	
				}
				echo '</tbody>';
				
				echo '</table>';
				
				//////////////////////////////////////////REPORT STEP 3/////////////////////////////
				$t = new TableCreator();
				$heading = "designationS LIST"; //CHANGE
				$t->open($this->full_name(user_id()), $heading);
				$t->thd($db_values);
				$t->close();
				$t->results();

				$e = new Exporter();
				echo $e->getDisplay($heading, $t->results());
				/////////////////////////////////////////////////////////////////////////////		
			
				
			}
			
			?>
		</div>
		<?php
	}
}