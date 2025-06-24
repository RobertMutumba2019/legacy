<?php
Class AreaOffice extends BeforeAndAfter{
	public $page = "AREA OFFICE";
	
	public function __construct(){
		$access = new AccessRights();
		//$access->pageAccess(user_id(), $this->page, 'V');
	}
	
	public function getLinks(){
		$page = "AREA OFFICE";
		$links = array(
			array(
				"link_name"=>"Add Area Office", 
				"link_address"=>"area-office/add-area-office",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"View Area Offices", 
				"link_address"=>"area-office/all-area-offices",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
		
		return $links;
	}
	
	public function deleteAreaOfficeAction(){
		$id = portion(3);
		$this->deletor("area_office", "area_office_id",  $id, 'area-office/all-area-offices');
	}
	
	public function AddAreaOfficeAction(){
		if(isset($_POST['submit'])){
		
			$area_office = $_POST['area_office'];
			$district_code = $_POST['district_code'];
			$territory = $_POST['territory'];
			$cost_center = $_POST['cost_center'];
			$time = time();
			$user = user_id();
			$db = new Db();

			$errors = array();
			if(empty($district_code)){
				$errors[]="Select District Code";
			}
			if(empty($area_office)){
				$errors[]="Enter Area Office";
			}
			
			if(empty($territory)){
				$errors[]="Select Territory";
			}
			
			if(empty($cost_center)){
				$errors[]="Select Cost Center";
			}

			if($this->isThere("area_office", ["area_office_name"=>$area_office])){
				$errors[]="Area Office($area_office) already exists";
			}

			if(empty($errors)){
				$x = $db->insert("area_office",["area_office_name"=>$area_office,"area_office_district_code_id"=>$district_code, "area_office_territory_id"=>"$territory","area_office_added_by"=>$user,"area_office_date_added"=>$time, "area_office_status"=>1, "area_office_cost_center"=>$cost_center]);
								
				
				if(!$db->error()){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'area-office/all-area-offices');
				}else{
					FeedBack::error($db->error());
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
					Add Area Office Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>District Code<span class="must">*</span></label>
									<div class="form-line">
										<select data-show-subtext="true" data-live-search="true" style="width:100%" name="district_code" class="form-control select2">
											<option value="">Select</option>
											<?php 
											$db = new Db();
											$dc = $district_code;
											$select = $db->select("SELECT * FROM district ORDER BY district_name");
											foreach($select[0] as $row){
												extract($row);
												if($dc == $district_id){
													echo '<option data-subtext="'.$district_name.' - '.$district_code.'" selected="selected" value="'.$district_id.'">'.$district_name.' - '.$district_code.'</option>';
												}else{
													echo '<option data-subtext="'.$district_name.' - '.$district_code.'" value="'.$district_id.'">'.$district_name.' - '.$district_code.'</option>';
												}
											}
											?>
										</select>
										
									</div>
								</div>
								<div class="form-group">
									<label>Office Area Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="area_office" placeholder="Enter Office Area Name" value="<?php echo @$office_area_name; ?>">
									</div>
								</div>
								<div class="form-group">
									<label>Parent Territory<span class="must">*</span></label>
									<div class="form-line">
										<select name="territory">
											<option value="">Select</option>
											<?php
											$x = new Db();
											$select = $x->select("SELECT * FROM territory WHERE territory_status = 1 ORDER BY territory_name ASC");
											foreach($select[0] as $row){
												extract($row);
												if($territory == $territory_id){
													echo '<option selected="selected" value="'.$territory_id.'">'.$territory_name.'</option> ';
												}else{
													echo '<option value="'.$territory_id.'">'.$territory_name.'</option> ';
												}
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label>Cost Center<span class="must">*</span></label>
									<div class="form-line">
										<select name="cost_center">
											<option value="">Select</option>
											<?php
											$x = new Db();
											$select = $x->select("SELECT cost_center_id, cost_center_name FROM cost_center ORDER BY cost_center_name ASC");
											foreach($select[0] as $row){
												extract($row);
												if($cost_center == $cost_center_id){
													echo '<option selected="selected" value="'.$cost_center_id.'">'.$cost_center_name.'</option> ';
												}else{
													echo '<option value="'.$cost_center_id.'">'.$cost_center_name.'</option> ';
												}
											}
											?>
										</select>
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save these area office details ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php

	}
	
	
	public function editAreaOfficeAction(){
		$db = new Db();
		$id = portion(3);

		$select = $db->select("SELECT * FROM area_office WHERE area_office_id = '$id'");
		extract($select[0][0]);

		$cost_center = $area_office_cost_center;
		$district_code = $area_office_district_code_id;
		if(isset($_POST['submit'])){
		
			$district_code = $_POST['district_code'];
			$area_office = $_POST['area_office'];
			$territory = $_POST['territory'];
			$status = $_POST['status'];
			$cost_center = $_POST['cost_center'];
			$time = time();
			$user = user_id();
			$db = new Db();

			$errors = array();
			if(empty($area_office)){
				$errors[]="Enter Area Office";
			}

			if(empty($cost_center)){
				$errors[]="Select Cost Center";
			}
			
			if(empty($territory)){
				$errors[]="Select Territory";
			}

			if($this->isThereEdit("area_office", ["area_office_name"=>$area_office, "area_office_id"=>$id]))
			{
				$errors[]="Area Office($area_office) already exists";
			}

			if(empty($errors)){
				$x = $db->update("area_office",["area_office_district_code_id"=>$district_code, "area_office_name"=>$area_office, "area_office_territory_id"=>"$territory","area_office_added_by"=>$user,"area_office_date_added"=>$time, "area_office_status"=>$status, "area_office_cost_center"=>$cost_center], ["area_office_id"=>$id]);								
				
				if(!$db->error()){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'area-office/all-area-offices');
				}else{
					FeedBack::error($db->error());
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
					Edit Area Office Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>District Code<span class="must">*</span></label>
									<div class="form-line">
										<select data-show-subtext="true" data-live-search="true" style="width:100%" name="district_code" class="form-control select2">
											<option value="">Select</option>
											<?php 
											$db = new Db();
											$dc = $district_code;
											$select = $db->select("SELECT * FROM district ORDER BY district_name");
											foreach($select[0] as $row){
												extract($row);
												if($dc == $district_id){
													echo '<option data-subtext="'.$district_name.' - '.$district_code.'" selected="selected" value="'.$district_id.'">'.$district_name.' - '.$district_code.'</option>';
												}else{
													echo '<option data-subtext="'.$district_name.' - '.$district_code.'" value="'.$district_id.'">'.$district_name.' - '.$district_code.'</option>';
												}
											}
											?>
										</select>
										
									</div>
								</div>
								<div class="form-group">
									<label>Office Area Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="area_office" placeholder="Enter Office Area Name" value="<?php echo @$area_office_name; ?>">
									</div>
								</div>
								<div class="form-group">
									<label>Parent Territory<span class="must">*</span></label>
									<div class="form-line">
										<select name="territory">
											<option value="">Select</option>
											<?php
											$x = new Db();
											$select = $x->select("SELECT * FROM territory WHERE territory_status = 1 ORDER BY territory_name ASC");
											foreach($select[0] as $row){
												extract($row);
												if($area_office_territory_id == $territory_id){
													echo '<option selected="selected" value="'.$territory_id.'">'.$territory_name.'</option> ';
												}else{
													echo '<option value="'.$territory_id.'">'.$territory_name.'</option> ';
												}
											}
											?>
										</select>
									</div>
								</div>


								<div class="form-group">
									<label>Cost Center<span class="must">*</span></label>
									<div class="form-line">
										<select name="cost_center">
											<option value="">Select</option>
											<?php
											$x = new Db();
											$select = $x->select("SELECT cost_center_id, cost_center_name FROM cost_center ORDER BY cost_center_name ASC");
											foreach($select[0] as $row){
												extract($row);
												if($cost_center == $cost_center_id){
													echo '<option selected="selected" value="'.$cost_center_id.'">'.$cost_center_name.'</option> ';
												}else{
													echo '<option value="'.$cost_center_id.'">'.$cost_center_name.'</option> ';
												}
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

										if($area_office_status == 0)
											echo '<option value="0" selected="selected">'.$this->show2.'</option>';
										else
											echo '<option value="0">'.$this->show2.'</option>';

										if($area_office_status == 1)
											echo '<option value="1" selected="selected">'.$this->show1.'</option>';
										else
											echo '<option value="1">'.$this->show1.'</option>';
										echo '</select>';
										?>
									</div>
								</div>
								<button onclick = "return confirm('Are you sure you want to save those changes on AreaOffice ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	
	public function AllAreaOfficesAction(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<h3>Areea Offices List</h3>
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM area_office ORDER BY area_office_name ASC");
			
			if(!$select){
				$db->error();
			}else{
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>Area Office Name</th>';
				echo '<th>Cost Center</th>';
				echo '<th>Territory</th>';
				echo '<th>District Code</th>';
				echo '<th>Status</th>';
				if($access->sectionAccess(user_id(), $this->page, 'E') || $access->sectionAccess(user_id(), $this->page, 'D')){
					echo '<th width="100px">Action</th>';
				}
				echo '</tr>';
				echo '</thead>';
				
				////////////////////////////REPORT STEP 1//////////////////
				$db_values = array();
				$db_values[] = array(
				
					"No",
					"Area Office Name",
					"Cost Center",
					"Territory",
					"District Code",
					
								
				);
				//////////////////////////////////////////////////////////////
				
				
				$i=1;
				echo '<tbody>';
				foreach($select[0] as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.($area_office_name).'</td>';
					echo '<td>'. $this->rgf("cost_center", $area_office_cost_center,  "cost_center_id", "cost_center_name").'</td>';
					echo '<td>'. $this->rgf("territory", $area_office_territory_id,  "territory_id", "territory_name").'</td>';
					echo '<td title="'. $this->rgf("district", $area_office_district_code_id,  "district_id", "district_name").'">'. $this->rgf("district", $area_office_district_code_id,  "district_id", "district_code").'</td>';


					echo '<td>';
					if(empty($area_office_status)){
						echo $this->show22;
					}else{
						echo $this->show11;
					}
					echo '</td>';
					
					if($access->sectionAccess(user_id(), $this->page, 'E') || $access->sectionAccess(user_id(), $this->page, 'D')){
					
						echo '<td>';
						if($access->sectionAccess(user_id(), $this->page, 'E'))
							echo $this->action('edit','area-office/edit-area-office/'.$area_office_id, 'Edit');
						if($access->sectionAccess(user_id(), $this->page, 'D'))
							echo $this->action('delete','area-office/delete-area-office/'.$area_office_id, 'Delete');
						echo '</td>';
					}
					echo '</tr>';
					
					//////////////////////////////////REPORT STEP 2//////////////////////////////////	
					$db_values[] = array(
					
						($i-1),
						($area_office_name),
						$this->rgf("territory", $area_office_territory_id,  "territory_id", "territory_name")
						
					); 
					
					/////////////////////////////////////////////////////////////////////////////
					
				}
				echo '</tbody>';
				
				echo '</table>';
				
				//////////////////////////////////////////REPORT STEP 3/////////////////////////////
				$t = new TableCreator();
				$heading = "AREA-OFFICES LIST"; //CHANGE
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