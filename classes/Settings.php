<?php
class Settings extends BeforeAndAfter{
	public $page = "SETTINGS";
	public function getLinks(){
		$page = "SETTINGS";
		
		return array(
			array(
				"link_name"=>"Request Vehicle", 
				"link_address"=>"settings/vehicle-and-fuel-settings",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
	}
	
	public function __construct(){
		$access = new AccessRights();
		$access->pageAccess(user_id(), $this->page, 'V');
	}
	
	public function vehicleAndFuelSettings(){
		$db = new Db();
		
		$select = $db->select("select * from settings_vehicle_fuel WHERE settings_id = 1");
		if (is_array($select) && isset($select[0]) && is_array($select[0])) {
		extract($select[0][0]);
		}
		/*echo '<pre>';
		print_r($select[0]);
		echo '</pre>';*/
		
		if(isset($_POST['submit'])){
			
							
			$settings_fuel_request_validity = addslashes($_POST['settings_fuel_request_validity']);
			$settings_fuel_consumption = addslashes($_POST['settings_fuel_consumption']);
			$settings_vehicle_request_validity = addslashes($_POST['settings_vehicle_request_validity']);
			$settings_dialy_distance_territory = addslashes($_POST['settings_dialy_distance_territory']);
			$settings_expiry_reminder = addslashes($_POST['settings_expiry_reminder']);			
			$settings_radius_out_of_kampala = addslashes($_POST['settings_radius_out_of_kampala']);		
			
			$user_id = user_id();
			$time = time();
			
			$insert = $db->update("settings_vehicle_fuel", [
			"settings_fuel_request_validity"=>$settings_fuel_request_validity, 
			"settings_fuel_consumption"=>$settings_fuel_consumption,
			"settings_vehicle_request_validity"=>$settings_vehicle_request_validity,
			"settings_dialy_distance_territory"=>$settings_dialy_distance_territory,
			"settings_expiry_reminder"=>$settings_expiry_reminder,
			"settings_radius_out_of_kampala"=>$settings_radius_out_of_kampala,
			"settings_date_added"=>$time,
			"settings_added_by"=>$user_id
			],["settings_id"=>1]);
			
			echo $db->error();
			if($insert){
				FeedBack::success();
				FeedBack::refresh();
			}else{
				FeedBack::error('Not Saved, '.$db->error());
			}			
					
		}	
			
		?>
		<div class="col-lg-12">
			<form role="form" action="" method="post">
			<div class="panel panel-default">
				<div class="panel-heading">
					Fuel Settings Form
				</div>
				<div class="panel-body">
					<div class="row">
						
							<div class="col-lg-6">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Request Validity (days)<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="settings_fuel_request_validity" value="<?php echo @$settings_fuel_request_validity; ?>">
									</div>
								</div>
							</div>							
							
							<div class="col-lg-6">
								<div class="form-group">
									<label>Acceptable Fuel Consumption (lts/Km)<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="settings_fuel_consumption" value="<?php echo @$settings_fuel_consumption; ?>">
									</div>
								</div>
							</div>
							
							<div class="clearfix"></div>
							
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					Vehicle Settings Form
				</div>
				<div class="panel-body">
					<div class="row">
						<form role="form" action="" method="post">
							<div class="col-lg-4">
								<div class="form-group">
									<label>Request Validity (days)<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="settings_vehicle_request_validity" value="<?php echo @$settings_vehicle_request_validity; ?>">
									</div>
								</div>
							</div>							
							
							<div class="col-lg-4">
								<div class="form-group">
									<label>Dialy Average Distance - territory (Km/Day) <span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="settings_dialy_distance_territory" value="<?php echo @$settings_dialy_distance_territory; ?>">
									</div>
								</div>
							</div>						
							
							<div class="col-lg-4">
								<div class="form-group">
									<label>Permit/Insurance Expiry Reminders(days) <span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="settings_expiry_reminder" value="<?php echo @$settings_expiry_reminder; ?>">
									</div>
								</div>
							</div>					
							
							<div class="col-lg-4">
								<div class="form-group">
									<label>Radius of Kampala(Km)Requiring MD's Approval<span class="must">*</span> </label>
									<div class="form-line">
										<input class="form-control" type="text" name="settings_radius_out_of_kampala" value="<?php echo @$settings_radius_out_of_kampala; ?>">
									</div>
								</div>
							</div>
							
							<div class="clearfix"></div>
							
						
					</div>
				</div>
			</div>
			<?php 
			$access = new AccessRights();
			if($access->sectionAccess(user_id(), $this->page, 'E')){ ?>
			<div class="col-lg-12">
				<button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
			</div>
			<?php } ?>
			</form>
		</div>
		<?php
		

	}

		
	
	public function fuelSettingsAction(){
		$db = new Db();
		if(isset($_POST['submit'])){
			
			$surname = addslashes(ucwords(strtolower($_POST['surname'])));			
			$othername = addslashes(ucwords(strtolower($_POST['othername'])));			
			$username = addslashes($_POST['username']);			
			$email = addslashes($_POST['email']);				
			$telephone = addslashes($_POST['telephone']);
			$password = addslashes($_POST['password']);	
			$cpassword = addslashes($_POST['cpassword']);	
			
			$user_section_id = addslashes($_POST['user_section_id']);	
			$user_department_id = addslashes($_POST['user_department_id']);	
			$user_branch_id = addslashes($_POST['user_branch_id']);
			
			$gender = addslashes($_POST['gender']);
			$designation = addslashes($_POST['designation']);
			
			$user_role = addslashes($_POST['user_role']);

			$user_id = user_id();
			$time = time();
			
			$insert = $db->insert("sysuser", ["user_name"=>$username, "user_surname"=>$surname,
			"user_othername"=>$othername,
			"user_status"=>1,
			"user_designation"=>$designation,
			"user_branch_id"=>$user_branch_id,
			"user_email"=>$email,
			"user_telephone"=>$telephone,
			"user_gender"=>$gender,
			"user_section_id"=>$user_section_id,
			"user_department_id"=>$user_department_id,
			"user_password"=>$password,
			"user_date_added"=>$time,
			"user_added_by"=>$user_id
			]);
			
			echo $db->error();
			if($insert){
				FeedBack::success();
				//FeedBack::refresh();
			}else{
				FeedBack::error('Not Saved, '.$db->error());
			}
			
					
		}	

			
		?>
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Add User Form
				</div>
				<div class="panel-body">
					<div class="row">
						<form role="form" action="" method="post">
							<div class="col-lg-6">
								<div class="form-group">
									<label>Surname</label>
									<div class="form-line">
										<input class="form-control" type="text" name="surname" value="<?php echo @$surname; ?>">
									</div>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label>Othername(s)</label>
									<div class="form-line">
										<input class="form-control" type="text" name="othername" value="<?php echo @$othername; ?>">
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-4">
								<div class="form-group">
									<label>Telephone Phone</label>
									<div class="form-line">
										<input class="form-control" type="phone" name="telephone" value="<?php echo @$telephone; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-4">
								<div class="form-group">
									<label>Email Address</label>
									<div class="form-line">
										<input class="form-control" type="text" name="email" value="<?php echo @$email; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-4">
								<div class="form-group">
									<label>Gender</label>
									<div class="form-line">
										<select name="gender">
											<option value="1">Male</option>
											<option value="0">Female</option>
										</select>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-4">
								<div class="form-group">
									<label>Username</label>
									<div class="form-line">
										<input class="form-control" type="text" name="username" value="<?php echo @$username; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-4">
								<div class="form-group">
									<label>Password</label>
									<div class="form-line">
										<input class="form-control" type="password" name="password" value="<?php echo @$password; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-4">
								<div class="form-group">
									<label>Confirm Password</label>
									<div class="form-line">
										<input class="form-control" type="password" name="cpassword" value="<?php echo @$cpassword; ?>">
									</div>
								</div>
							</div>	
							<div class="clearfix"></div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Branch</label>
									<div class="form-line">
										<select name="user_branch_id">
											<option value="">--Select--</option>
											<?php
											$db = new Db();
											$select = $db->select("SELECT branch_id, branch_name FROM branch ORDER BY branch_name ASC");
											
											if (is_array($select) && isset($select[0]) && is_array($select[0])) {
											foreach($select[0] as $row){
												extract($row);
												if ($branch_id == $user_branch_id) {
                                                    echo '<option value="'.$branch_id.'" selected="selected">'.$branch_name.'</option>';
                                                } else {
                                                    echo '<option value="'.$branch_id.'">'.$branch_name.'</option>';
                                                }
											}
											}
											?>
										</select>
									</div>
								</div>
							</div>							
							<div class="col-lg-3">
								<div class="form-group">
									<label>Department</label>
									<div class="form-line">
										<select name="user_department_id">
											<option value="">--Select--</option>
											<?php
											$db = new Db();
											$select = $db->select("SELECT dept_id, dept_name FROM department ORDER BY dept_name ASC");
											
											if (is_array($select) && isset($select[0]) && is_array($select[0])) {
											foreach($select[0] as $row){
												extract($row);
												if ($dept_id == $department_id) {
                                                    echo '<option value="'.$dept_id.'" selected="selected">'.$dept_name.'</option>';
                                                } else {
                                                    echo '<option value="'.$dept_id.'">'.$dept_name.'</option>';
                                                }
											}
										}
											?>
										</select>
									</div>
								</div>
							</div>							
							<div class="col-lg-3">
								<div class="form-group">
									<label>Section</label>
									<div class="form-line">
										<select name="user_section_id">
											<option value="">--Select--</option>
											<?php
											$db = new Db();
											$select = $db->select("SELECT section_id, section_name FROM section ORDER BY section_name ASC");
											
											if (is_array($select) && isset($select[0]) && is_array($select[0])) {
											foreach($select[0] as $row){
												extract($row);
												if ($section_id == $user_section_id) {
                                                    echo '<option value="'.$section_id.'" selected="selected">'.$section_name.'</option>';
                                                } else {
                                                    echo '<option value="'.$section_id.'">'.$section_name.'</option>';
                                                }
											}
										}
											?>
										</select>
									</div>
								</div>
							</div>								
							<div class="col-lg-3">
								<div class="form-group">
									<label>Designation</label>
									<div class="form-line">
										<input class="form-control" type="text" name="designation" value="<?php echo @$designation; ?>">
									</div>
								</div>
							</div>	
							<div class="clearfix"></div>
															
							<div class="col-lg-3">
								<div class="form-group">
									<label>System User Role</label>
									<div class="form-line">
										<input class="form-control" type="text" name="user_role" value="<?php echo @$role_id; ?>">
									</div>
								</div>
							</div>	
							<div class="clearfix"></div>
							<div class="col-lg-12">
								<button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
		

	}
		
}
?>