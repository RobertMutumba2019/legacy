<?php

include_once __DIR__ . "/Exporter.inc";
Class UserRole extends BeforeAndAfter{
	public $page = "USER ROLE";
	
	public function __construct(){
		new AccessRights();
		//$access->pageAccess(user_id(), $this->page, 'V');
	}
	
	public static function getLinks(){
		$page = "USER ROLE";
		
		return array(
			array(
				"link_name"=>"Add User Role", 
				"link_address"=>"user-role/add-user-role",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"View User Role", 
				"link_address"=>"user-role/all-user-role",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Import User Role", 
				"link_address"=>"user-role/import-user-role",
				"link_icon"=>"fa-upload",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
	}
	
	public function deleteUserRoleAction(){
		$id = portion(3);
		$this->deletor("user_role", "ur_id",  $id, 'user-role/all-user-role');
	}

	public function importUserRoleAction(){
		$db = new Db();
		$time = time();
		$user = user_id();
		$errors = array();
		if (isset($_POST['uploaddata'])) { 
			$filename=$_FILES["uploadFile"]["tmp_name"];
			if($_FILES["uploadFile"]["size"] > 0){

				$valid_name = "USER ROLE NAME";
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
						$designation_id = $this->rgf("user_role", $col_1, "ur_name", "ur_id");
						if($designation_id){
							$errors[] = "Cell <b>A".$count."</b> already exists";
						}
					}
					
					echo $db->error();
				}
				fclose($file);

				if($errors === []){
					$db = new Db();

					$file = fopen($filename, "r");
					while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
						$count++;
						if($count > 1){
							$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));
							$db->insert("user_role",["ur_date_added"=>$time, "ur_name"=>$col_1,"ur_added_by"=>$user]);
						}
					}
					
					if(!$db->error()){
						FeedBack::success();
						FeedBack::refresh(3, return_url().'user-role/all-user-role');
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
				<a href="../import file/user role name.csv">Download Template</a>
			</div>
		</div>
	<?php	
	}
	
	public function addUserRoleAction(){
		if(isset($_POST['submit'])){
		
			$designation = $_POST['designation'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($designation)){
				$errors[]="Enter User Role";
			}
			if($this->isThere("user_role", ["ur_name"=>$designation])){
				$errors[]="User role($user_role) already exists";
			}
			
			if($errors === []){
							
				$db = new Db();
				$insert = $db->insert("user_role", ["ur_name"=>$designation, "ur_added_by"=>$user_id, "ur_date_added"=>$time]);
					
				
				if(!$db->error()){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'user-role/all-user-role');
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
					Add User Role Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>User Role Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" id="designation" type="text" name="designation" placeholder="Enter User Role Name" value="<?php echo @$designation; ?>">
									</div>
								</div>
								
								<button  onclick = "return confirm('Are you sure, you want to save User role ?');" type="button" id="submitBtn2" name="" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><span id="roleStatus"></span>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	public function editUserRoleAction(){
		$id=portion(3);
		$db=new Db();
		$select=$db->select("select * from user_role WHERE ur_id ='$id'");
		extract($select[0]);
		if(isset($_POST['submit'])){
		
			$designation = $_POST['designation'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($designation)){
				$errors[]="Enter User Role";
			}
			if($this->isThereEdit("user_role", ["ur_name"=>$designation,"ur_id"=>$id])){
				$errors[]="User Role ($user_role) already exists";
			}
			
			if($errors === []){
				$db = new Db();

				$insert = $db->update("user_role", ["ur_name"=>$designation, "ur_added_by"=>$user_id, "ur_date_added"=>$time], ["ur_id"=>$id]);
				
				if(!$db->error()){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'user-role/all-user-role');
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
					Edit User Role Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<!-- <form role="form" action="" method="post"> -->
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>User Role Name<span class="must">*</span></label>
									<input type="hidden" value="<?php echo $id; ?>" id="ur_id"/>
									<div class="form-line">
										<input class="form-control" id="designation" type="text" name="designation" placeholder="Enter User Role Name" value="<?php echo @$ur_name; ?>">
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save changes?');" type="button" id="editBtn" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><span id="editrolestatus"></span>
							<!-- </form> -->
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public function AllUserRoleAction(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<h3>User Role List</h3>
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM user_role ORDER BY ur_name ASC");
			
			if(!$select){
				$db->error();
			}else{
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>User Role Name</th>';
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
				    "User Role Name",
				    "Number"
								
				);
				//////////////////////////////////////////////////////////////
				$i=1;
				echo '<tbody>';
				foreach($select as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.($ur_name).'</td>';
					echo '<td>'.number_format($this->total("sysuser", "user_role", $ur_id)).'</td>';
					
					if($access->sectionAccess(user_id(), $this->page, 'E') || $access->sectionAccess(user_id(), $this->page, 'D')){
					
						echo '<td>';
						if ($access->sectionAccess(user_id(), $this->page, 'E')) {
                            echo $this->action('edit','user-role/edit-user-role/'.$ur_id, 'Edit');
                        }
						if ($access->sectionAccess(user_id(), $this->page, 'D')) {
                            echo $this->action('delete','user-role/delete-designation/'.$ur_id, 'Delete');
                        }
						echo '</td>';
					}
					echo '</tr>';
					
					//////////////////////////////////REPORT STEP 2//////////////////////////////////	
					$db_values[] = array(
					
						($i-1),
						($designation_name),
						($this->total("sysuser", "user_role", $designation_id))
						
					); 
					
					/////////////////////////////////////////////////////////////////////////////
					
				}
				echo '</tbody>';
				
				echo '</table>';
				
				//////////////////////////////////////////REPORT STEP 3/////////////////////////////
				$t = new TableCreator();
				$heading = "USER-ROLES LIST"; //CHANGE
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