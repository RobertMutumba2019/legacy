<?php
Class Item extends BeforeAndAfter{

	public $page = "ITEM";

	public function __construct(){
		$access = new AccessRights();
		
		if(portion(2) == "all-item"){
			if(!$access->sectionAccess(user_id(), $this->page, 'V')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url()."dashboard/index");
			}
		}else if(portion(2)=="add-item"){
			if(!$access->sectionAccess(user_id(), $this->page, 'A')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url()."dashboard/index");
			}
		}
	}
	
	public function deleteItemsAction(){
		$id = portion(3);
		$this->deletor("item", "item_id",  $id, 'item/all-item');
	}

	public function deleteSectionAction(){
		$id = portion(3);
		$this->deletor("section", "section_id",  $id, 'department/all-sections');
	}
	
	public function getLinks(){
		$page = "ITEM";
		$links = array(
			array(
				"link_name"=>"Add Item", 
				"link_address"=>"item/add-item",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"View Items", 
				"link_address"=>"item/all-item",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Import Items", 
				"link_address"=>"item/import-items",
				"link_icon"=>"fa-upload",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
		
		return $links;
	}
	
	public function importItemsAction(){
		$db = new Db();
		$time = time();
		$user = user_id();
		$errors = array();
		if (isset($_POST['uploaddata'])) { 
			$filename=$_FILES["uploadFile"]["tmp_name"];
			if($_FILES["uploadFile"]["size"] > 0){

				$valid_name = "ITEM CODE";
				$file = fopen($filename, "r");
				while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
					$count++;
					$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));
					$col_2 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[1]))));
					$col_3 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[2]))));
					if($count == 1){
						//echo "$col_1 is equal to $valid_name";
						if($col_1 != $valid_name){
							$errors[] = "Invalid Template";
						}
					}else{
						//checking if there is not empty field
						if(empty($col_1)){
							$errors[] = "Cell <b>A".$count."</b> should not be empty";
						}elseif(empty($col_2)){
							$errors[] = "Cell <b>B".$count."</b> should not be empty";
						}
						elseif(empty($col_3)){
							$errors[] = "Cell <b>C".$count."</b> should not be empty";
						}

						//checking duplicates
						// $department_id = $this->rgf("department", $col_1, "dept_name", "department_id");
						// if($department_id){
						// 	$errors[] = "Cell <b>A".$count."</b> already exists";
						// }
					}
					
					echo $db->error();
				}
				fclose($file);

				if(empty($errors)){
					$db = new Db();

					$file = fopen($filename, "r");
					while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
						$count++;
						$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));
						$col_2 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[1]))));
						$col_3 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[2]))));
						$db->insert("item",["item_date_added"=>$time,"item_code"=>$col_1,"item_name"=>$col_2,"item_unit_of_measure"=>$col_3,"item_added_by"=>$user,]);
					}
					
					if(!$db->error()){
						FeedBack::success();
						FeedBack::refresh(3, return_url().'item/all-item"');
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
				<a href="../import file/items.csv">Download Template</a>
			</div>
		</div>

	<?php	
	} 
	public function AddItemAction(){
		if(isset($_POST['submit'])){
			$itemcode = $_POST['itemcode'];
			$itemname = $_POST['itemname'];
			$unitmeasure = $_POST['unitmeasure'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();

			if(empty($itemcode)){
				$errors[] = "Enter Item Code";
			}
			if(empty($itemname)){
				$errors[] = "Enter Item Name";
			}
			if(empty($unitmeasure)){
				$errors[] = "Enter Unit Measure";
			}
			
			// if($this->isThere("department", ["dept_name"=>$department])){
			// 	$errors[]="department($department) already exists";
			// }
				
			if(empty($errors)){
				$x = $db->insert("item",["item_date_added"=>$time,"item_code"=>$itemcode,"item_name"=>$itemname,"item_unit_of_measure"=>$unitmeasure,"item_added_by"=>$user,]);
				
				if($x){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'item/all-item');
				}else{
					FeedBack::error();
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
					Add Item Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
							<div id="must">All fields with asterisk(*) are mandatory.</div>
							<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Code<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="itemcode" placeholder="Enter Item Code" value="<?php echo @$itemcode; ?>">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Item Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="itemname" placeholder="Enter Item Name" value="<?php echo @$itemname; ?>">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Unit of measure<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="unitmeasure" placeholder="Enter Unit Measure" value="<?php echo @$unitmeasure; ?>">
									</div>
								</div>
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
	
	public function AllitemAction(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<h3>Item List</h3>
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM item order by item_date_added asc");
			
			if(!$select){
				$db->error();
			}else{
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>Item Code</th>';
				echo '<th>Name</th>';
				echo '<th>Unit OF Measure</th>';
				echo '<th>Date Added</th>';
				echo '<th>Added By</th>';
				echo '<th width="100px">Action</th>';
				echo '</tr>';
				echo '</thead>';
				
				$i=1;
				echo '<tbody>';
				foreach($select[0] as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.($item_code).'</td>';
					echo '<td>'.($item_name).'</td>';
					echo '<td>'.($item_unit_of_measure).'</td>';
					echo '<td>'.FeedBack::date_fm($item_date_added).'</td>';
					echo '<td>'.$this->full_name($item_added_by).'</td>';
					echo '<td>';
							echo $this->action('edit','item/edit-item/'.$item_id, 'Edit');
							echo $this->action('delete','item/delete-items/'.$item_id, 'Delete');
					echo '</td>';

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
	
	public function EdititemAction(){
		$id = portion(3);
		$db = new Db();
		$select = $db->select("SELECT * FROM item WHERE item_id ='$id'");
		
		extract($select[0][0]);
            if(isset($_POST['submit'])){
			$itemcode = $_POST['itemcode'];
			$itemname = $_POST['itemname'];
			$unitmeasure = $_POST['unitmeasure'];
			$time = time();
			$user = user_id();
			$errors = array();

			if(empty($itemcode)){
				$errors[] = "Enter Item Code";
			}
			if(empty($itemname)){
				$errors[] = "Enter Item Name";
			}
			if(empty($unitmeasure)){
				$errors[] = "Enter Unit Measure";
			}
			
			// if($this->isThere("department", ["dept_name"=>$department])){
			// 	$errors[]="department($department) already exists";
			// }
				
			if(empty($errors)){
				$x = $db->update("item",["item_date_added"=>$time,"item_code"=>$itemcode,"item_name"=>$itemname,"item_unit_of_measure"=>$unitmeasure,"item_added_by"=>$user],["item_id"=>$id]);
				
				if($x){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'item/all-item');
				}else{
					FeedBack::error();
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
					Edit Item Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
							<div id="must">All fields with asterisk(*) are mandatory.</div>
							<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Code<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="itemcode" placeholder="Enter Item Code" value="<?php echo @$item_code; ?>">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Item Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="itemname" placeholder="Enter Item Name" value="<?php echo @$item_name; ?>">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Unit of measure<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" name="unitmeasure" placeholder="Enter Unit Measure" value="<?php echo @$item_unit_of_measure; ?>">
									</div>
								</div>
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