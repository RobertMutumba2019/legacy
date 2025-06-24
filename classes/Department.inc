<?php
Class Department extends BeforeAndAfter{

	public $page = "DEPARTMENTS";

	public function __construct(){
		$access = new AccessRights();
		
		if(portion(2) == "all-departments"){
			if(!$access->sectionAccess(user_id(), $this->page, 'V')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url()."dashboard/index");
			}
		}else if(portion(2)=="add-department"){
			if(!$access->sectionAccess(user_id(), $this->page, 'A')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url()."dashboard/index");
			}
		}
	}
	
	public function deleteDepartmentAction(){
		$id = portion(3);
		$this->deletor("department", "dept_id",  $id, 'department/all-departments');
	}

	public function deleteSectionAction(){
		$id = portion(3);
		$this->deletor("section", "section_id",  $id, 'department/all-sections');
	}
	
	public static function getLinks(){
		$page = "DEPARTMENTS";
		$links = array(
			array(
				"link_name"=>"Add Department", 
				"link_address"=>"department/add-department",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"View Departments", 
				"link_address"=>"department/all-departments",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Import Departments", 
				"link_address"=>"department/import-departments",
				"link_icon"=>"fa-upload",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
		
		return $links;
	}
	
	public function importDepartmentsAction(){

		$db = new Db();
		$time = time();
		$user = user_id();
		$errors = array();
		if (isset($_POST['uploaddata'])) { 
			$filename=$_FILES["uploadFile"]["tmp_name"];
			if($_FILES["uploadFile"]["size"] > 0){

				$valid_name = "DEPARTMENT NAME";
				$file = fopen($filename, "r");
				while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
					$count++;
					$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));
					//echo $col_1;
					if($count == 1){
						//echo "$col_1 is equal to $valid_name";
						if($col_1 != $valid_name){
							$errors[] = "Invalid Template";
						}
					}else{
						//checking if there is not empty field
						if(empty($col_1)){
							$errors[] = "Cell <b>A".$count."</b> should not be empty";
						}

						//checking duplicates
						$department_id = $this->rgf("department", $col_1, "dept_name", "department_id");
						if($department_id){
							$errors[] = "Cell <b>A".$count."</b> already exists";
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
						if($count > 1){
							$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));
							$db->insert("department",["dept_date_added"=>$time, "dept_name"=>$col_1,"dept_added_by"=>$user, "dept_office_id"=>0]);
						}
					}
					
					if(!$db->error()){
						FeedBack::success();
						FeedBack::refresh(3, return_url().'department/all-departments');
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
				<a href="../import file/department.csv">Download Template</a>
			</div>
		</div>

	<?php	
	} 
	public function AddDepartmentAction(){
		if(isset($_POST['submit'])){
		
			$department = $_POST['department'];
			//$office = $_POST['office'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();

			if(empty($department)){
				$errors[] = "Enter department name";
			}
			
			if($this->isThere("department", ["dept_name"=>$department])){
				$errors[]="department($department) already exists";
			}
				
			if(empty($errors)){
				$x = $db->insert("department",["dept_date_added"=>$time, "dept_name"=>$department,"dept_added_by"=>$user, "dept_office_id"=>0, "dept_status"=>1]);
				
				if($x){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'department/all-departments');
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
					Add Department Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All fields with asterisk(*) are mandatory.</div>
									<label>Department Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="department" placeholder="Enter Department Name" value="<?php echo @$department; ?>">
									</div>
								</div>
								<?php 
								$access = new AccessRights();
								if($access->sectionAccess(user_id(), $this->page, 'A')){ ?>
								<button onclick = "return confirm('Are you sure you want to save those department details ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
								<?php } ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public function AllDepartmentsAction(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM department order by dept_name asc");
			
			if(!$select){
				$db->error();
			}else{
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>Department Name</th>';
				//echo '<th>HOD</th>';
				echo '<th>Total Staff</th>';
				//echo '<th>Status</th>';

				$db_values = array();
				$db_values[] = array(
					"No.",
					"Department Name",
					"Office",
					"HOD",
					"Total Sections",
					"Total Staff",
					"S tatus",
				);

				if($access->sectionAccess(user_id(), $this->page, 'E') || $access->sectionAccess(user_id(), $this->page, 'D')){
					echo '<th width="100px">Action</th>';
				}
				echo '</tr>';
				echo '</thead>';
				
				$i=1;
				echo '<tbody>';
				foreach($select as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.($dept_name).'</td>';
					//echo '<td>'.$this->rgf("branch", $dept_office_id, "branch_id", "branch_name").'</td>';
					
					echo '<td align="">'.number_format($this->total("sysuser", "user_department_id", $dept_id)).'</td>';
					//echo '<td>';
						//if(empty($dept_status)){
							// $xyz = $this->show22;
							// echo $this->show22;
						//}else{
							// $xyz = $this->show11;
							// echo $this->show11;
						//}

					//echo '</td>';
					if($access->sectionAccess(user_id(), $this->page, 'E') || $access->sectionAccess(user_id(), $this->page, 'D')){
					
						echo '<td>';
						if($access->sectionAccess(user_id(), $this->page, 'E'))
							echo $this->action('edit','department/edit-department/'.$dept_id, 'Edit');
						if($access->sectionAccess(user_id(), $this->page, 'D'))
							echo $this->action('delete','department/delete-department/'.$dept_id, 'Delete');
						echo '</td>';
					}
					echo '</tr>';

					$db_values[] = array(
						$i, 
						$dept_name,
						//$this->rgf("branch", $dept_office_id, "branch_id", "branch_name"),
						$user_surname.' '.$user_othername,
						number_format($this->total("sysuser", "user_department_id", $dept_id)),
						$xyz
					); 

				}
				echo '</tbody>';
				
				echo '</table>';

				$t = new TableCreator();
				$heading = "DEPARTMENT LIST";
				$t->open($this->full_name(user_id()), $heading);
				$t->thd($db_values);
				$t->close();
				$t->results();

				$e = new Exporter();
				echo $e->getDisplay($heading, $t->results());
			}
			
			?>
		</div>
		<?php
	}
	
	public function EditDepartmentAction(){
		$id = portion(3);
		$db = new Db();
		$select = $db->select("SELECT * FROM department WHERE dept_id ='$id'");
		
		extract($select[0]);
		
		$department = $dept_name;
		if(isset($_POST['submit'])){
		
			$department = $_POST['department'];
			$status = $_POST['status'];
			$time = time();
			$user = user_id();
			$db = new Db();
			$time = time();
			
			$errors = array();

			if(empty($department)){
				$errors[] = "Enter department name";
			}
						
			if($this->isThereEdit("department", ["dept_name"=>$department, "dept_id"=>$id])){
				$errors[]="department($department) already exists";
			}
				
			if(empty($errors)){
				$x = $db->update("department", ["dept_name"=>$department,  "dept_status"=>$status], ["dept_id"=>$id]);
			
				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(2, return_url()."department/all-departments");
				}else{
					FeedBack::error("An error occured.".$db->error());
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
					Edit Department Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<label>Department Name</label>
									<div class="form-line">
										<input class="form-control" type="text" name="department" placeholder="Enter Department Name" value="<?php echo @$dept_name; ?>">
									</div>
								</div>
								<div class="form-group">
								
									<label>Status:<span class="must"></span></label>
									<div class="form-line">
										<?php
										echo '<select name="status">';

										if($dept_status == 0)
											echo '<option value="0" selected="selected">'.$this->show2.'</option>';
										else
											echo '<option value="0">'.$this->show2.'</option>';

										if($dept_status == 1)
											echo '<option value="1" selected="selected">'.$this->show1.'</option>';
										else
											echo '<option value="1">'.$this->show1.'</option>';
										echo '</select>';
										?>
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save changes ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	
	public function AddSectionAction(){
		if(isset($_POST['submit'])){
		
			$section = $_POST['section_name'];
			$department_id = $_POST['department_id'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($section)){
				$errors[] = "Enter section name";
			}
			if(empty($department_id)){
				$errors[] = "Select the department for that section";
			}
			if($this->isThere("section", ["section_name"=>$section])){
				$errors[]="section($section) already exists";
			}
			if(empty($errors)){
		
				$x = $db->insert("section",["section_date_added"=>$time,"section_dept_id"=>$department_id, "section_name"=>$section,"section_added_by"=>$user, "section_status"=>1]);
				echo $db->error();
				if($x){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'department/all-sections');
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
					Add Section Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Section Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="section_name" placeholder="Enter Section Name" value="<?php echo @$department; ?>">
									</div>
								</div>
								<div class="form-group">
								
									<label>Belongs To which Department:<span class="must">*</span></label>
									<div class="form-line">
										<select name="department_id" id="itemName" data-show-subtext="true" data-live-search="true" style="width:100%;">
											<option value="">--Select--</option>
											<?php
											$db = new Db();
											$select = $db->select("SELECT dept_id, dept_name FROM department WHERE dept_status = 1 ORDER BY dept_name ASC");
											foreach($select[0] as $row){
												extract($row);
												if($dept_id == $department_id)
													echo '<option data-subtext="'.$dept_name.'" value="'.$dept_id.'" selected="selected">'.$dept_name.'</option>';
												else
													echo '<option data-subtext="'.$dept_name.'" value="'.$dept_id.'">'.$dept_name.'</option>';
											}
											?>
										</select>
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save those section details ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	public function EditSectionAction(){
		$id=portion(3);
		$db=new Db();
		$select=$db->select("select * from section WHERE section_id='$id'");
		extract($select[0][0]);
		if(isset($_POST['submit'])){
		
			$section = $_POST['section_name'];
			$department_id = $_POST['department_id'];
			$status = $_POST['status'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($section)){
				$errors[] = "Enter section name";
			}
			if(empty($department_id)){
				$errors[] = "Select the department for that section";
			}
			if($this->isThereEdit("section", ["section_name"=>$section,"section_id"=>$id])){
				$errors[]="section($section) already exists";
			}
			if(empty($errors)){
		
				$x = $db->update("section",["section_date_added"=>$time,"section_dept_id"=>$department_id, "section_name"=>$section, "section_added_by"=>$user, "section_status"=>$status ],["section_id"=>$id]);
				echo $db->error();
				if($x){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'department/all-sections');
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
					Edit Section Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Section Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="section_name" placeholder="Enter Section Name" value="<?php echo @$section_name; ?>">
									</div>
								</div>
								<div class="form-group">
								
									<label>Belongs To which Department:<span class="must">*</span></label>
									<div class="form-line">
										<select name="department_id" id="itemName" data-show-subtext="true" data-live-search="true" style="width:100%;">
											<option data-subtext="" value="">--Select--</option>
											<?php
											$db = new Db();
											$select = $db->select("SELECT dept_id, dept_name FROM department WHERE dept_status = 1 ORDER BY dept_name ASC");
											foreach($select[0] as $row){
												extract($row);
												if($section_dept_id == $dept_id)
													echo '<option data-subtext="'.$dept_name.'" value="'.$dept_id.'" selected="selected">'.$dept_name.'</option>';
												else
													echo '<option data-subtext="'.$dept_name.'" value="'.$dept_id.'">'.$dept_name.'</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
								
									<label>Status:<span class="must"></span></label>
									<div class="form-line">
										<?php
										echo '<select name="status">';

										if($section_status == 0)
											echo '<option value="0" selected="selected">'.$this->show2.'</option>';
										else
											echo '<option value="0">'.$this->show2.'</option>';

										if($section_status == 1)
											echo '<option value="1" selected="selected">'.$this->show1.'</option>';
										else
											echo '<option value="1">'.$this->show1.'</option>';
										echo '</select>';
										?>
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save these section changes ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public function AllSectionsAction(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<h3>Section List</h3>
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM department, section WHERE dept_id = section_dept_id ");
			
			if(!$select){
				$db->error();
			}else{
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>Section Name</th>';
				echo '<th>Department Name</th>';
				echo '<th>Total Staff</th>';
				echo '<th>Status</th>';
				if($access->sectionAccess(user_id(), $this->page2, 'E') || $access->sectionAccess(user_id(), $this->page2, 'D')){
					echo '<th width="100px">Action</th>';
				}
				echo '</tr>';
				echo '</thead>';
				
				////////////////////////////REPORT STEP 1//////////////////
				$db_values = array();
				$db_values[] = array(
				
					"No",
					"Section Name",
					"Department Name",
					"Total Staff"
					
								);
				//////////////////////////////////////////////////////////////
				
				
				$i=1;
				echo '<tbody>';
				foreach($select[0] as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.($section_name).'</td>';
					echo '<td>'.($dept_name).'</td>';
					echo '<td>'.number_format($this->total("sysuser", "user_section_id", $section_id)).'</td>';

					echo '<td>';
					if(empty($section_status)){
						echo $this->show22;
					}else{
						echo $this->show11;
					}
					echo '</td>';
										
					if($access->sectionAccess(user_id(), $this->page2, 'E') || $access->sectionAccess(user_id(), $this->page2, 'D')){
					
						echo '<td>';
						if($access->sectionAccess(user_id(), $this->page2, 'E'))
							echo $this->action('edit','department/edit-section/'.$section_id, 'Edit');
						if($access->sectionAccess(user_id(), $this->page2, 'D'))
							echo $this->action('delete','department/delete-section/'.$section_id, 'Delete');
						echo '</td>';
					}
					echo '</tr>';
					
					//////////////////////////////////REPORT STEP 2//////////////////////////////////	
					$db_values[] = array(
					
						($i-1),
						($section_name),
						($dept_name),
						number_format($this->total("sysuser", "user_section_id", $section_id))

					
					); 
					
					/////////////////////////////////////////////////////////////////////////////
					
				}
				echo '</tbody>';
				
				echo '</table>';
				
				//////////////////////////////////////////REPORT STEP 3/////////////////////////////
				$t = new TableCreator();
				$heading = "SECTION LIST"; //CHANGE
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