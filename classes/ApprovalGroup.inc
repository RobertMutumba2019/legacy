<?php
Class ApprovalGroup extends BeforeAndAfter{
	public $page = "APPROVALGROUP";
	public $id = 0;
	public function __construct(){
		$access = new AccessRights();
		
		if(portion(2) == "all-approval-group"){
			if(!$access->sectionAccess(user_id(), $this->page, 'V')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url()."dashboard/index");
			}
		}else if(portion(2)=="add-approval-group"){
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
		$page = "APPROVALGROUP";
		$links = array(
			array(
				"link_name"=>"Add Approver to Group", 
				"link_address"=>"approvalgroup/add-approval-group",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"All Approvers & Groups", 
				"link_address"=>"approvalgroup/all-approval-group",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Import Approval Groups", 
				"link_address"=>"approvalgroup/import-approval-groups",
				"link_icon"=>"fa-upload",
				"link_page"=>$page,
				"link_right"=>"V",
			),
		);
		
		return $links;
	}
	
	public function deleteApprovalGroupAction(){
		$id = portion(3);
		$db = new Db();
		$apg_user = $this->rgf("approval_group", $id, "apg_id", "apg_user");
		$update = $db->update("sysuser", ["user_role"=>1084], ["user_id"=>$apg_user]);
		$this->deletor("approval_group", "apg_id",  $id, 'approvalgroup/all-approval-group');
	}

	public function importgroupsAction(){

		?>

	<?php	
	}
	
	public function addApprovalGroupAction(){
		if(isset($_POST['submit'])){
			$groupname = $_POST['groupname'];
			$groupuser = $_POST['groupuser'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($groupname)){
				$errors[]="Select Group Name";
			}
			if(empty($groupuser)){
				$errors[]="Select Users Name";
			}
			if($this->isThere("Approval_group", ["gr_name"=>$groupname])){
				$errors[]="Group Name($groupname) already exists";
			}
			
			if(empty($errors)){
				$db->insert("approval_group",["apg_date_added"=>time(),"apg_name"=>$groupname,"apg_user"=>$groupuser,"apg_added_by"=>user_id()]);
				$update = $db->update("sysuser", ["user_role"=>1082], ["user_id"=>$groupuser]);
				
				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'approvalgroup/all-approval-group');
				}else{
					FeedBack::error($db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
		}
		?>
		<div class="col-md-5">
			<h3>&nbsp;</h3>
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Add Approval Group Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Group Name<span class="must">*</span></label>
									<div class="form-line">
										<select id="group" style="width:100%" class="select2" name="groupname">
											<?php 
											$uid = user_id();
											$db=new Db();
											$select=$db->select("SELECT * FROM groups");
											echo '<option value="">--- select ---</option>';
											foreach ($select as $row) {
												extract($row);
												if($groupname == $gr_id)
													echo '<option selected value="'.$gr_id.'" >'.$gr_name.'</option>';
												else
													echo '<option value="'.$gr_id.'" >'.$gr_name.'</option>';
											}
											?>
										</select>
									</div>
									<label>Users Name<span class="must">*</span></label>
									<div class="form-line">
										<select id="user" style="width:100%" class="select3" name="groupuser">
											<?php 
											$uid = user_id();
											$db=new Db();
											$existing_approvals = array();
											$select = $db->select("SELECT apg_user FROM approval_group");
											foreach($select as $row){
												extract($row);
												$existing_approvals[] = $apg_user;
											}
											$select=$db->select("SELECT * FROM sysuser ORDER BY user_surname ASC ");
											echo '<option value="">--- select ---</option>';
											foreach ($select as $row) {
												extract($row);
												if(!in_array($user_id, $existing_approvals))
												{
													if($groupuser == $user_id)
														echo '<option selected value="'.$user_id.'" >'.$check_number.' - '.$user_surname.' '.$user_othername.'</option>';
													else
														echo '<option value="'.$user_id.'" >'.$check_number.' - '.$user_surname.' '.$user_othername.'</option>';
												}
											}
											?>
										</select>
										<script type="text/javascript">
											$('.select3').select2();
											$('.select2').select2();
										</script>
									</div>
								</div>
								
								<button  type="button" id="AddUserToGroup" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><span id="status"></span>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	public function importApprovalGroupsAction(){
		$id = portion(3);
		$db = new Db();

		$db = new Db();
		$time = time();
		$user = user_id();
		$errors = array();
		if (isset($_POST['uploaddata'])) { 
			$filename=$_FILES["uploadFile"]["tmp_name"];
			if($_FILES["uploadFile"]["size"] > 0){

				$valid_name = "PF";
				$file = fopen($filename, "r");
				while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
					$count++;
					$col_1 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[0]))));
					$col_2 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[1]))));
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
						if(empty($col_2)){
							$errors[] = "Cell <b>B".$count."</b> should not be empty";
						}



						$d = new Db();
						$col_1 = (int)$col_1;
						$sql = "SELECT user_id FROM sysuser WHERE check_number = '$col_1'";
						$s = $d->select($sql);
						if($d->num_rows()) extract($s[0]);
						
						$col_21 = $this->rgf("groups", $col_2, "gr_name", "gr_id");				

						if($col_1&&$col_2){
						//checking duplicates
							if(!$user_id){
								$errors[] = "Cell <b>A".$count."</b> does not exit";
							}
							if(!$col_21){
								$errors[] = "Cell <b>B".$count."</b> does not exit";
							}
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
							$col_2 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[1]))));
							$col_2 = $this->rgf("groups", $col_2, "gr_name", "gr_id");	
							$d = new Db();
							
							$col_1 = (int)$col_1;
							$s = $d->select("SELECT user_id AS col_1 FROM sysuser WHERE check_number = '$col_1'");
							if($d->num_rows())
							extract($s[0]);
							$db->insert("approval_group",["apg_date_added"=>time(),"apg_name"=>$col_2,"apg_user"=>$col_1,"apg_added_by"=>user_id()]);
							$update = $db->update("sysuser", ["user_role"=>1082], ["user_id"=>$col_1]);
						}
					}
					
					if(!$db->error()){
						FeedBack::success();
						//FeedBack::refresh(3, return_url().'department/all-departments');
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

	public function editApprovalGroupAction(){
		$id=portion(3);
		$id = $this->id;
		$db=new Db();
		$select=$db->select("select * from approval_group WHERE apg_id ='$id'");
		extract($select[0]);
		$groupname = $apg_name;
		$groupuser = $apg_user;

		if(isset($_POST['submit'])){
			$groupname = $_POST['groupname'];
			$groupuser = $_POST['groupuser'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($groupname)){
				$errors[]="Select Group Name";
			}
			if(empty($groupuser)){
				$errors[]="Select Users Name";
			}
			// if($this->isThere("Approval_group", ["gr_name"=>$groupname])){
			// 	$errors[]="Group Name($groupname) already exists";
			// }

			
			if(empty($errors)){
				$db->update("approval_group",["apg_date_added"=>time(),"apg_name"=>$groupname,"apg_user"=>$groupuser,"apg_added_by"=>user_id()],["apg_id"=>$id]);
				
				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'approvalgroup/all-approval-group');
				}else{
					FeedBack::error($db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
		}
		?>
		<div class="col-md-5">
			<h3>&nbsp;</h3>
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Add Approval Group Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Group Name<span class="must">*</span></label>
									<div class="form-line">
										<select id="group" class="select3" style="width:100%;" name="groupname">
											<?php 
											$uid = user_id();

											$existing_approvals = array();
											$select = $db->select("SELECT apg_user FROM approval_group where apg_user != '$apg_user'");
											foreach($select as $row){
												extract($row);
												$existing_approvals[] = $apg_user;
											}

											$db=new Db();
											$select=$db->select("SELECT * FROM groups");
											echo '<option value="">--- select ---</option>';
											foreach ($select as $row) {
												extract($row);
												if($groupname == $gr_id)
													echo '<option selected value="'.$gr_id.'" >'.$gr_name.'</option>';
												else
													echo '<option value="'.$gr_id.'" >'.$gr_name.'</option>';
											}
											?>
										</select>
									</div>
									<label>Users Name<span class="must">*</span></label>
									<div class="form-line">
										<select id="user" class="select3" style="width:100%;" name="groupuser">
											<?php 
											$uid = user_id();
											$db=new Db();
											$select=$db->select("SELECT * FROM sysuser");
											echo '<option value="">--- select ---</option>';
											foreach ($select as $row) {
												extract($row);
												if(!in_array($user_id, $existing_approvals)){
													if($groupuser == $user_id)
														echo '<option selected value="'.$user_id.'" >'.$user_surname.' '.$user_othername.'</option>';
													else
														echo '<option value="'.$user_id.'" >'.$user_surname.' '.$user_othername.'</option>';
												}
											}
											?>
										</select>
									</div>
								</div>
								<input type="hidden" value="<?php echo $id; ?>" id="ggid"/>
								<script type="text/javascript">
									$(document).ready(function(){									$('.select3').select2();
									});
								</script>								
								<button id="EditUserToGroup" type="button" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><span id="status"></span>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	public function AllApprovalGroupAction(){
		$access = new AccessRights();
	?>
		<div class="col-md-12">
			<?php 
			
			echo '<div class="row" style="margin-bottom:5px;">';
			echo '<input type="hidden" value="1" id="eagleActivePage">';
			echo '<div class="col-md-4">';
			echo 'Rows Per Page: ';
			echo '<select id="rowsPerPage">';
			echo '<option value="20">20</option>';
			echo '<option value="50">20</option>';
			echo '<option value="100">100</option>';
			echo '<option value="200">200</option>';
			echo '</select>';
			echo '</div>';

			echo '<div class="col-md-8">';
			echo '<div class="pull-right">';
			echo 'Search: ';
			echo '<input id="searchWord" type="text">';
			echo '<button class="btn btn-xs btn-success" id="eagleSearchBtn" type="button"><i class="fa fa-fw fa-search"></i> Search</button>';
			echo ' &nbsp; &nbsp; &nbsp; ';
			echo '<button class="btn btn-xs btn-danger" id="eagleResetBtn" data-url="'.return_url().'approval-group/all-approval-group" type="button"><i class="fa fa-fw fa-refresh"></i> Reset</button>';

			echo '</div>';
			echo '</div>';

			echo '</div>';
			echo '<div class="code-eagle-table"></div>';
			echo '</div>';
			
			?>
		</div>

	    <script>
	        $(function(){
	            $(document).on('click', '.eagle-page-link, #eagleSearchBtn', function(){
	                $('#eagleActivePage').val($(this).attr('data-number'));
	                eagleSearch();
	            });
	            $(document).on('change', '#rowsPerPage', function(){
	                eagleSearch();
	            });
	            $(document).on('click', '.eagle-sort, .sort-asc, .sort-desc', function(e) {
	                e.preventDefault();
	            
	                var id = $(this).attr('id');
	                var valueNow = $(this).attr('class');
	                var value = "";

	                if(valueNow=="eagle-sort"){
	                    $('#'+id).toggleClass('eagle-sort sort-asc');
	                    value = "ASC";
	                }else if(valueNow=="sort-desc"){
	                    $('#'+id).toggleClass('sort-desc sort-asc');
	                    value = "ASC";
	                }else if(valueNow=="sort-asc"){
	                    $('#'+id).toggleClass('sort-desc sort-asc');
	                    value = "DESC";
	                }

	                eagleSearch(id, value);
	            });
	        });	        
	    
	    
            $("#eagleResetBtn").click(function(){
                $('.code-eagle-table').fadeTo('normal', 0.4);
                location.replace($(this).attr('data-url'));
            });
            $(".code-eagle-table").ready(function(){
                eagleSearch();
            });

		    function eagleSearch(label="", value=""){

		        $('.code-eagle-table').fadeTo('normal', 0.4);

		        var rowsPerPage = $('#rowsPerPage').val();
		        var searchWord = $('#searchWord').val();
		        var eagleActivePage = $('#eagleActivePage').val();

		        var form_data = new FormData();
		        form_data.append('value', value);
		        form_data.append('label', label);
		        form_data.append('rowsPerPage', rowsPerPage);
		        form_data.append('searchWord', searchWord);
		        form_data.append('eagleActivePage', eagleActivePage);
		        $.ajax({
		            url: $('#urlPath').val()+"ajax/approval-group-table.php",
		            type: "POST",
		            data: form_data,
		            contentType: false,
		            cache: false,
		            processData:false,
		            success: function(data){                
		                $('.code-eagle-table').fadeTo('normal', 1);
		                $('.code-eagle-table').html(data);
		            }
		        });
		    }
	    </script>
		<?php
	}
	
	
	
	public function sdsdAllApprovalGroupActionrewrwer(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM groups,sysuser,approval_group WHERE groups.gr_id=approval_group.apg_name AND sysuser.user_id=approval_group.apg_user ORDER BY apg_date_added ASC");
			//echo$db->error();
			if($db->error()){
				echo $db->error();
			}else{
				//print_r($select);
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>PF</th>';
				echo '<th>Group Name</th>';
				echo '<th> Name</th>';
				echo '<th>Date Added</th>';
				echo '<th>Added By</th>';
				echo '<th width="150px">Action</th>';
				echo '</tr>';
				echo '</thead>';

				$db_values = array();
				$db_values[] = array(
				
					"PF",
					"GROUP NAME",
					" NAME",
					"DATE ADDED"
					);

				$i=1;
				echo '<tbody>';
				foreach($select as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.($check_number).'</td>';
					echo '<td>'.($gr_name).'</td>';
					echo '<td>'.($user_surname).' '.($user_othername).'</td>';
					
					echo '<td>'.FeedBack::date_fm($apg_date_added).'</td>';
					echo '<td>'.$this->full_name($apg_added_by).'</td>';
					echo '<td>';
							echo $this->action('edit','approvalgroup/edit-approval-group/'.$apg_id, 'Edit');
							echo '<a title="Remove '.($user_surname).' '.($user_othername).'" onclick="return confirm(\'Do you really want to remove'.($user_surname).' '.($user_othername).'\');" class="btn btn-xs btn-danger" href="'.return_url().'approval-group/delete-approval-group/'.$apg_id.'">Remove User</a>';
							//echo $this->action('delete','approvalgroup/delete-approval-group/'.$apg_id, 'Delete');
					echo '</td>';

					$db_values[] = array(
					
						($i),
						$check_number,
						$gr_name,
						$user_surname.' '.$user_othername,
						FeedBack::date_fm($apg_date_added),					
					); 

				}
				echo '</tbody>';
				
				echo '</table>';

				echo '<br/>';

				// $t = new TableCreator();
				// $heading = "LIST OF ALL APPROVERS"; //CHANGE
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
}