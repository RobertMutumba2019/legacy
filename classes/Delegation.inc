<?php
Class Delegation extends BeforeAndAfter{

	public $page = "DELEGATION";

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
	
	public function deleteDelegationAction(){
		$id = portion(3);
		$this->deletor("delegation", "del_id",  $id, 'delegation/all-delegation');
	}

	
	public function getLinks(){
		$page = "DELEGATION";
		$links = array(
			array(
				"link_name"=>"Add Delegation", 
				"link_address"=>"delegation/add-delegation",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"My Delegation", 
				"link_address"=>"delegation/all-delegation",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			)
			// array(
			// 	"link_name"=>"Import Items", 
			// 	"link_address"=>"item/import-items",
			// 	"link_icon"=>"fa-upload",
			// 	"link_page"=>$page,
			// 	"link_right"=>"V",
			// )
		);
		
		return $links;
	}
	
	public function importItemsAction(){
		
		?>
  

	<?php	
	} 
	public function AddDelegationAction(){
		if(isset($_POST['submit'])){
			$delegateTo = $_POST['delegateTo'];
			$start_date = $_POST['start_date'];
			$end_date = $_POST['end_date'];
			$reason = $_POST['reason'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();

			if(empty($delegateTo)){
				$errors[] = "Select User";
			}
			if(empty($start_date)){
				$errors[] = "Enter Start Date";
			}
			if(empty($end_date)){
				$errors[] = "Enter End Date";
			}
			if(empty($reason)){
				$errors[] = "Enter Reason";
			}
			
			// if($this->isThere("department", ["dept_name"=>$department])){
			// 	$errors[]="department($department) already exists";
			// }
			
			$sd = strtotime($start_date." 12:00:00 am");
			$ed = strtotime($end_date." 11:59:59 pm");


			if($sd > $ed){
				$errors[] = "Invalid Dates";
			}

			//check if the user has a delegate
			$user_id = user_id();
			$sql = "SELECT del_start_date, del_end_date FROM delegation WHERE del_added_by = '$user_id' AND (del_start_date <= '$sd' AND del_end_date >= '$sd') OR (del_start_date <= '$ed' AND del_end_date >= '$ed')";
			$select = $db->select($sql);
			if($db->num_rows()){
				extract($select[0][0]);
				$errors[] = "You already have an existing Delegation in the same period<b> (".FeedBack::date_s($del_start_date).'-'.FeedBack::date_s($del_end_date).")</b>";
			}
				
			if(empty($errors)){
				$db->insert("delegation",["del_date_added"=>time(),"del_to"=>$delegateTo,"del_start_date"=>$sd,"del_end_date"=>$ed,"del_reason"=>$reason,"del_added_by"=>user_id()]);
				
				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'delegation/all-delegation');
				}else{
					FeedBack::error($db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
		}
		?>
		<div class="col-md-10">
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Delegation Item Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
							<div id="must">All fields with asterisk(*) are mandatory.</div>
							<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Delegation To<span class="must">*</span></label>
									<div class="form-line">
										<select class="form-control" name="delegateTo">
											<?php 
											$uid = user_id();
											$db=new Db();
											$select=$db->select("SELECT * FROM sysuser WHERE user_id != '$uid'");
											echo '<option value="">--- select ---</option>';
											foreach ($select[0] as $row) {
												extract($row);
												if($delegateTo == $user_id)
													echo '<option selected value="'.$user_id.'" >'.$user_surname.' '.$user_othername.'</option>';
												else
													echo '<option value="'.$user_id.'" >'.$user_surname.' '.$user_othername.'</option>';
											}
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Start Date<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="date" name="start_date" placeholder="Enter Item Name" value="<?php echo @$start_date; ?>">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>End Date<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="date" name="end_date" placeholder="Enter Unit Measure" value="<?php echo @$end_date; ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<label>Reason<span class="must">*</span></label>
								<textarea class="form-control" placeholder="Enter Reason" name="reason"><?php echo$reason;?></textarea>
							</div>	
						</div>	
						<div class="row">
							<div class="col-md-3" style="margin-top:20px;">
								<button type="submit" name="submit" class="form-control btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>	
							</div>
						</div>		
								
								
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public function AlldelegationAction(){
	$access = new AccessRights();

	$activeDelegate = $this->myDelegate(user_id(), time());
	if(empty($activeDelegate)){
		echo '<h5>ACTIVE DELEGATE</h5>';
		echo '<b>Name:</b> '.$this->full_name($activeDelegate['delegate']);
		echo '<br/><b>From:</b> '.FeedBack::date_s($activeDelegate['from']);
		echo '<br/><b>To:</b> '.FeedBack::date_s($activeDelegate['to']);
		echo '<br/><b>Duration:</b> '.ceil(($activeDelegate['to']-$activeDelegate['from'])/(24*60*60));
		echo '<br/>';
		echo '<br/>';
	}
	?>
		<div class="col-md-12">
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM delegation");
			//echo$db->error();
			if($db->error()){
				echo $db->error();
			}else{
				//print_r($select);
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>Delegation To</th>';
				echo '<th>Start Date</th>';
				echo '<th>End Date</th>';
				echo '<th>Reason</th>';
				//echo '<th>Date Added</th>';
				//echo '<th>Added By</th>';
				echo '<th width="100px">Action</th>';
				echo '</tr>';
				echo '</thead>';
				
				$i=1;
				echo '<tbody>';
				foreach($select[0] as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.$this->full_name($del_to).'</td>';
					echo '<td>'.FeedBack::date_fm($del_start_date).'</td>';
					echo '<td>'.FeedBack::date_fm($del_end_date).'</td>';
					echo '<td>'.($del_reason).'</td>';
					//echo '<td>'.FeedBack::date_fm($del_date_added).'</td>';
					//echo '<td>'.$this->full_name($del_added_by).'</td>';
					echo '<td>';
							echo $this->action('edit','delegation/edit-delegation/'.$del_id, 'Edit');
							echo $this->action('delete','delegation/delete-delegation/'.$del_id, 'Delete');
					echo '</td>';

				}
				echo '</tbody>';
				
				echo '</table>';

				// $t = new TableCreator();
				// $heading = "DELEGATION LIST";
				// $t->open($this->full_name(user_id()), $heading);
				// $t->thd($db_values);
				// $t->close();
				// $t->results();

				// $e = new Exporter();
				// echo $e->getDisplay($heading, $t->results());
			}
			
			?>
		</div>
		<?php
	}
	
	public function EditDelegationAction(){
		$id = portion(3);
		$db = new Db();
		$select = $db->select("SELECT * FROM delegation WHERE del_id ='$id'");
		
		extract($select[0][0]);
			if(isset($_POST['submit'])){
			$delegateTo = $_POST['delegateTo'];
			$start_date = $_POST['start_date'];
			$end_date = $_POST['end_date'];
			$reason = $_POST['reason'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();

			if(empty($delegateTo)){
				$errors[] = "Select User";
			}
			if(empty($start_date)){
				$errors[] = "Enter Start Date";
			}
			if(empty($end_date)){
				$errors[] = "Enter End Date";
			}
			if(empty($reason)){
				$errors[] = "Enter Reason";
			}
			
			// if($this->isThere("department", ["dept_name"=>$department])){
			// 	$errors[]="department($department) already exists";
			// }
			$sd = strtotime($start_date." 12:00:00 am");
			$ed = strtotime($end_date." 11:59:59 pm");


			//check if the user has a delegate
			$user_id = user_id();
			$sql = "SELECT del_start_date, del_end_date FROM delegation WHERE del_id != '$id' AND del_added_by = '$user_id' AND (del_start_date <= '$sd' AND del_end_date >= '$sd') OR (del_start_date <= '$ed' AND del_end_date >= '$ed')";
			$select = $db->select($sql);
			if($db->num_rows()){
				extract($select[0][0]);
				$errors[] = "You already have an existing Delegation in the same period<b> (".FeedBack::date_s($del_start_date).'-'.FeedBack::date_s($del_end_date).")</b>";
			}
				
			if(empty($errors)){
				$db->update("delegation",["del_date_added"=>$user,"del_to"=>$delegateTo,"del_start_date"=>$sd,"del_end_date"=>$ed,"del_reason"=>$reason,"del_added_by"=>$user],["del_id"=>$id]);
				
				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'delegation/all-delegation');
				}else{
					FeedBack::error($db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
		}

		if(empty($delegateTo))
			$delegateTo = $del_to;
		if(empty($start_date))
			$start_date = date('Y-m-d', $del_start_date);
		if(empty($end_date))
			$end_date = date('Y-m-d', $del_end_date);
		
		?>
		<div class="col-md-10">
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Delegation Item Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
							<div id="must">All fields with asterisk(*) are mandatory.</div>
							<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Delegation To<span class="must">*</span></label>
									<div class="form-line">
										<select class="form-control" name="delegateTo">
											<?php 
											$uid = user_id();
											$db=new Db();
											$select=$db->select("SELECT * FROM sysuser WHERE user_id = '$uid'");
											echo '<option value="">--- select ---</option>';
											foreach ($select[0] as $row) {
												extract($row);
												if($user_id == $delegateTo)
												echo '<option selected value="'.$user_id.'" >'.$user_surname.' '.$user_othername.'</option>';
												else
												echo '<option value="'.$user_id.'" >'.$user_surname.' '.$user_othername.'</option>';
											}
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Start Date<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="date" name="start_date" placeholder="Enter Item Name" value="<?php echo @$start_date; ?>">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>End Date<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="date" name="end_date" placeholder="Enter Unit Measure" value="<?php echo @$end_date; ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<label>Reason<span class="must">*</span></label>
								<textarea class="form-control" placeholder="Enter Reason" name="reason"><?php echo$del_reason;?></textarea>
							</div>	
						</div>		
								<button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
								
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
	
}