<?php
Class Groups extends BeforeAndAfter{
	public $page = "GROUPS";
	public $id = 0;
	public function id($id){
		$this->id = $id;
	}
	
	public function __construct(){
		$access = new AccessRights();
		
		if(portion(2) == "all-groups"){
			if(!$access->sectionAccess(user_id(), $this->page, 'V')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url()."dashboard/index");
			}
		}else if(portion(2)=="add-group"){
			if(!$access->sectionAccess(user_id(), $this->page, 'A')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url()."dashboard/index");
			}
		}
	}
	
	
	public static function getLinks(){
		$page = "GROUPS";
		$links = array(
			array(
				"link_name"=>"Add Group", 
				"link_address"=>"groups/add-group",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"View Group", 
				"link_address"=>"groups/all-groups",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Import Groups", 
				"link_address"=>"groups/import-groups",
				"link_icon"=>"fa-upload",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
		
		return $links;
	}
	
	public function deletegroupAction(){
		$id = portion(3);
		$this->deletor("groups", "gr_id",  $id, 'groups/all-groups');
	}

	public function importgroupsAction(){
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
					$col_3 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[2]))));

					if($count == 1){
						//checking template
						if($col_1 != $valid_name||$col_3 != "UNIT NAME" ||$col_2 != "GROUP NAME" ){
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
						if(empty($col_3)){
							$errors[] = "Cell <b>C".$count."</b> should not be empty";
						}

						//checking duplicates
						$application_id = $this->rgf("approval_matrix", $col_1, "ap_code", "ap_id");
						if(empty($application_id)){
							$errors[] = "Cell <b>A".$count."</b> does exist <b>".$col_1."-".$col_2."</b>";
						}

						$group_name = $this->rgf("groups", $col_2, "gr_name", "gr_id");
						if(($group_name)){
							$errors[] = "Cell <b>C".$count."</b> already exists <b>".$col_3."</b>";
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
							$col_3 = @strtoupper(trim(str_replace("'","\'",strip_tags($emapData[2]))));

							$matrix = $this->rgf("approval_matrix", $col_1, "ap_code", "ap_id");

							$db->insert("groups",["gr_date_added"=>$time, "gr_matrix"=>$matrix,"gr_name"=>$col_2,"gr_added_by"=>$user]);
						}
					}
					
					if(!$db->error()){
						FeedBack::success();
						FeedBack::refresh(3, return_url().'groups/all-groups');
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
	

	public function addGroupAction(){
		if(isset($_POST['submit'])){
			$groupname = $_POST['groupname'];
			$matrix = $_POST['matrix'];

			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($groupname)){
				$errors[]="Enter Group Name";
			}
			if($this->isThere("groups", ["gr_name"=>$groupname])){
				$errors[]="Group Name($groupname) already exists";
			}
			
			if(empty($errors)){
				$db->insert("groups",["gr_date_added"=>time(),"gr_matrix"=>$matrix,"gr_name"=>$groupname,"gr_added_by"=>user_id()]);
				
				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'groups/all-groups');
				}else{
					FeedBack::error($db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
		}
		?>
		<div class="col-md-6">
			<h3>&nbsp;</h3>
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Add Group Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Group Name<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" id="groupname" type="text" name="groupname" placeholder="Enter Group Name" value="<?php echo @$groupname; ?>">
									</div>
								</div>
								<div class="form-group">
									<label>Matrix<span class="must">*</span></label>
									<div class="form-line">
										<select name="matrix" id="matrix" class="select2" style="width:100%">
										<?php
										$db = new Db();
										$select = $db->select("SELECT * FROM approval_matrix ORDER BY ap_unit_code ASC");
										foreach($select as $row){
											extract($row);
											if(!$this->rgf("groups", $ap_id, "gr_matrix", "gr_id")){
												if($matrix == $ap_id)
													echo '<option selected="selected" value="'.$ap_id.'">'.$ap_unit_code.' - '.$ap_code.'</option>';
												else
													echo '<option value="'.$ap_id.'">'.$ap_unit_code.' - '.$ap_code.'</option>';
											}
										}
										?>
										</select>
										<script type="text/javascript">$('.select2').select2();</script>
									</div>
								</div>
								
								<button id="submitttBtn" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><br><span id="NewGroupStatus"></span>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	public function editgroupAction(){
		$id=portion(3);
		$id = $this->id;
		$db=new Db();
		$select=$db->select("select * from groups WHERE gr_id ='$id'");
		extract($select[0]);
		$groupname = $gr_name; 
		$matrix = $gr_matrix; 

		if(isset($_POST['submit'])){
			$groupname = $_POST['groupname'];
			$matrix = $_POST['matrix'];
			$time = time();
			$user = user_id();
			$db = new Db();
			
			$errors = array();
			if(empty($groupname)){
				$errors[]="Enter Group Name";
			}
			if($this->isThereEdit("groups", ["gr_name"=>$groupname, "gr_id"=>$id])){
				$errors[]="Group Name($groupname) already exists";
			}
			
			if(empty($errors)){
				$db->update("groups",["gr_matrix"=>$matrix,"gr_date_added"=>time(),"gr_name"=>$groupname,"gr_added_by"=>user_id()],["gr_id"=>$id]);
				
				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'groups/all-groups');
				}else{
					FeedBack::error($db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
		}
		?>
		<div class="col-md-6">
			<h3>&nbsp;</h3>
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Edit Group Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Group Name<span class="must">*</span></label>
									<input type="hidden" value="<?php echo $id; ?>" id="gr_id"/>
									<div class="form-line">
										<input class="form-control" type="text" id="groupname" name="groupname" placeholder="Enter Group Name" value="<?php echo @$groupname; ?>">
									</div>
								</div>
								<div class="form-group">
									<label>Matrix<span class="must">*</span></label>
									<div class="form-line">
										<select name="matrix" id="matrix" class="select3" style="width:100%">
										<?php
										$db = new Db();
										$select = $db->select("SELECT * FROM approval_matrix ORDER BY ap_unit_code ASC");
										foreach($select as $row){
											extract($row);
											//if(!$this->rgf("groups", $ap_id, "gr_matrix", "gr_id"))
											{
												if($matrix == $ap_id)
													echo '<option selected="selected" value="'.$ap_id.'">'.$ap_unit_code.' - '.$ap_code.'</option>';
												else
													echo '<option value="'.$ap_id.'">'.$ap_unit_code.' - '.$ap_code.'</option>';
											}
										}
										?>
										</select>
									</div>
								</div>
								<script type="text/javascript">$('.select3').select2();</script>
								<button id="editGroupbtn" onclick = "return confirm('Are you sure you want to save that Group?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><br><span id="editgroupstatus"></span>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

		public function AllgroupsAction(){
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
			echo '<button class="btn btn-xs btn-danger" id="eagleResetBtn" data-url="'.return_url().'requisition/approved-requisitions" type="button"><i class="fa fa-fw fa-refresh"></i> Reset</button>';

			echo '</div>';
			echo '</div>';

			echo '</div>';
			echo '<div class="code-eagle-table"></div>';
			echo '</div>';
			
			?>
		</div>

	    <script>
	        $(function(){
	            $(document).off('click').on('click', '.eagle-page-link, #eagleSearchBtn', function(){
	                $('#eagleActivePage').val($(this).attr('data-number'));
	                eagleSearch();
	            });
	            $(document).off('change').on('change', '#rowsPerPage', function(){
	                eagleSearch();
	            });
	            $(document).off('click').on('click', '.eagle-sort, .sort-asc, .sort-desc', function(e) {
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
		            url: $('#urlPath').val()+"ajax/_all-groups-table.php",
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
	
	public function sActionqw(){
	$access = new AccessRights();
	?>
		<div class="col-md-12">
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM groups");
			//echo$db->error();
			if($db->error()){
				echo $db->error();
			}else{
				//print_r($select);
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>Group Name</th>';
				echo '<th>Division/Location</th>';
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
					echo '<td>'.($gr_name).'</td>';
					echo '<td>'.$this->rgf("approval_matrix", $gr_matrix, "ap_id", "ap_unit_code").' - '.$this->rgf("approval_matrix", $gr_matrix, "ap_id", "ap_code").'</td>';
					
					echo '<td>'.FeedBack::date_fm($gr_date_added).'</td>';
					echo '<td>'.$this->full_name($gr_added_by).'</td>';
					echo '<td>';
							echo $this->action('edit','groups/edit-group/'.$gr_id, 'Edit');
							echo $this->action('delete','groups/delete-group/'.$gr_id, 'Delete');
					echo '</td>';

				}
				echo '</tbody>';
				
				echo '</table>';

				
			}
			
			?>
		</div>
		<?php
	}
}