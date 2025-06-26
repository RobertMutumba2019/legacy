<?php 
//include "Db.php";
Class AccessRights extends BeforeAndAfter{
	public $id = 0;
	public static function getLinks(){
		$page = "USER ROLES AND PRIVILEGES";
		
		return array(			
			array(
				"link_name"=>"User Rights & Privileges", 
				"link_address"=>"access-rights/all-user-rights-and-privileges",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"A",
			)
		);
	}


	public function id($id){
		$this->id = $id;
	}
	
	public $pages = array(
		'USERS',
	'USER ROLES AND PRIVILEGES',
	'PENDING APPROVALS',
	'AUDIT TRAIL',
	'SETTINGS',
	'APPROVED REQUISITIONS',
	'REQUISITION',
	'USER ROLE',
	'STORES',
	'APPROVAL LEVELS',
	'APPROVAL MATRIX',
	'GROUPS',
	'APPROVALGROUP',
	'DESIGNATION',
	'BRANCHES',
	'DEPARTMENTS',
	'ALL REQUISITIONS'
	);
	
	public $controls = array('A'=>'ADD', 'V'=>'View', 'E'=>'Edit / Modify', 'P'=>'Print', 'I'=>'Import', 'X'=>'Export');

	//public $controls = array('A'=>'ADD', 'V'=>'View', 'E'=>'Edit / Modify', 'D'=>'Deactivate', 'P'=>'Print', 'I'=>'Import', 'X'=>'Export');
	
	public $roles = "";
		
	public function __construct(){
		
		$this->getUserRoles();
		
		//echo $val = 'CREATE TABLE access_rights (ar_id INT PRIMARY KEY AUTO_INCREMENT, ar_role_id INT, ar_page INT, ar_a INT, ar_v INT, ar_e INT, ar_d INT, ar_p INT, ar_i INT, ar_x INT);';
		
		//echo $val = '<BR/><BR/>CREATE TABLE user_role (ur_id INT PRIMARY KEY AUTO_INCREMENT, ur_name VARCHAR(100), ur_added_by INT, ur_date_added INT);';
	}
	
	public function check($page, $privilege){
		
		str_replace(' ', '_',
		 strtolower($this->pages[$i])); //undefined variable $i by Mutumba
	}
	
	public function getUserRights($user_role){
		$users = array();
		$db = new Db();
	$sql = "SELECT user_surname, user_othername FROM sysuser WHERE user_role = '$user_role'";
		$select = $db->select($sql);
		
		if($db->num_rows()){
			if(is_array($select)){

			
			foreach($select as $row){
				extract($row);
				$users[] = $user_surname.' '.$user_othername;

			}
			}
		}
		return $users;
	}
	
	public function getUserRoles(){
		$db = new Db();
		$sql = "SELECT ur_id, ur_name, ur_added_by, ur_date_added FROM user_role ORDER BY ur_name ASC";
		$select = $db->select($sql);
		$this->roles = $select;
		//print_r($this->roles);
	}
	
	public function deleteRole(){
		echo $id = portion(3);
		$db = new Db();
		$sql = "DELETE FROM access_rights WHERE ar_role_id = '$id'";
		$select = @$db->query(@$sql);
		echo $db->error();
		 $sql = "DELETE FROM user_role WHERE ur_id = '$id'";
		$select = $db->query($sql);
		echo $db->error();
		
		if($select){
			FeedBack::success('Deleting. Please wait ...');
			FeedBack::refresh(1,return_url().'access-rights/all-user-rights-and-privileges');
		}else{
			FeedBack::error('Not Deleted '.$db->error());
		}
	}
	
	public function getUserRole($id){
		$db = new Db();
		
		$sql = "SELECT ur_id, ur_name, ur_added_by, ur_date_added FROM user_role WHERE ur_id = '$id'";
		$select = $db->select($sql);
		if (is_array($select) && isset($select[0]) && is_array($select[0])) {
		return $select[0];
		}
	}
	
	public function addUserRoleAndPrivilegesAction(){
		$x = new AccessRights();
		?>
			<div class="center" id="error">
				 <?php
				if(isset($error))
				  {
					foreach($error as $error)
					  {
						?>
						<div class="<?php echo $call_alert; ?>">
						  <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; 
						  <?php echo $error; ?>
						  </div>
						  <?php
						}
					  }
					  ?>
			</div>
		</div>
		<div class="forms-grids">
			<?php
			
			$db = new Db();
			
			if(isset($_POST['submit'])){
				
				$role_name = $_POST['role_name'];
				$pages = $_POST['name'];
				$time = time();
				$user_id = user_id();
				
				$insert = $db->insert("user_role", ["ur_name"=>$role_name, "ur_added_by"=>$user_id, "ur_date_added"=>$time]);
				
				echo $db->error();
				
				$role_id = $db->last_id();
                //get role id after inserting
                $counter = count($pages);//get role id after inserting
				
				for($i=0; $i<$counter; $i++){
					
					$page_name = $pages[$i];
					$re = array();
					
					foreach($x->controls as $key => $control){	
						$key = strtolower($key);
						$key1 = $_POST[$key.$i];
						$re[] = $key1;						
					}
					$rec = "'".implode(" ', '", $received)."'";
									
					$insert = $db->insert("access_rights", ["ar_role_id"=>$role_id,"ar_page"=>$page_name,"ar_a"=>$re[0],"ar_v"=>$re[1],"ar_e"=>$re[2],"ar_d"=>$re[3],"ar_p"=>$re[4],"ar_i"=>$re[5],"ar_x"=>$re[6]]);
					
					$db->error();
				}	

				if(empty($db->error())){
					FeedBack::success();
					FeedBack::refresh(3, return_url().'access-rights/all-user-rights-and-privileges');
				}else{
					echo $db->error();
				}
				
			}
			?>
			<div class="col-md-12">
			<form data-toggle="validator" validate="true" enctype="multipart/form-data" method="post">
				<div class="col-sm-8 panel panel-widget">
					<div class="validation-grids widget-shadow" data-example-id="basic-forms"> 
					<div class="input-info">
							<h3>Role Name :</h3>
						</div>
						
						<div class="form-body form-body-info">
							
									<div class="form-group">
									<input type="text" required name="role_name" placeholder="Enter Role Name" class="input-block-level" value="<?php echo '';  ?>">
									<br/><br/><p>Check Privileges Per Page :</p>
								<?php
								
								
								
								for($j=0; $j<1; $j++){
									echo '<br/>';
									echo '<table>';
									//echo '<tr><td><br/><b>'.ucwords(strtolower($x->roles[$j])).'</b><br/>'.implode($x->usersUnderRights, ", ").'</td></tr>';
									echo '<tr valign="top">';
									echo '<td>';
									
									echo '<table border="1">';
									
									echo '<tr valign="bottom">';
									echo '<th rowspan="2">No.</th>';
									echo '<th rowspan="2">PAGE</th>';
									echo '<th colspan="8">PRIVILEGES</th>';
									echo '</tr>';
									
									echo '<tr>';
									foreach($x->controls as $key => $control){									
										echo '<th><center>'.$key.'</center></th>';
									}
									echo '</tr>';
									$q=1;
									sort($x->pages);
                                    $counter = count($x->pages);
									for($i=0; $i<$counter; $i++){
										echo '<tr>';
										echo '<td>'.($i+1).'</td>';
										echo '<td>'.$x->pages[$i].'</td>';
										echo '<input type="hidden" value="'.$x->pages[$i].'" name="name[]"/>';
										
										foreach($x->controls as $key => $control){
											echo '<td style="width:20px"><input type="checkbox" style="width:20px" name="'.strtolower($key).$i.'" value="1" title="'.$control.' '.$x->pages[$i].'"  id="la'.$q.'"><label for="la'.$q.'"></label></td>';
											$q++;
										}
										echo '</tr>';
									}
									
									echo '</table>';
									
									echo '</td>';
									echo '<td> &nbsp;  &nbsp; </td>';
									echo '<td>';
									$x->rightsKey();
									echo '</td>';
									echo '</tr>';
									echo '</table>';
								}
								?>												
								</div>
								<div>
								</div>
								<div class="clearfix"></div>
								<div class="bottom">
									<div class="form-group recover-button">												
										<button type="submit" name="submit" class="btn btn-primary pull-right">Add Role</button>
									</div>
								</div>
						</div>
					</div>
				</div>
				</form>
			<div class="clearfix"></div>
			<br/><br/><br/>
	</div>
		<?php
	}
	
	public function allUserRightsAndPrivilegesAction(){
		$db = new Db();
		//get all user all accounts
		//get the user assigned access rights for each user
		
		echo "<b>Total User Roles: ".count($this->roles);
		echo '</b><br/>';
		$all = array();
        $counter = count($this->roles);
		for($j=0; $j<$counter; $j++){
			$all[] = '<a  href="#'.ucwords($this->roles[$j]['ur_name']).'"> '.ucwords($this->roles[$j]['ur_name']).'</a>';
		}
		echo '<ol><li>'.implode(' </li><li> ', $all).'</li></ol>';
		echo '<br/>';
		echo '<b><h4>Roles And Privileges</h4></b>';
		echo '<style type="text/css">.divPart .divSection{padding:50px !important;}.divPart .divSection+div{border-top:1px solid black;}.divPart .divSection:first-child{border-top:1px solid black;}.divPart .divSection:last-child{border-bottom:1px solid black;}</style>';
		echo '<div class="divPart">';
        $counter = count($this->roles);
		for($j=0; $j<$counter; $j++){
			echo '<div  id="'.ucwords($this->roles[$j]['ur_name']).'"></div>';
			if ($j%2==1) {
                echo '<div class="divSection" style="background-color: white;padding:10px;">';
            } else {
                echo '<div class="divSection" style="background-color: #fff1f1;padding:10px;">';
            }
			$user_id = $this->roles[$j]['ur_id'];
			
			//SELECT ar_id, ar_role_id, ar_page, ar_a, ar_v, ar_e, ar_d, ar_p, ar_i, ar_x FROM access_rights WHERE 1
			
			$sql = "SELECT * FROM access_rights WHERE ar_role_id = '$user_id'";
			$rows = $db->select($sql);
			
			echo $db->error();	
			if(is_array($rows)){

				
			foreach($rows as $row){
				extract($row);
				//echo '<pre>'; print_r($row); echo '</pre>';
				//echo $ar_page.">>>";
				//echo 'Userid =>'.$user_id.' j=>'.$j.'<br/>';
				foreach($this->controls as $key => $control){									
					//echo '<th>'.$key.'</th>';
					//echo str_replace(' ', '_', strtolower($ar_page))."ar_".strtolower($key).$j.' ';
					${str_replace(' ', '_', strtolower($ar_page))."ar_".strtolower($key).$j} = ${"ar_".strtolower($key)};
				}
			}	//$a++;
				//echo '<br/>';
			}
			$rt = $this->roles[$j]['ur_id'];
			$rtv = $this->getUserRights($rt);
			echo '<div  id="'.ucwords($this->roles[$j]['ur_name']).'"></div>';
			echo '<table style="font-size:14px; font-family:arial;">';
			echo '<tr><td><br/><a href="'.return_url()."access-rights/edit-user-role-and-privileges/".$this->roles[$j]['ur_id'].'" title="Edit User Roles & Right"> <b>'.($j+1).'.&nbsp;  '.ucwords(($this->roles[$j]['ur_name'])).'</b></a> &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; <a  href="#top">Back to Top</a><br/>Total Users:'.count($this->getUserRights($this->roles[$j]['ur_id'])).'<br/>'.implode(",", $rtv).' </td></tr>';
			echo '<tr valign="top">';
			echo '<td>';
			echo '<style type="text/css">.tx th, .tx td{padding:0; margin: 0;}</style>';
			echo '<table border="1" id="table" class="tx" cellpadding="2" cellspacing="0" style="font-size:14px; font-family:arial;width:500px;">';
			
			echo '<tr valign="bottom">';
			echo '<th rowspan="2" width="20px">No.</th>';
			echo '<th rowspan="2">PAGE</th>';
			echo '<th colspan="8">PRIVILEGES</th>';
			echo '</tr>';
			
			echo '<tr>';
			foreach($this->controls as $key => $control){									
				echo '<th  style="width:20px; text-align:center;">'.$key.'</th>';
			}
			echo '</tr>';
			$ar = $t = "";
			sort($this->pages);
			for($i=0; $i<count($this->pages); $i++){
				$show = 0;
				foreach($this->controls as $key => $control){
					$ar = "".str_replace(' ', '_', strtolower($this->pages[$i]));
					//echo $ar.'ar_'.strtolower($key).$i.$j."<br/>";
					@$t = ${$ar.'ar_'.strtolower($key).$j};
					if($t == 1){
						$show = 1;
					}
				}

				if($show !== 0){
					echo '<tr>';
					echo '<td>'.($i+1).'</td>';
					echo '<td>'.$this->pages[$i].'</td>';
					echo '<input type="hidden" value="'.$this->pages[$i].'" name="name[]"/>';
					$ar = $t = "";
					foreach($this->controls as $key => $control){
						$ar = "".str_replace(' ', '_', strtolower($this->pages[$i]));
						//echo $ar.'ar_'.strtolower($key).$i.$j."<br/>";
						@$t = ${$ar.'ar_'.strtolower($key).$j};
						if($t == 1){
							echo '<td style="width:20px; text-align:center;">&#10004;</td>';
						}else{
							echo '<td  style="width:20px; text-align:center;"></td>';
						}
					}
					echo '</tr>';
				}
			}			
			
			echo '</table>';
			
			echo '</td>';
			echo '<td> &nbsp;  &nbsp; </td>';
			echo '<td>';
			$this->rightsKey();
			echo '</td>';
			echo '</tr>';
			echo '</table>';
			echo '</div>';
		}
		echo '</div>';

		echo '<br/>';
		echo '<br/>';
		echo '<br/>';
		?>

<script type="text/javascript">		
	$('.eagle-load').off('click').on('click',function(e){
		var urlPath = $('#urlPath').val();
		e.preventDefault();
		var href = $(this).attr('href');
		$('#EagleContainer').fadeTo('normal', 0.4).append('<img class="eaglepreview" src="'+urlPath+'images/loading3.gif" alt="loading..."/>');
		
		var form_data = new FormData();
		
		form_data.append('href', href);
		$.ajax({
			url: urlPath+"ajax/__EAGLE_route.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
		        $('#EagleContainer').fadeTo('normal', 1);
				$('#EagleContainer').html(data);
			}
		});
	});
</script>
		<?php
	}
	
	public function editUserRoleAndPrivilegesAction(){
	$db = new Db();
	$x = new AccessRights();
	$y = new AccessRights();
	$z = new AccessRights();
	?>
	<!--grids-->
	<div class="grids">
		
		
		<div class="progressbar-heading grids-heading">
			<div class="center" id="error">
				 <?php
				if(isset($error))
				  {
					foreach($error as $error)
					  {
						?>
						<div class="<?php echo $call_alert; ?>">
						  <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; 
						  <?php echo $error; ?>
						  </div>
						  <?php
						}
					  }
					  ?>
			</div>
		</div>
		<div class="forms-grids">
			<?php
			$id = portion(3);
			//$id = $this->id;
			//echo '<a href="'.return_url()."access-rights/delete-role/".$id.'" class="btn btn-danger pull-right">Delete Role</a>';
			
			$user = $this->getUserRole($id);
			//print_r($user);
			extract($user);
			
			$sql = "SELECT * FROM access_rights WHERE ar_role_id = '$ur_id'";
			$db = new Db();
			$rows = $db->select($sql);
			//print_r($rows);
			$a = $j = 0;
			if(is_array($rows)){

			
			foreach($rows as $row){
				extract($row);
				//echo '<pre>'.print_r($row).'</pre>';
				//echo 'Userid =>'.$user_id.' j=>'.$j.'<br/>';
				
				foreach($this->controls as $key => $control){									
					//echo '<th>'.$key.'</th>';
					//echo ${"ar_".strtolower($key)}.'<br/>';
					//echo str_replace(' ', '_', strtolower($ar_page))."ar_".strtolower($key).$j.' ';
					${str_replace(' ', '_', strtolower($ar_page))."ar_".strtolower($key).$j} = ${"ar_".strtolower($key)};
				}
				$a++;
				//echo '<br/>';
			}
		    }
			if(isset($_POST['submit'])){
				
				$role_name = $_POST['role_name'];
				$pages = $_POST['name'];
				$time = time();
				$user_id = user_id();
				
				$x = $db->update("user_role", ["ur_name"=>$role_name], ['ur_id'=>$id]);
                $counter = count($pages);
															
				for($i=0; $i<$counter; $i++){
					
					$page_name = $pages[$i];
					$received = array();
					$db = new Db();
					$sql = "SELECT * FROM access_rights WHERE ar_page ='$page_name' AND ar_role_id = '$id'";
					$insert = $db->select($sql);
					echo $db->error();
					if($db->num_rows()){
						$received1 = array();
						//$received1["ar_role_id"] = $id;						
						foreach($this->controls as $key => $control){	
							$key = strtolower($key);
							$key1 = $_POST[$key.$i];
							$received1["ar_$key"] = $key1;	
						}
						//echo $all = implode(', ',$received1);
						//$x = $db->update("access_rights", $received1, ["ar_page" => "$page_name"]);
						$x = $db->update("access_rights", $received1, ["ar_role_id"=>$id, "ar_page" => "$page_name"]);
						echo $db->error();
						//$sql = "UPDATE access_rights SET ar_role_id = '$id', $rec1  WHERE ar_role_id = '$id' AND ar_page = '$page_name'";
						//$insert = $db->query($sql);

					}else{
						$received1 = array();
						$received1["ar_role_id"] = $id;
						$received1["ar_page"] = "$page_name";
						foreach($this->controls as $key => $control){	
							$key = strtolower($key);
							$key1 = $_POST[$key.$i];
							$received1["ar_$key"] = $key1;			
						}
						
						$x = $db->insert("access_rights",$received1);
												
					}
				}	

				if($insert){
					FeedBack::success();
					FeedBack::refresh(3, return_url()."access-rights/all-user-rights-and-privileges");
				}else{
					echo $db->error();
				}
				
				
				
			}
			?>
			<div class="col-md-12">
			<form data-toggle="validator" validate="true" enctype="multipart/form-data" method="post">
				<div class="col-sm-12 panel panel-widget">
					<div class="validation-grids widget-shadow" data-example-id="basic-forms"> 
					<div class="input-info">
							<h3>Role Name :</h3>
						</div>
						
						<div class="form-body form-body-info">
							
									<div class="form-group">
									<input type="text" name="role_name" placeholder="Enter Role Name" class="input-block-level" value="<?php echo $ur_name;  ?>" style="width:300px;">
									<br/><br/><p>Check Privileges Per Page :</p>
								<?php
								
								$sql = "SELECT `ur_id`, `ur_name`, `ur_added_by`, `ur_date_added` FROM `user_role` ORDER BY ur_name ASC";
								$select = $db->select($sql);
								
								for($j=0; $j<1; $j++){
									//echo '<br/>';
									echo '<table style="font-size:10px; font-family:arial;">';
									echo '<tr valign="top">';
									echo '<td>';
									echo '<style>.thd th, .thd td{padding:0 !important; margin:0 !important;}.thd th input{background-color: white !important;}</style>';
									echo '<table border="1" id="table" class="thd" cellpadding="2" cellspacing="0" style="font-size:12px; font-family:arial;">';

									echo '<tr valign="bottom">';
									echo '<th rowspan="2">No.</th>';
									echo '<th rowspan="2">PAGE</th>';
									echo '<th colspan="6">PRIVILEGES</th>';
									echo '<th rowspan="3" style="width:80px;">';

									echo '<Br/><span  onclick="return multipleChecker(); " id="checker">Check All</span><br/>';
									echo '<bR/><center>LINE</center>';
									echo '</th>';
									echo '</tr>';

									echo '<tr>';
									foreach($this->controls as $key => $control){							
										echo '<th  style="width:20px; text-align:center;">'.$key.'</th>';
									}
									echo '</tr>';

									echo '<tr>';
									echo '<th><Br/><br/></th>';
									echo '<th>CHECK COLUMN</th>';
									$n = 0;
									foreach($this->controls as $key => $control){
										$n++;
										echo '<th style="width:20px;padding-left: 5px;text-align: center;"><span  onclick="return multipleCheckerColumn('.$n.'); " class="checker" id="checkerColumn'.$n.'" style="padding:0 5px;background-color:yellow;color:blue;">C</span></th>';
									}
									echo '</tr>';

									$ar = $t = "";
									sort($this->pages);
									$q=1;
                                    $counter = count($this->pages);
									for($i=0; $i<$counter; $i++){
										echo '<tr>';
										echo '<td>'.($i+1).'</td>';
										echo '<td>'.$this->pages[$i].'</td>';
										echo '<input type="hidden" value="'.$this->pages[$i].'" name="name[]"/>';
										$ar = $t = "";
										$n = 0;
										foreach($this->controls as $key => $control){
											$n++;
											$ar = "".str_replace(' ', '_', strtolower($this->pages[$i]));
											//echo $ar.'ar_'.strtolower($key).$i.$j."<br/>";
											@$t = ${$ar.'ar_'.strtolower($key).$j};
											if($t==1){
												echo '<td class="" style="width:20px;text-align: center;padding-left: 5px;"><input class="AllCheckBoxes AllCheckBoxesLine'.$i.' AllCheckBoxesColumn'.$n.'" type="checkbox" checked="checked" style="width:20px" name="'.strtolower($key).$i.'" value="1" title="'.$control.' '.$this->pages[$i].'"  id="la'.$q.'"><label for="la'.$q.'"></label></td>';
											}else{
												echo '<td class="" style="text-align:center;width:20px;padding-left: 5px;"><input class="AllCheckBoxes AllCheckBoxesLine'.$i.'  AllCheckBoxesColumn'.$n.'" type="checkbox" style="width:20px" name="'.strtolower($key).$i.'" value="1" title="'.$control.' '.$this->pages[$i].'"  id="la'.$q.'"><label for="la'.$q.'"></label></td>';
											}
											$q++;
											
										}

										echo '<th style="width:20px;text-align: center;"><span  onclick="return multipleCheckerLine('.$i.'); " class="checker" id="checkerLine'.$i.'" style="padding:0 10px;">C</span></th>';

										echo '</tr>';
									}			
									
									echo '</table>';
									
									echo '</td>';
									echo '<td> &nbsp;  &nbsp; </td>';
									echo '<td>';
									$this->rightsKey();
									echo '</td>';
									echo '</tr>';
									echo '</table>';
								}
								?>												
								</div>
								<div class="clearfix"></div>
								<div class="bottom">
									<div class="form-group ">												
										<button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-fx fa-save"></i> Save</button>
									</div>
								</div>
						</div>
					</div>
				</div>
				</form>
			<div class="clearfix"></div>
			<br/><br/><br/>
	</div>
	<!--//grids-->
	<?php
	}
	
	public function rightsKey(){
		echo '<style type="text/css">.thde td, .thde th{padding:0 5px !important;}</style>';
		echo '<table border="1" id="table" class="thde" cellpadding="2" cellspacing="0" style="font-size:14px; font-family:arial;">';
		
		echo '<tr>';
		echo '<th colspan="2">KEY</th>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<th>SYM</th>';
		echo '<th>MEANING</th>';
		echo '</tr>';

		foreach($this->controls as $key => $control){
			echo '<tr>';
			echo '<td  style="text-align:center;">'.$key.'</td>';
			echo '<td>'.$control.'</td>';
			echo '</tr>';
		}

		echo '<tr>';
		echo '<td style="text-align:center;">U</td>';
		echo '<td>Uncheck</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td style="text-align:center;">C</td>';
		echo '<td>Check</td>';
		echo '</tr>';

		echo '</table>';
	}
	
	public function showPages(){
		sort($this->pages);
		echo '<ol><li>'.implode('</li><li>', $this->pages).'</li></ol>';
	}
		
	public function pageAccess($user_id, $page, $right){
		
		if(!in_array(strtoupper($page), $this->pages)){
			//echo $page."NOT KNOWN !!!<br/> Enter one of the following pages:<br/> ".implode(", ", $this->pages);
			return 0;
		}else{
			$db = new Db();
			$right = 'ar_'.strtolower($right);
			
			$sql = "SELECT user_role FROM sysuser WHERE user_id = '$user_id'";
			$select = $db->select($sql);
			if (is_array($select) && isset($select[0]) && is_array($select[0])) {
			extract($select[0]);
			}
			
			$sql = "SELECT $right FROM access_rights WHERE ar_page = '$page' AND ar_role_id = '$user_role'";
			extract(@$db->select($sql)[0]);
			
			if($$right == 0){
				//echo '<H1><center style="color:red;">YOU DONT HAVE ACCESS TO THIS PAGE</center></H1>';
				echo '<meta http-equiv="refresh" content="0;'.return_url().'dashboard/index"/> ';
			}else{
				//echo 'ACCESS';
			}
		}
        return null;
				
	}
		
	public function sectionAccess($user_id, $page, $right){
		
		if(!in_array(strtoupper($page), $this->pages)){
			//echo $page." NOT KNOWN !!!<br/> Enter one of the following pages:<br/> ".implode(", ", $this->pages);
			return 1;
		}else{
			$db = new Db();
			$right = 'ar_'.strtolower($right);
			
			$sql = "SELECT user_role FROM sysuser WHERE user_id = '$user_id'";
			$select = $db->select($sql);
			if ($db->num_rows()) {
				if (is_array($select) && isset($select[0]) && is_array($select[0])) {
                extract(@$select[0]);
				}
            }
			//print_r($select);
			
			$sql = "SELECT $right FROM access_rights WHERE ar_page = '$page' AND ar_role_id = '$user_role'";
			$db2 = new Db();
			$sel = $db2->select($sql);
			if($db2->num_rows()){
				if (is_array($sel) && isset($sel[0]) && is_array($sel[0])) {
				extract($sel[0]);	
				}			
			}
			//echo $$right;
			return $$right;
		}
		
	}
		
}

//$x = new AccessRights();

//$x->viewAll();
