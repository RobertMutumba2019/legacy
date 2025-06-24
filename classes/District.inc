<?php
Class District extends BeforeAndAfter{
	public $page = "DISTRICT";
	
	public function __construct(){
		$access = new AccessRights();
		//$access->pageAccess(user_id(), $this->page, 'V');
	}
	
	public function getLinks(){
		$page = "DISTRICT";
		$links = array(
			array(
				"link_name"=>"Add District", 
				"link_address"=>"district/add-district",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"View Districts", 
				"link_address"=>"district/all-districts",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
		
		return $links;
	}
	
	public function deleteDesignationAction(){
		$id = portion(3);
		//$this->deletor("designation", "designation_id",  $id, 'designation/all-user-role');
	}
	
	public function addDistrictAction(){
		if(isset($_POST['submit'])){
		
			$district = $_POST['district'];
			$code = $_POST['code'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($code)){
				$errors[]="Enter Code";
			}
			if(empty($district)){
				$errors[]="Enter district";
			}
			if($this->isThere("district", ["district_name"=>$district])){
				$errors[]="District ($district) already exists";
			}
			if($this->isThere("district", ["district_code"=>$code])){
				$errors[]="District Code ($code) already exists";
			}
			
			if(empty($errors)){
				$x = $db->insert("district",["district_date_added"=>$time, "district_name"=>"$district","district_added_by"=>$user,"district_code"=>$code]);
								
				
				if(!$db->error()){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'district/all-districts');
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
					Add District Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>District Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="district" placeholder="Enter District Name" value="<?php echo @$district; ?>">
									</div>
								</div>
								<div class="form-group">
									<label>District Code</label>
									<div class="form-line">
										<input class="form-control" type="text" name="code" placeholder="Enter Code" value="<?php echo @$code; ?>">
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save that designation ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	public function editDistrictAction(){
		$db = new Db();
		$id = portion(3);

		$select = $db->select("SELECT * FROM district WHERE district_id = '$id'");
		extract($select[0][0]);
		$district = $district_name;
		$code = $district_code;

		if(isset($_POST['submit'])){
		
			$district = $_POST['district'];
			$code = $_POST['code'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($code)){
				$errors[]="Enter Code";
			}
			if(empty($district)){
				$errors[]="Enter district";
			}
			if($this->isThereEdit("district", ["district_name"=>$district, "district_id"=>$id])){
				$errors[]="District ($district) already exists";
			}
			if($this->isThereEdit("district", ["district_code"=>$code, "district_id"=>$id])){
				$errors[]="District Code ($code) already exists";
			}
			
			if(empty($errors)){
				$x = $db->update("district",["district_date_added"=>$time, "district_name"=>"$district","district_added_by"=>$user,"district_code"=>$code], ["district_id"=>$id]);
								
				
				if(!$db->error()){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'district/all-districts');
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
					Add District Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>District Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="district" placeholder="Enter District Name" value="<?php echo @$district; ?>">
									</div>
								</div>
								<div class="form-group">
									<label>District Code</label>
									<div class="form-line">
										<input class="form-control" type="text" name="code" placeholder="Enter Code" value="<?php echo @$code; ?>">
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save that designation ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
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
		$select=$db->select("select * from district WHERE district_id='$id'");
		extract($select[0][0]);
		if(isset($_POST['submit'])){
		
			$district = $_POST['district'];
			$code = $_POST['code'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($district)){
				$errors[]="Enter District";
			}
			if(empty($code)){
				$errors[]="Enter Code";
			}
			if($this->isThereEdit("district", ["district_name"=>$district,"district_id"=>$id])){
				$errors[]="District($district) already exists";
			}
			if($this->isThereEdit("district", ["district_code"=>$code,"district_id"=>$id])){
				$errors[]="District Code($code) already exists";
			}
			
			if(empty($errors)){
				$db = new Db();

				$x = $db->update("district",["district_date_added"=>$time, "district_name"=>"$district","district_added_by"=>$user,"district_code"=>$code],["district_id"=>$id]);
				
				if(!$db->error()){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'district/all-districts');
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
					Edit District Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>District Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="district" placeholder="Enter User Role Name" value="<?php echo @$district_name; ?>">
									</div>
								</div>
								<div class="form-group">
									<label>District Code</label>
									<div class="form-line">
										<input class="form-control" type="text" name="code" placeholder="Enter District Code" value="<?php echo @$district_code; ?>">
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
	
	public function AllDistrictsAction(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<h3>District List</h3>
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM district ORDER BY district_name ASC");
			
			if(!$select){
				$db->error();
			}else{
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>District Name</th>';
				echo '<th>District Code</th>';
				//echo '<th>Total</th>';
				if($access->sectionAccess(user_id(), $this->page, 'E') || $access->sectionAccess(user_id(), $this->page, 'D')){
					echo '<th width="100px">Action</th>';
				}
				echo '</tr>';
				echo '</thead>';
				
					////////////////////////////REPORT STEP 1//////////////////
				$db_values = array();
				$db_values[] = array(
				
					"No",
				    "District Name",
				    "District Code",
				   // "Number"
								
				);
				//////////////////////////////////////////////////////////////
				$i=1;
				echo '<tbody>';
				foreach($select[0] as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.($district_name).'</td>';
					echo '<td>'.($district_code).'</td>';
					//echo '<td>'.$this->total("sysuser", "user_designation", $designation_id).'</td>';
					//echo '<td></td>';
					if($access->sectionAccess(user_id(), $this->page, 'E') || $access->sectionAccess(user_id(), $this->page, 'D')){
					
						echo '<td>';
						if($access->sectionAccess(user_id(), $this->page, 'E'))
							echo $this->action('edit','district/edit-district/'.$district_id, 'Edit');
						//if($access->sectionAccess(user_id(), $this->page, 'D'))
							//echo $this->action('delete','designation/delete-designation/'.$designation_id, 'Delete');
						echo '</td>';
					}
					echo '</tr>';
					
					//////////////////////////////////REPORT STEP 2//////////////////////////////////	
					$db_values[] = array(
					
						($i-1),
						($district_name),
						//($this->total("sysuser", "user_designation", $designation_id))
						"msi",
						
					); 
					
					/////////////////////////////////////////////////////////////////////////////
					
				}
				echo '</tbody>';
				
				echo '</table>';
				
				//////////////////////////////////////////REPORT STEP 3/////////////////////////////
				$t = new TableCreator();
				$heading = "DISTRICTS LIST"; //CHANGE
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