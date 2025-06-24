<?php
class Users extends BeforeAndAfter{
	public $page = "USERS";
	//public $page2 = "HODS";
	public $id = 0;
	public function __construct(){
		$access = new AccessRights();

		$page = "USERS";
		//$page2 = "HODS";

		if(portion(2)=="add-user"){
			if(!$access->sectionAccess(user_id(), $page, 'A')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url());
			}
		}else if(portion(2) == "all-users"){
			if(!$access->sectionAccess(user_id(), $page, 'V')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				FeedBack::refresh(1, return_url());
			}
		}else if(portion(2) == "changed-password"||portion(2) == "logout"){
			//allowed
		}

		/*else{
			if(!$access->sectionAccess(user_id(), $page2, 'A')){
				echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				//FeedBack::refresh(1, return_url());
			}
		}*/
	}
	public function id($id){
		$this->id = $id;
	}
	public static function getLinks(){
		$page = "USERS";
		//$page1 = "HODS";
		$links = array(
			array(
				"link_name"=>"Add System User", 
				"link_address"=>"users/add-user",
				"link_icon"=>"fa-user-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"View Users", 
				"link_address"=>"users/all-users",
				"link_icon"=>"fa-users",
				"link_page"=>$page,
				"link_right"=>"V",
			),/*
			array(
				"link_name"=>"Add HOD", 
				"link_address"=>"users/add-hod",
				"link_icon"=>"fa-user-plus",
				"link_page"=>$page1,
				"link_right"=>"A",
			),*/
			// array(
			// 	"link_name"=>"View Managers", 
			// 	"link_address"=>"users/all-hods",
			// 	"link_icon"=>"fa-user-secret",
			// 	"link_page"=>$page1,
			// 	"link_right"=>"V",
			// ),
			array(
				"link_name"=>"Import Users", 
				"link_address"=>"users/import-user",
				"link_icon"=>"fa-upload",
				"link_page"=>$page,
				"link_right"=>"V",
			),
		);
		
		return $links;
	}
	
	public function logout(){
		echo '<h3>Logging Out. Please wait....</h3>';
		session_destroy();
		FeedBack::redirect(return_url());
		
		$db = new Db();

		

		$update = $db->update("sysuser", ["user_last_logged_in"=>time(), "user_online"=>0], ["user_id"=>user_id()]);
	}
	
	public function deleteHodAction(){
		$id = portion(3);		
		$this->deletor("hod", "hod_id",  $id, 'users/all-hods');
	}

	public function deleteUserAction(){
		$id = portion(3);
		$this->deletor("sysuser", "user_id",  $id, 'users/all-users');
	}
		
	public function AllUsersAction(){
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
			echo '<button class="btn btn-xs btn-danger" id="eagleResetBtn" data-url="'.return_url().'users/all-users" type="button"><i class="fa fa-fw fa-refresh"></i> Reset</button>';

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
		            url: $('#urlPath').val()+"ajax/users-table.php",
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
	
	public function ViewUserDetailsAction(){
		$id = portion(3);
		echo '<div class="col-lg-12"><a href="'.return_url().'users/edit-user/'.$id.'" class="btn btn-success btn-md pull-right"><i class="fa fa-fx fa-edit"></i> Edt User Details</a></div>';
		echo '<div class="clearfix"></div>';
		$this->detailsOfUsers($id);
		echo '<br/></br/>';
	}

	public function detailsOfUsers($id){
	$n = new Db();
		$nn = $n->select("SELECT * FROM sysuser WHERE user_id = '$id'");
		extract($nn[0]);

		$access = new AccessRights();
	?>
		<div class="col-md-12">
			<h3>User Account Details</h3>
			<?php 
			$db = new Db();
			$select = $db->select("SELECT * FROM sysuser WHERE user_id = '$id' ORDER BY user_surname ASC ");
			
			if(!$select){
				echo $db->error();
			}else{
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr valign="bottom">';
				echo '<th width="30px">No.</th>';
				echo '<th width="150px">Name</th>';
				echo '<th>Value</th>';
				
				echo '</tr>';
				echo '</thead>';
				
				$i=1;
				echo '<tbody>';
				foreach($select as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>Account Status</td>';	
					echo '<td>'.(($user_active)?"Active":"Locked").'</td>';				
					echo '</tr>';

					// echo '<tr>';
					// echo '<td><center>'.($i++).'.</center></td>';
					// echo '<td>Account Created On</td>';	
					// echo '<td>'.Feedback::date_fm($user_date_added).'</td>';				
					// echo '</tr>';

					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>Last Logged in</td>';	
					echo '<td>'.Feedback::date_fm($user_last_logged_in).'</td>';				
					echo '</tr>';

					// echo '<tr>';
					// echo '<td><center>'.($i++).'.</center></td>';
					// echo '<td>Password Last changed</td>';	
					// echo '<td>'.Feedback::date_fm($user_last_changed).'</td>';				
					// echo '</tr>';


					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>Name</td>';	
					echo '<td>'.($user_surname.' '.$user_othername).'</td>';				
					echo '</tr>';
					
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>Username</td>';	
					echo '<td>'.($user_name).'</td>';				
					echo '</tr>';
					
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>Email</td>';	
					echo '<td>'.($user_email).'</td>';				
					echo '</tr>';
					
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>User Role:</td>';	
					echo '<td>'.$this->rgf("user_role", $user_role, "ur_id", "ur_name").'</td>';				
					echo '</tr>';
					
					// echo '<tr>';
					// echo '<td><center>'.($i++).'.</center></td>';
					// echo '<td>Gender:</td>';	
					// echo '<td>';
					// echo	($user_gender)?"Male":"Female";
					// echo '</td>';				
					// echo '</tr>';
					
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>Online/Offline</td>';	
					echo '<td>'.(($user_online)?"Online":"Offline").'</td>';				
					echo '</tr>';
					
					

					echo '<tr><td colspan="3"></td></tr>';				
				}
				echo '</tbody>';
				
				echo '</table>';

			}
			
			?>
		</div>
		<?php
	}
	public function importUserAction(){

				$db = new Db();
		$time = time();
		$user = user_id();
		$errors = array();
		if (isset($_POST['uploaddata'])) { 
			$filename=$_FILES["uploadFile"]["tmp_name"];
			if($_FILES["uploadFile"]["size"] > 0){
				$count = 0;
				$valid_name = "SURNAME";
				$file = fopen($filename, "r");
				while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
						$count++;
					 $col_1 = @(trim(str_replace("'","\'",strip_tags($emapData[0]))));
					 $col_2 = @(trim(str_replace("'","\'",strip_tags($emapData[1]))));
					 $col_3 = @(trim(str_replace("'","\'",strip_tags($emapData[2]))));
					 $col_4 = @(trim(str_replace("'","\'",strip_tags($emapData[3]))));
					 $col_5 = @(trim(str_replace("'","\'",strip_tags($emapData[4]))));
					 $col_6 = @(trim(str_replace("'","\'",strip_tags($emapData[5]))));
					 $col_7 = @(trim(str_replace("'","\'",strip_tags($emapData[6]))));
					 $col_8 = @(trim(str_replace("'","\'",strip_tags($emapData[7]))));
					 $col_9 = @(trim(str_replace("'","\'",strip_tags($emapData[8]))));
					 
					if($count == 1){
						//checking template
						if($col_1 != $valid_name){
							$errors[] = "Invalid Template";
						}
					}else{
						//checking if there is not empty field
						if(empty($col_1)){
							$errors[] = "Cell <b>A".$count."</b> should not be empty";
						}
						if(empty($col_2)){
							//$errors[] = "Cell <b>B".$count."</b> should not be empty";
						}
						if(empty($col_3)){
							$errors[] = "Cell <b>C".$count."</b> should not be empty";
						}
						if(empty($col_4)){
							$errors[] = "Cell <b>D".$count."</b> should not be empty";
						}
						if(empty($col_5)){
							//$errors[] = "Cell <b>E".$count."</b> should not be empty";
						}
						if(empty($col_6)){
							$errors[] = "Cell <b>E".$count."</b> should not be empty";
						}
						if(empty($col_7)){
							$errors[] = "Cell <b>F".$count."</b> should not be empty";
						}
						if(empty($col_8)){
							$errors[] = "Cell <b>G".$count."</b> should not be empty";
						}
						if(empty($col_9)){
							$errors[] = "Cell <b>H".$count."</b> should not be empty";
						}
						
						//checking duplicates
						//$user_id = $this->rgf("sysuser", $col_5, "user_email", "user_id");
						if($user_id){
							//$errors[] = "The <b>Email</b> in Cell <b>E".$count."</b> already exists";
						}

						$user_id2 = $this->rgf("sysuser", $col_3, "user_name", "user_id");
						if($user_id2){
							$errors[] = "The <b>PF No.</b> in Cell <b>C".$count."</b> already exists";
						}
						//exists
						$col_7 = $this->rgf("branch", $col_7, "branch_name", "branch_id");
						$col_8 = $this->rgf("department", $col_8, "dept_name", "dept_id");
						$col_9 = $this->rgf("designation", $col_9, "designation_name", "designation_id");

						if(!$col_7){
							//$errors[] = "The <b>Branch</b> in Cell <b>F".$count."</b> does not exist <B>$col_7</B>";
						}
						if(!$col_8){
							//$errors[] = "The <b>Department</b> in Cell <b>G".$count."</b> does not exist <B> $col_8</B>";
						}
						if(!$col_9){
							//$errors[] = "The <b>Designation</b> in Cell <b>H".$count."</b> does not exist <B>$col_9</B>";
						}

						$user_id3 = $this->rgf("user_role", $col_4, "ur_name", "ur_id");
						if(empty($user_id3)){				
							
							$rows = array();
							$select = $db->select("SELECT ur_name FROM user_role");
							foreach($select as $row){
								extract($row);
								$rows[] = $ur_name;
							}
							$errors[] = "The <b>User Role/System Role</b> in Cell <b>D".$count."</b> does not exist. It should be one of these: <b>".implode(',', $rows).'</b>';
						}
					}
										
				}
				fclose($file);
				$count = 0;
				if(empty($errors)){
					$db = new Db();

					$file = fopen($filename, "r");
					while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE){
						$count++;

						if($count > 1){
					$col_1 = @(trim(str_replace("'","\'",strip_tags($emapData[0]))));
					$col_2 = @(trim(str_replace("'","\'",strip_tags($emapData[1]))));
					$col_3 = @(trim(str_replace("'","\'",strip_tags($emapData[2]))));
					$col_4 = @(trim(str_replace("'","\'",strip_tags($emapData[3]))));
					$col_5 = @(trim(str_replace("'","\'",strip_tags($emapData[4]))));
					$col_6 = @(trim(str_replace("'","\'",strip_tags($emapData[5]))));
					$col_7 = @(trim(str_replace("'","\'",strip_tags($emapData[6]))));
					$col_8 = @(trim(str_replace("'","\'",strip_tags($emapData[7]))));
					$col_9 = @(trim(str_replace("'","\'",strip_tags($emapData[8]))));
					
					$password = Feedback::password_generator();

					$col_4 = $this->rgf("user_role", $col_4, "ur_name", "ur_id");

					$col_7 = $this->rgf("branch", $col_7, "branch_name", "branch_id");
					$col_8 = $this->rgf("department", $col_8, "dept_name", "dept_id");
					$col_9 = $this->rgf("designation", $col_9, "designation_name", "designation_id");

					$name = explode(' ',ucwords(strtolower($col_1)));

					if($col_5=="#N/A"||$col_5=="")
						$col_5 = implode('.', $name)."@centenarybank.co.ug";
					
					$pf = "";
					foreach($name as $l){
						$pf .= substr($l, 0,1);
					}
					//$pf .= ''.str_pad($col_3, 5, "0", STR_PAD_LEFT);
					//$col = $col_3;
					//$col_3 = $pf;

					$db->insert("sysuser", [
						"check_number"=>$col_3,		
						"user_surname"=>$col_1,
						"user_othername"=>$col_2,
						"user_email"=>$col_5,
						"user_gender"=>$col_6,
						"user_role"=>$col_4,
						"user_name"=>$col_3, 
						"user_status"=>1,		
						"user_password"=>$this->penc('password'),//$this->penc($password),
						"user_date_added"=>$time,
						"user_added_by"=>$user_id,
						"user_forgot_password"=>1,
						"user_active"=>1,
						"user_branch_id"=>$col_7,
						"user_department_id"=>$col_8,
						"user_designation"=>$col_9,
						]);

						if(empty($db->error())){
							// $msg = array();
			    //             $msg[] = "Hello $col_2, ";
			    //             $msg[] = "";
			    //             $msg[] = "Your Account has been successfully created by :".$this->full_name(user_id());
			    //             $msg[] = "";
			    //             $msg[] = "Username/PF No.: $col_3";
			                
			    //             if(!empty($password)){
			    //             	 $msg[] = "Password: $password";
			    //         	}
			               
			    //             $mssg[] = "";
			    //             $message[] = "Thank You.";

			    //             $to = $col_5;
			    //             $subject = "ACCOUNT CREDENTIALS";
			    //             $message = implode("\r \n <br/>",$msg);
			    //             Feedback::sendmail($to,$subject,$message,$name);
			            }
			       	 }
					}
					
					if(empty($db->error())){
						FeedBack::success("Successfully imported. Records: <b>".number_format($count-1).'</b>');
						//FeedBack::refresh(3, return_url().'users/all-users');
					}else{
						FeedBack::errors($errors);
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
				<a href="<?php echo return_url(); ?>import file/SystemUsers.csv">Download Template</a>
			</div>
		</div>


<?php
	}

	public function addUserAction(){


		$msg_pass = "Password should be at least 7 characters in length and should include at least an upper case letter(A-Z), lower case letter(a-z), number(0-9), and special character.(@ ? / # $ %) ";
		$db = new Db();
		if(isset($_POST['submit'])){
			
			$check_number = addslashes(ucwords(strtolower($_POST['check_number'])));	
			$surname = addslashes(ucwords(strtolower($_POST['surname'])));			
			$othername = addslashes(ucwords(strtolower($_POST['othername'])));				
			$email = addslashes($_POST['email']);				
			//$telephone = addslashes($_POST['telephone']);

			//$user_department_id = addslashes($_POST['user_department_id']);	
			//$user_branch_id = addslashes($_POST['user_branch_id']);

			
			$user_gender = addslashes($_POST['user_gender']);
			//$user_designation = addslashes($_POST['designation']);
			
			$user_role = addslashes($_POST['user_role']);

			$username = $check_number;

			$user_id = user_id();
			$time = time();
			
			$errors = array();

			if(empty($check_number)){
				$errors[] = "Please Enter PF number";
			}
			
			// if(empty($othername)){
			// 	$errors[] = "Please fill your othername";
			// }
			// if(empty($user_designation)){
			// 	$errors[] = "Select User Role";
			// }
			if(empty($email)){
				$errors[] = "Please Enter your Email Address";
			}
			if(empty($user_role)){
				$errors[] = "Please Select User Role";
			}
			// if(empty($telephone)){
			// 	$errors[] = "Enter telephone number";
			// }
			
			// if(empty($user_branch_id)){
			// 	$errors[] = "select branch";
			// }

			if(empty($username)){
				$errors[] = "Enter Username";
			}
					
			
			if($this->isThere("sysuser", ["user_name"=>$username])){
				$errors[] = "Username $username already exists";
			}
			
			if($this->isThere("sysuser", ["check_number"=>$check_number])){
				$errors[] = "PF number $check_number already exists";
			}
				

			if(empty($errors)){
				$password = Feedback::password_generator();

				$insert = $db->insert("sysuser", [
				"user_name"=>$username, 
				"user_surname"=>$surname,
				"user_othername"=>$othername,
				"user_status"=>1,
				//"user_designation"=>$user_designation,
				//"user_branch_id"=>$user_branch_id,
				"user_email"=>$email,
				"user_telephone"=>$telephone,
				"user_gender"=>$user_gender,
				//"user_section_id"=>$user_section_id,
				//"user_department_id"=>$user_department_id,
				"user_password"=>$this->penc($password),
				"user_date_added"=>$time,
				"user_added_by"=>$user_id,
				"user_role"=>$user_role,
				"user_forgot_password"=>1,
				"user_active"=>1,
				"check_number"=>$check_number,
				]);

				
				echo $db->error();
				if($insert){

					$msg = array();
	                $msg[] = "Hello $othername, ";
	                $msg[] = "";
	                $msg[] = "Your Account has been successfully created by :".$this->full_name(user_id());
	                $msg[] = "";
	                $msg[] = "Username: $username";
	                
	                if(!empty($password)){
	                	 $msg[] = "Password: $password";
	            	}
	               
	                $mssg[] = "";
	                $message[] = "Thank You.";

	                $to = $email;
	                $subject = "ACCOUNT CREDENTIALS";
	                $message = implode("\r \n <br/>", $msg);
	                Feedback::sendmail($to,$subject,$message,$name);

					FeedBack::success();
					FeedBack::refresh();
				}else{
					FeedBack::error('Not Saved, '.$db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
					
		}	
		
		//echo $this->branch_name(3);
			
		?>
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Add User Form
				</div>
				<div class="panel-body">
					<div class="row">

						<form role="form" action="" method="post">
							<div class="col-md-12">
								<div id="must">All field with asterisk(*) are mandatory.</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-2">
								<div class="form-group">
									<label>PF no.<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" id="check_number" type="text" autocomplete="off" name="check_number" placeholder="Enter PF no." value="<?php echo @$check_number; ?>">
									</div>
								</div>
							</div>

							<div class="col-lg-3">
								<div class="form-group">
									<label>User Role<span class="must">*</span></label>
									<div class="form-line">
										<select  data-show-subtext="true" data-live-search="true" id="user_role" style="width:100%" name="user_role" >
											<?php
												

											$db = new Db();
											$select = $db->select("SELECT ur_id, ur_name FROM user_role ORDER BY ur_name ASC");										
											
											if($db->num_rows()){												
												foreach($select as $row){
												extract($row);	
													if($user_role == $ur_id){
													 	echo '<option selected="selected" data-subtext="" value="'.$ur_id.'">'.$ur_name.'</option>';
													}else{
														echo '<option data-subtext="" value="'.$ur_id.'">'.$ur_name.'</option>';
													}													
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Surname<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" id="surname" autocomplete="off" name="surname" value="<?php echo @$surname; ?>">
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Othername(s)<span class="must"></span></label>
									<div class="form-line">
										<input class="form-control" type="text" autocomplete="off" id="othername" name="othername" value="<?php echo @$othername; ?>">
									</div>
								</div>
							</div>
							<div class="col-lg-3" style="display: none;">
								<div class="form-group">
									<label>Telephone Phone<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="number" autocomplete="off" id="telephone" name="telephone" value="<?php echo @$telephone; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-4">
								<div class="form-group">
									<label>Email Address<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="email" id="email" autocomplete="off" name="email" value="<?php echo @$email; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-2">
								<div class="form-group">
									<label>Gender<span class="must"></span></label>
									<div class="form-line">
										<select name="user_gender" id="user_gender">
											<?php 
											if($user_gender ==1)
												echo '<option selected="selected" value="1">Male</option>';
											else
												echo '<option value="1">Male</option>';

											if($user_gender ==0)
												echo '<option selected="selected" value="0">Female</option>';
											else
												echo '<option value="0">Female</option>';
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
													
							<div class="col-lg-4" style="display:none;" title="<?php echo $msg_pass; ?>" class="form-control" type="password">
								<div class="form-group">
									<label>Password<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="password" id="password" name="password" value="<?php echo @$password; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-4" style="display:none;">
								<div class="form-group">
									<label>Confirm Password<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="password" name="cpassword" value="<?php echo @$cpassword; ?>">
									</div>
								</div>
							</div>	
							<div class="clearfix"></div>
							<div class="col-lg-2" style="display:none;">
								<div class="form-group">
									<label>Branch<span class="must">*</span></label>
									<div class="form-line">
										<select name="user_branch_id" id="office" onchange="officeCode12('territory12');" data-show-subtext="true" data-live-search="true" style="width:100%">
											<option data-subtext="" value="">--Select--</option>
											<?php
											$db = new Db();
											$select = $db->select("SELECT branch_id, branch_name FROM branch WHERE branch_status = 1 ORDER BY branch_name ASC");
											foreach($select as $row){
												extract($row);
												if($branch_id == $user_branch_id)
													echo '<option data-subtext="'.$branch_name.'" value="'.$branch_id.'" selected="selected">'.$branch_name.'</option>';
												else
													echo '<option data-subtext="'.$branch_name.'" value="'.$branch_id.'">'.$branch_name.'</option>';
											}
											?>
										</select>
									</div>
								</div>
							</div>
							
									<div class="col-lg-3" style="display:none">
										<div class="form-group">
											<label>Department<span class="must">*</span></label>
											<div class="form-line">
												<select data-show-subtext="true" data-live-search="true" style="width:100%" name="user_department_id" id="department" onchange=" officeCode('department'); officeCodeRole('departmentRoles'); ">
													
													<?php
													$db = new Db();
													$select = $db->select("SELECT dept_id, dept_name FROM department WHERE dept_name IS NOT NULL AND dept_status = 1 ORDER BY dept_name ASC");
													foreach($select as $row){
														extract($row);
														if($dept_id == $user_department_id)
															echo '<option data-subtext="" value="'.$dept_id.'" selected="selected">'.$dept_name.'</option>';
														else
															echo '<option data-subtext="" value="'.$dept_id.'">'.$dept_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
									</div>							
									
							
							<div class="col-lg-3" style="display:none;">
								<div class="form-group">
									<label>Designation<span class="must">*</span></label>
									<div class="form-line">
										<select data-show-subtext="true" data-live-search="true" style="width:100%" name="designation" id="designation" onclick="return designation();">
											<?php
																						

											$db = new Db();

											$select = $db->select("SELECT designation_id, designation_name FROM designation ORDER BY designation_name ASC");
											
											if($db->num_rows()){												
												foreach($select as $row){
												extract($row);	
													if($user_designation1212 == $designation_id){
													 	echo '<option selected="selected" data-subtext="" value="'.$designation_id.'">'.$designation_name.'</option>';
													}else{
														echo '<option data-subtext="" value="'.$designation_id.'">'.$designation_name.'</option>';
													}													
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>					
								
							<div class="clearfix"></div>
							<div class="col-lg-12">
								<button type="button" id="addUserBtn" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><span id="addUserStatus"></span>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
		//echo 'ksdflsdfl sldf sdf';
		echo '<table border="1" id="table" style="display:none;">';
		echo '<tr>';
		echo '<th>No.</th>';
		echo '<th>PF No.</th>';
		echo '<th>Surname</th>';
		echo '<th>Other name</th>';
		echo '<th>Telephone</th>';
		echo '<th>Email</th>';
		echo '<th>Gender</th>';
		echo '<th>Branch</th>';
		echo '<th>User Role</th>';
		echo '</tr>';
		$v = 1;
		for($j=1; $j<4; $j++){
			for($i=1; $i<4; $i++){
				echo '<tr>';
				echo '<td>'.($v++).'</td>';
				echo '<td>00'.($v-1).'</td>';
				echo '<td>User '.$i.'</td>';
				echo '<td>B'.$j.'</td>';
				echo '<td>0788229210</td>';
				echo '<td>josemusiitwa@gmail.com</td>';
				if($i==2)
				echo '<td>Female</td>';
				else
				echo '<td>Male</td>';

				echo '<td>Branch '.$j.'</td>';
				if($i==1)
				echo '<td>Branch/Dempartment Approver</td>';
				else
				echo '<td>User</td>';
				echo '</tr>';
			}
		}
		echo '</table>';

	}
	
	
	public function editUserAction(){
		$id = portion(3);
		$id = $this->id;
		$db = new Db();
		$select = $db->select("SELECT * FROM sysuser WHERE user_id = '$id'");
		extract($select[0]);

		$check_number = $check_number;	
		$surname = $user_surname;			
		$othername = $user_othername;				
		$email = $user_email;				
		$telephone = $user_telephone;

		$user_department_id = $user_department_id;	
		$user_branch_id = $user_branch_id;

		
		//$user_gender = addslashes($_POST['user_gender']);
		$user_designation = $user_designation_id;
		
		//$user_role = addslashes($_POST['user_role']);

		$msg_pass = "Password should be at least 7 characters in length and should include at least an upper case letter(A-Z), lower case letter(a-z), number(0-9), and special character.(@ ? / # $ %) ";
		$db = new Db();
		if(isset($_POST['submit'])){
			$change_password = $_POST['change_password'];
			$status = $_POST['status'];
			$check_number = addslashes(ucwords(strtolower($_POST['check_number'])));	
			$surname = addslashes(ucwords(strtolower($_POST['surname'])));			
			$othername = addslashes(ucwords(strtolower($_POST['othername'])));				
			$email = addslashes($_POST['email']);				
			$telephone = addslashes($_POST['telephone']);

			$user_department_id = addslashes($_POST['user_department_id']);	
			$user_branch_id = addslashes($_POST['user_branch_id']);

			
			$user_gender = addslashes($_POST['user_gender']);
			$user_designation = addslashes($_POST['designation']);
			
			$user_role = addslashes($_POST['user_role']);

			$username = $check_number;

			$user_id = user_id();
			$time = time();
			
			$errors = array();

			if(empty($check_number)){
				$errors[] = "Please Enter PF number";
			}
			
			// if(empty($othername)){
			// 	$errors[] = "Please fill your othername";
			// }
			if(empty($email)){
				$errors[] = "Please Enter your Email Address";
			}
			if(empty($user_role)){
				$errors[] = "Please Select User Role";
			}
			
			

			if(empty($username)){
				$errors[] = "Enter Username";
			}
					
			
			if($this->isThereEdit("sysuser", ["user_name"=>$username, "user_id"=>$id])){
				$errors[] = "Username $username already exists";
			}
			
			if($this->isThereEdit("sysuser", ["check_number"=>$check_number, "user_id"=>$id])){
				$errors[] = "PF number $check_number already exists";
			}
				

			if(empty($errors)){
				$password = Feedback::password_generator();

				if($change_password){
					$user_password = $this->penc($password);
					$fp = 1;
				}else{
					$fp = 0;
				}

				$insert = $db->update("sysuser", [
				"user_name"=>$username, 
				"user_surname"=>$surname,
				"user_othername"=>$othername,
				"user_status"=>1,
				"user_designation"=>$user_designation,
				"user_branch_id"=>$user_branch_id,
				"user_email"=>$email,
				"user_telephone"=>$telephone,
				"user_gender"=>$user_gender,
				"user_section_id"=>$user_section_id,
				"user_department_id"=>$user_department_id,
				"user_password"=>$user_password,
				"user_date_added"=>$time,
				"user_added_by"=>$user_id,
				"user_role"=>$user_role,
				"user_forgot_password"=>$fp,
				"user_active"=>$status,
				"check_number"=>$check_number,
				],["user_id"=>$id]);

				
				echo $db->error();
				if($insert){
					if($change_password){
						$msg = array();
		                $msg[] = "Hello $othername, ";
		                $msg[] = "";
		                $msg[] = "Your Account has been successfully created by :".$this->full_name(user_id());
		                $msg[] = "";
		                $msg[] = "Username: $username";
		                
		                if(!empty($password)){
		                	 $msg[] = "Password: $password";
		            	}
		               
		                $mssg[] = "";
		                $message[] = "Thank You.";

		                $to = $email;
		                $subject = "ACCOUNT CREDENTIALS";
		                $message = implode("\r \n <br/>", $msg);
		                Feedback::sendmail($to,$subject,$message,$name);
	            	}

					FeedBack::success();
					FeedBack::refresh();
				}else{
					FeedBack::error('Not Saved, '.$db->error());
				}
			}else{
				FeedBack::errors($errors);
			}
					
		}	
		
		//echo $this->branch_name(3);
			
		?>
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Edit User Form
				</div>
				<div class="panel-body">
					<div class="row">
						<input type="hidden" value="<?php echo $id; ?>" id="user_id"/>
						<!-- <form role="form" action="" method="post"> -->
							<div class="col-md-12">
								<div id="must">All field with asterisk(*) are mandatory.</div>
							</div>
							
							<div class="clearfix"></div>
							<div class="col-lg-2">
								<div class="form-group">
									<label>PF no.<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="text" id="check_number" autocomplete="off" name="check_number" placeholder="Enter PF no." value="<?php echo @$check_number; ?>">
									</div>
								</div>
							</div>

							<div class="col-lg-3">
								<div class="form-group">
									<label>User Role<span class="must">*</span></label>
									<div class="form-line">
										<select  data-show-subtext="true" data-live-search="true" id="user_role" style="width:100%" name="user_role" id="">
											<?php
												

											$db = new Db();
											$select = $db->select("SELECT ur_id, ur_name FROM user_role ORDER BY ur_name ASC");										
											
											if($db->num_rows()){												
												foreach($select as $row){
												extract($row);	
													if($user_role == $ur_id){
													 	echo '<option selected="selected" data-subtext="" value="'.$ur_id.'">'.$ur_name.'</option>';
													}else{
														echo '<option data-subtext="" value="'.$ur_id.'">'.$ur_name.'</option>';
													}													
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>

							<div class="col-lg-2">
								<div class="form-group">
									<label>Status<span class="must"></span></label>
									<div class="form-line">
										<select id="status" name="status">
											<?php 
											if(empty($status)) $status = 1;
											if($user_active == 0)
												echo '<option selected="selected" value="0">Lock</option>';
											else
												echo '<option value="0">Lock</option>';

											if($user_active == 1)
												echo '<option selected="selected" value="1">Active</option>';
											else
												echo '<option value="1">Active</option>';
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Change Password<span class="must"></span></label>
									<div class="form-line">
										<select id="change_password" name="change_password">
											<?php 
											if($change_password ==0)
												echo '<option selected="selected" value="0">No, Retain Password</option>';
											else
												echo '<option value="0">No, Retain Password</option>';

											if($change_password ==1)
												echo '<option selected="selected" value="1">Yes, Change Password</option>';
											else
												echo '<option value="1">Yes, Change Password</option>';
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Surname<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" id="surname" type="text" autocomplete="off" name="surname" value="<?php echo @$surname; ?>">
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label>Othername(s)<span class="must"></span></label>
									<div class="form-line">
										<input class="form-control" id="othername" type="text" autocomplete="off" name="othername" value="<?php echo @$othername; ?>">
									</div>
								</div>
							</div>
							<div class="col-lg-3" style="display: none;">
								<div class="form-group">
									<label>Telephone Phone<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control"   type="number" id="telephone" autocomplete="off" name="telephone" value="<?php echo @$telephone; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-4">
								<div class="form-group">
									<label>Email Address<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" id="email" type="email" autocomplete="off" name="email" value="<?php echo @$email; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-2">
								<div class="form-group">
									<label>Gender<span class="must"></span></label>
									<div class="form-line">
										<select id="user_gender" name="user_gender">
											<?php 
											if($user_gender ==1)
												echo '<option selected="selected" value="1">Male</option>';
											else
												echo '<option value="1">Male</option>';

											if($user_gender ==0)
												echo '<option selected="selected" value="0">Female</option>';
											else
												echo '<option value="0">Female</option>';
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
													
							<div class="col-lg-4" style="display:none;" title="<?php echo $msg_pass; ?>" class="form-control" type="password">
								<div class="form-group">
									<label>Password<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="password" name="password" value="<?php echo @$password; ?>">
									</div>
								</div>
							</div>							
							<div class="col-lg-4" style="display:none;">
								<div class="form-group">
									<label>Confirm Password<span class="must">*</span></label>
									<div class="form-line">
										<input class="form-control" type="password" name="cpassword" value="<?php echo @$cpassword; ?>">
									</div>
								</div>
							</div>	
							<div class="clearfix"></div>
							<div class="col-lg-2" style="display:none;">
								<div class="form-group">
									<label>Branch<span class="must">*</span></label>
									<div class="form-line">
										<select name="user_branch_id" id="office" onchange="officeCode12('territory12');" data-show-subtext="true" data-live-search="true" style="width:100%">
											<option data-subtext="" value="">--Select--</option>
											<?php
											$db = new Db();
											$select = $db->select("SELECT branch_id, branch_name FROM branch WHERE branch_status = 1 ORDER BY branch_name ASC");
											foreach($select as $row){
												extract($row);
												if($branch_id == $user_branch_id)
													echo '<option data-subtext="'.$branch_name.'" value="'.$branch_id.'" selected="selected">'.$branch_name.'</option>';
												else
													echo '<option data-subtext="'.$branch_name.'" value="'.$branch_id.'">'.$branch_name.'</option>';
											}
											?>
										</select>
									</div>
								</div>
							</div>
							
									<div class="col-lg-3" style="display:none">
										<div class="form-group">
											<label>Department<span class="must">*</span></label>
											<div class="form-line">
												<select data-show-subtext="true" data-live-search="true" style="width:100%" name="user_department_id" id="department" onchange=" officeCode('department'); officeCodeRole('departmentRoles'); ">
													
													<?php
													$db = new Db();
													$select = $db->select("SELECT dept_id, dept_name FROM department WHERE dept_name IS NOT NULL AND dept_status = 1 ORDER BY dept_name ASC");
													foreach($select as $row){
														extract($row);
														if($dept_id == $user_department_id)
															echo '<option data-subtext="" value="'.$dept_id.'" selected="selected">'.$dept_name.'</option>';
														else
															echo '<option data-subtext="" value="'.$dept_id.'">'.$dept_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
									</div>							
									
							
							<div class="col-lg-3" style="display:none;">
								<div class="form-group">
									<label>Designation<span class="must">*</span></label>
									<div class="form-line">
										<select data-show-subtext="true" data-live-search="true" style="width:100%" name="designation" id="designation" onclick="return designation();">
											<?php
																						

											$db = new Db();

											$select = $db->select("SELECT designation_id, designation_name FROM designation ORDER BY designation_name ASC");
											
											if($db->num_rows()){												
												foreach($select as $row){
												extract($row);	
													if($user_designation1212 == $designation_id){
													 	echo '<option selected="selected" data-subtext="" value="'.$designation_id.'">'.$designation_name.'</option>';
													}else{
														echo '<option data-subtext="" value="'.$designation_id.'">'.$designation_name.'</option>';
													}													
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>	
							<div class="clearfix"></div>
							<div class="col-lg-12">
								<button type="button" id="editUserBtn" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button><span id="editStatus"></span>
							</div>
						<!-- </form> -->
					</div>
				</div>
			</div>
		</div>
		<?php
		

	}
	
	
	public function AddHodAction(){
		if(isset($_POST['submit'])){
		
			$section = $_POST['section_name'];
			$department_id = $_POST['department_id'];
			$hod_user_id = $_POST['hod_user_id'];
			$time = time();
			$user = user_id();
			$db = new Db();
			$errors = array();

			if(empty($department_id)){
				$errors[] = "Select department name";
			}
			if(empty($hod_user_id)){
				$errors[] = "Select user name";
			}
			
			if($this->isThere("hod", ["hod_dept_id"=>$department_id])){
				$errors[] = "Product Department name ()$department_id already exists";
			}
			
			if($this->isThere("hod", ["hod_user_id"=>$hod_user_id])){
				$errors[] = "name ()$hod_user_id already exists";
			}
			if(empty($errors)){
				$x = $db->insert("hod",["hod_date_added"=>$time,"hod_dept_id"=>$department_id, "hod_user_id"=>$hod_user_id,"hod_added_by"=>$user,"hod_date_added"=>$time]);
				echo $db->error();
				if($x){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'users/all-hods');
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
					Add HOD Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Department:<span class="must">*</span></label>
									<div class="form-line">
										<select name="department_id">
											<option value="">--Select--</option>
											<?php
											$db = new Db();
											$dept_ids = array();
											$user_ids = array();
											$select = $db->select("SELECT * FROM hod");
											foreach($select as $row){
												extract($row);
												$dept_ids[] = $hod_dept_id;
												$user_ids[] = $hod_user_id;
											}
											
											$db = new Db();
											$select = $db->select("SELECT dept_id, dept_name FROM department ORDER BY dept_name ASC");
											foreach($select as $row){
												extract($row);
												if(!in_array($dept_id, $dept_ids)){
													if($dept_id == $department_id)
														echo '<option value="'.$dept_id.'" selected="selected">'.$dept_name.'</option>';
													else
														echo '<option value="'.$dept_id.'">'.$dept_name.'</option>';
												}
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
								
									<label>Name:<span class="must">*</span></label>
									<div class="form-line">
										<select name="hod_user_id">
											<option value="">--Select--</option>
											<?php
											$db = new Db();
											$select = $db->select("SELECT user_surname, user_othername, user_id FROM sysuser WHERE user_designation = 15 ORDER BY user_surname ASC");
											foreach($select as $row){
												extract($row);
												if(!in_array($user_id, $user_ids)){
													if($hod_user_id == $user_id)
														echo '<option value="'.$user_id.'" selected="selected">'.$user_surname.' '.$user_othername.'</option>';
													else
														echo '<option value="'.$user_id.'">'.$user_surname.' '.$user_othername.' </option>';
												}
											}
											?>
										</select>
									</div>
								</div>
								
								<button type="submit" onclick="return confirm('Are you sure you want to save.');" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public function EditHodAction(){
		$id = portion(3);
		$db = new Db();
		$select = $db->select("SELECT * FROM hod WHERE hod_id = '$id'");
		if(isset($_POST['submit'])){
		
			$section = $_POST['section_name'];
			$department_id = $_POST['department_id'];
			$hod_user_id = $_POST['hod_user_id'];
			$time = time();
			$user = user_id();
			$db = new Db();
			$errors = array();

			if(empty($department_id)){
				$errors[] = "Select department name";
			}
			if(empty($hod_user_id)){
				$errors[] = "Select user name";
			}
			
			if($this->isThere("hod", ["hod_dept_id"=>$department_id])){
				$errors[] = "Product Department name ()$department_id already exists";
			}
			
			if($this->isThere("hod", ["hod_user_id"=>$hod_user_id])){
				$errors[] = "name ()$hod_user_id already exists";
			}
			if(empty($errors)){
				$x = $db->update("hod",["hod_date_added"=>$time,"hod_dept_id"=>$department_id, "hod_user_id"=>$hod_user_id,"hod_added_by"=>$user,"hod_date_added"=>$time], ["hod_id"=>$id]);
				echo $db->error();
				if($x){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'users/all-hods');
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
					Edit HOD Form
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<form role="form" action="" method="post">
								
								<div class="form-group">
									<div id="must">All field with asterisk(*) are mandatory.</div>
									<label>Department:<span class="must">*</span></label>
									<div class="form-line">
										<select name="department_id">
											<option value="">--Select--</option>
											<?php
											$db = new Db();
											$dept_ids = array();
											$user_ids = array();
											$select = $db->select("SELECT * FROM hod");
											foreach($select as $row){
												extract($row);
												$dept_ids[] = $hod_dept_id;
												$user_ids[] = $hod_user_id;
											}
											
											$db = new Db();
											$select = $db->select("SELECT dept_id, dept_name FROM department ORDER BY dept_name ASC");
											foreach($select as $row){
												extract($row);
												if(!in_array($dept_id, $dept_ids)){
													if($dept_id == $department_id)
														echo '<option value="'.$dept_id.'" selected="selected">'.$dept_name.'</option>';
													else
														echo '<option value="'.$dept_id.'">'.$dept_name.'</option>';
												}
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
								
									<label>Name:<span class="must">*</span></label>
									<div class="form-line">
										<select name="hod_user_id">
											<option value="">--Select--</option>
											<?php
											$db = new Db();
											$select = $db->select("SELECT user_surname, user_othername, user_id FROM sysuser WHERE user_designation = 15 ORDER BY user_surname ASC");
											foreach($select as $row){
												extract($row);
												if(!in_array($user_id, $user_ids)){
													if($hod_user_id == $user_id)
														echo '<option value="'.$user_id.'" selected="selected">'.$user_surname.' '.$user_othername.'</option>';
													else
														echo '<option value="'.$user_id.'">'.$user_surname.' '.$user_othername.' </option>';
												}
											}
											?>
										</select>
									</div>
								</div>
								
								<button onclick = "return confirm('Are you sure you want to save ?');" type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public function AllHodsAction(){
	
	?>
		<div class="col-md-12">
			<?php 
			$db = new Db();
			$hod = static_hod_id();
			//$md = static_md();
			$select = $db->select("SELECT * FROM sysuser WHERE user_role = '$hod' OR user_role = '$md'  ORDER BY user_surname ASC");
			
			if(!$select){
				$db->error();
			}else{
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" id="table">';
				echo '<thead>';
				echo '<tr>';
				echo '<th width="30px">No.</th>';
				echo '<th>Branch</th>';
				echo '<th>Department</th>';
				echo '<th>Head of Department</th>';
				echo '<th>Total Staff</th>';
				echo '</tr>';
				echo '</thead>';
				
				$db_values = array();
				$db_values[] = array(
				
					"No",
					"Department",
					"Head of Department",
					"Total Staff"
					);
				
				$i=1;
				echo '<tbody>';
				foreach($select as $row){
					extract($row);
					echo '<tr>';
					echo '<td><center>'.($i++).'.</center></td>';
					echo '<td>'.$this->rgf("branch", $user_branch_id, "branch_id", "branch_name").'</td>';
					echo '<td>'.$this->dept_name($user_department_id).'</td>';
					echo '<td>'.$this->full_name($user_id).'</td>';
					echo '<td>'.number_format($this->total("sysuser", "user_department_id", $user_department_id)).'</td>';
					
					echo '</tr>';
					
					$db_values[] = array(
					
						($i),
						$this->dept_name($user_department_id),
						$this->full_name($user_id),
						number_format($this->total("sysuser", "user_department_id", $user_department_id))
					
					); 
				}
				echo '</tbody>';
				
				echo '</table>';
				
				$t = new TableCreator();
				$heading = "LIST FOR HEAD OF DEPARTMENTS"; //CHANGE
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
	public function passwordStrength($password){
		
		// Validate password strength
		$uppercase = preg_match('@[A-Z]@', $password);
		$lowercase = preg_match('@[a-z]@', $password);
		$number    = preg_match('@[0-9]@', $password);
		$specialChars = preg_match('@[^\w]@', $password);
		
		if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 7) {
		    return true;
		}else{
		    return false;
		}

	}

	public function changedPassword(){
		?>
		<div class="col-md-12">
			<div class="row">
			<?php
			
			$this->detailsOfUsers(user_id());
			?>
		</div>
		</div>

	<?php
	}

	public function gender($sex){
		if($sex)
			return "Male";
		else
			return "Female";
	}

}

?>