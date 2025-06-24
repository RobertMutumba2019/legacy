<?php
Class ApprovalOrder extends BeforeAndAfter{
	public $page = "APPROVAL ORDER";
	
	public function __construct(){
		$access = new AccessRights();
		//$access->pageAccess(user_id(), $this->page, 'V');
	}
	
	public static function getLinks(){
		$page = "APPROVAL ORDER";
		$links = array(
			array(
				"link_name"=>"Requisition", 
				"link_address"=>"approval-order/requisition",
				"link_icon"=>"fa-edit",
				"link_page"=>$page,
				"link_right"=>"A",
			)
		);
		
		return $links;
	}
	
	public function requisitionAction(){
		$db = new Db();
		
		if(isset($_POST['send'])){
			
			$role1= $_POST['role1'];
			
			if(!empty($role1)){
				$setter = 1;
				foreach($role1 as $item){
					///if(!empty($item))
					{
						$insert = $db->query("UPDATE approval_order SET app_role_id = '$item' WHERE app_id = $setter;");
					}
					$setter++;
				}
				
				FeedBack::success("Successfully Updated");
				FeedBack::refresh();
			}else{
				FeedBack::error("Check the roles, they should not be the same.");
			}
			
		}
		
		if(isset($_POST['send2'])){
			
			$role2= $_POST['role2'];
			//print_r($role2);
			if(!empty($role2)){
				$setter = 1;
				foreach($role2 as $item){
					//if(!empty($item))
					{
						$insert = $db->query("UPDATE approval_order_second SET app_role_id = '$item' WHERE app_id = $setter;");
					}
					echo $db->error();
					$setter++;
				}
				
				FeedBack::success("Successfully Updated");
				FeedBack::refresh();
			}else{
				FeedBack::error("Check the roles, they should not be the same.");
			}
			
		}
		
		if(isset($_POST['send3'])){
			
			$role3= $_POST['role3'];
			//print_r($role2);
			if(!empty($role3)){
				$setter = 1;
				foreach($role3 as $item){
					//if(!empty($item))
					{
						$insert = $db->query("UPDATE approval_order_third SET app_role_id = '$item' WHERE app_id = $setter;");
					}
					echo $db->error();
					$setter++;
				}
				
				FeedBack::success("Successfully Updated");
				FeedBack::refresh();
			}else{
				FeedBack::error("Check the roles, they should not be the same.");
			}
			
		}
		
		if(isset($_POST['send4'])){
			
			$role4= $_POST['role4'];
			//print_r($role4);
			if(!empty($role4)){
				$setter = 1;
				foreach($role4 as $item){
					//if(!empty($item))
					{
						
						$insert = $db->query("UPDATE approval_order_fourth SET app_role_id = '$item' WHERE app_id = $setter;");
					}
					echo $db->error();
					$setter++;
				}
				
				FeedBack::success("Successfully Updated");
				FeedBack::refresh();
			}else{
				FeedBack::error("Check the roles, they should not be the same.");
			}
			
		}
		
		if(isset($_POST['send5'])){
			
			$role5= $_POST['role5'];
			
			if(!empty($role5)){
				$setter = 1;
				foreach($role5 as $item){
					//if(!empty($item))
					{
						
						$insert = $db->query("UPDATE approval_order_fifth SET app_role_id = '$item' WHERE app_id = $setter;");
					}
					echo $db->error();
					$setter++;
				}
				
				FeedBack::success("Successfully Updated");
				FeedBack::refresh();
			}else{
				FeedBack::error("Check the roles, they should not be the same.");
			}
			
		}

		?>
		<style>.order{border:1px solid inherit; /*#f1ffbd*/;}.order:hover{background-color:#fff; border:1px solid #000; /*#bdf1ff*/;}</style>		
		<form action="" method="post">
			<div class="col-lg-3 order">
				<h4>Requisition Levels</h4>
				<?php 
					
				$select = $db->select("SELECT app_id, app_role_id FROM  approval_order WHERE app_role_id != 0 ORDER BY app_id ASC");
				$app_role_ids = array();
				$app_ids = array();
				$tot = $db->num_rows();
				
				foreach($select as $sel){
					extract($sel);
					$app_role_ids[] = $app_role_id;
					$app_ids[] = $app_id;
				}
		
				$i=0;
				//while($i <= $db->num_rows() && $i <= $tot+1){				
				while(($i <= $db->num_rows()) && ($i < $tot+1)){				
				?>
				<div class="form-group">
					<label>Select Person <?php echo $i+1; ?>:</label>
					<select name="role1[]" class="form-control">
						<?php 
						/*$rowsx = $db->select("SELECT * FROM user_role ORDER BY ur_name ASC");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						foreach($rowsx[0] as $rowx){
							extract($rowx);
							
							if($ur_id == $app_role_ids[$i])
								echo '<option class="form-control" selected="selected" value="'.$ur_id.'">'.$ur_name.'</option>';
							else
								echo '<option class="form-control" value="'.$ur_id.'">'.$ur_name.'</option>';
						}
						*/
						$rowsx = $db->select("SELECT * FROM user_role");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						foreach($rowsx as $rowx){
							extract($rowx);
							
							if($ur_id == $app_role_ids[$i])
								echo '<option class="form-control" selected="selected" value="'.$ur_id.'">'.$ur_name.'</option>';
							else
								echo '<option class="form-control" value="'.$ur_id.'">'.$ur_name.'</option>';
						}
						?>
						
					</select>
				</div>
				<?php 
				$i++;
				} 
				?>
				<div class="form-group">
					<input onclick = "return confirm('Are you sure you want to this Level?');" type="submit" name="send" value="Send" class="form-control btn btn-success"/>
				</div>
			</div>
			<div class="col-lg-2 order" style="display:none;">
				<h4>Vehicle Return Order<Br/>&nbsp;</h4>
				<?php 
									
				$select = $db->select("SELECT app_id, app_role_id FROM  approval_order_second WHERE app_role_id != 0 ORDER BY app_id ASC");
				$app_role_ids = array();
				$app_ids = array();
				$tot = $db->num_rows();
				
				foreach($select as $sel){
					extract($sel);
					$app_role_ids[] = $app_role_id;
					$app_ids[] = $app_id;
				}
		
				$i=0;
				//while($i <= $db->num_rows() && $i <= $tot+1){				
				while(($i <= $db->num_rows()) && ($i < $tot+1)){				
				?>
				<div class="form-group">
					<label>Select Person <?php echo $i+1; ?>:</label>
					<select name="role2[]" class="form-control">
						<?php 
						/*$rowsx = $db->select("SELECT * FROM user_role ORDER BY ur_name ASC");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						foreach($rowsx[0] as $rowx){
							extract($rowx);
							
							if($ur_id == $app_role_ids[$i])
								echo '<option class="form-control" selected="selected" value="'.$ur_id.'">'.$ur_name.'</option>';
							else
								echo '<option class="form-control" value="'.$ur_id.'">'.$ur_name.'</option>';
						}
						*/
						$rowsx = $db->select("SELECT * FROM designation");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						foreach($rowsx as $rowx){
							extract($rowx);
							
							if($designation_id == $app_role_ids[$i])
								echo '<option class="form-control" selected="selected" value="'.$designation_id.'">'.$designation_name.'</option>';
							else
								echo '<option class="form-control" value="'.$designation_id.'">'.$designation_name.'</option>';
						}
						?>
						
					</select>
				</div>
				<?php 
				$i++;
				} 
				?>
				<div class="form-group">
					<input onclick = "return confirm('Are you sure you want to send this as a correct order for approval during vehicle returns?');" type="submit" name="send2" value="Send" class="form-control btn btn-success"/>
				</div>
			</div>
			
			<div class="col-lg-2 order" style="display:none;">
				<h4>Vehicle Request Order<Br/>&nbsp;</h4>
				<?php 
									
				$select = $db->select("SELECT app_id, app_role_id FROM  approval_order_third WHERE app_role_id != 0 ORDER BY app_id ASC");
				$app_role_ids = array();
				$app_ids = array();
				$tot = $db->num_rows();
				
				foreach($select as $sel){
					extract($sel);
					$app_role_ids[] = $app_role_id;
					$app_ids[] = $app_id;
				}
		
				$i=0;
				//while($i <= $db->num_rows() && $i <= $tot+1){				
				while(($i <= $db->num_rows()) && ($i < $tot+1)){				
				?>
				<div class="form-group">
					<label>Select Person <?php echo $i+1; ?>:</label>
					<select name="role3[]" class="form-control">
						<?php 
						/*$rowsx = $db->select("SELECT * FROM user_role ORDER BY ur_name ASC");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						foreach($rowsx[0] as $rowx){
							extract($rowx);
							
							if($ur_id == $app_role_ids[$i])
								echo '<option class="form-control" selected="selected" value="'.$ur_id.'">'.$ur_name.'</option>';
							else
								echo '<option class="form-control" value="'.$ur_id.'">'.$ur_name.'</option>';
						}
						*/
						$rowsx = $db->select("SELECT * FROM designation");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						foreach($rowsx as $rowx){
							extract($rowx);
							
							if($designation_id == $app_role_ids[$i])
								echo '<option class="form-control" selected="selected" value="'.$designation_id.'">'.$designation_name.'</option>';
							else
								echo '<option class="form-control" value="'.$designation_id.'">'.$designation_name.'</option>';
						}
						?>
						
					</select>
				</div>
				<?php 
				$i++;
				} 
				?>
				<div class="form-group">
					<input onclick = "return confirm('Are you sure you want to send this as a correct order for approval of vehicle requests Xkm within Kampala?');" type="submit" name="send3" value="Send" class="form-control btn btn-success"/>
				</div>
			</div>
			
			<div class="col-lg-2 order" style="display:none;">
				<h4>Pool Fuel Request Order<Br/>&nbsp;</h4>
				<?php 
									
				$select = $db->select("SELECT app_id, app_role_id FROM  approval_order_fourth WHERE app_role_id != 0 ORDER BY app_id ASC");
				$app_role_ids = array();
				$app_ids = array();
				$tot = $db->num_rows();
				
				foreach($select as $sel){
					extract($sel);
					$app_role_ids[] = $app_role_id;
					$app_ids[] = $app_id;
				}
		
				$i=0;
				//while($i <= $db->num_rows() && $i <= $tot+1){				
				while(($i <= $db->num_rows()) && ($i < $tot+1)){				
				?>
				<div class="form-group">
					<label>Select Person <?php echo $i+1; ?>:</label>
					<select name="role4[]" class="form-control">
						<?php 
						/*$rowsx = $db->select("SELECT * FROM user_role ORDER BY ur_name ASC");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						foreach($rowsx[0] as $rowx){
							extract($rowx);
							
							if($ur_id == $app_role_ids[$i])
								echo '<option class="form-control" selected="selected" value="'.$ur_id.'">'.$ur_name.'</option>';
							else
								echo '<option class="form-control" value="'.$ur_id.'">'.$ur_name.'</option>';
						}
						*/
						$rowsx = $db->select("SELECT * FROM designation");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						if($db->num_rows()){
							foreach($rowsx as $rowx){
								extract($rowx);
								
								if($designation_id == $app_role_ids[$i])
									echo '<option class="form-control" selected="selected" value="'.$designation_id.'">'.$designation_name.'</option>';
								else
									echo '<option class="form-control" value="'.$designation_id.'">'.$designation_name.'</option>';
							}
						}
						?>
						
					</select>
				</div>
				<?php 
				$i++;
				} 
				?>
				<div class="form-group">
					<input onclick = "return confirm('Are you sure you want to send this as a correct order for approval on Pool Fuel Requests?');" type="submit" name="send4" value="Send" class="form-control btn btn-success"/>
				</div>
			</div>
			
			<div class="col-lg-2 order" style="display:none;">
				<h4>Pool Fuel Accountability<Br/>&nbsp;</h4>
				<?php 
									
				$select = $db->select("SELECT app_id, app_role_id FROM  approval_order_fifth WHERE app_role_id != 0 ORDER BY app_id ASC");
				$app_role_ids = array();
				$app_ids = array();
				$tot = $db->num_rows();
				
				foreach($select as $sel){
					extract($sel);
					$app_role_ids[] = $app_role_id;
					$app_ids[] = $app_id;
				}
		
				$i=0;
				//while($i <= $db->num_rows() && $i <= $tot+1){				
				while(($i <= $db->num_rows()) && ($i < $tot+1)){				
				?>
				<div class="form-group">
					<label>Select Person <?php echo $i+1; ?>:</label>
					<select name="role5[]" class="form-control">
						<?php 
						/*$rowsx = $db->select("SELECT * FROM user_role ORDER BY ur_name ASC");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						foreach($rowsx[0] as $rowx){
							extract($rowx);
							
							if($ur_id == $app_role_ids[$i])
								echo '<option class="form-control" selected="selected" value="'.$ur_id.'">'.$ur_name.'</option>';
							else
								echo '<option class="form-control" value="'.$ur_id.'">'.$ur_name.'</option>';
						}
						*/
						$rowsx = $db->select("SELECT * FROM designation");
						echo '<option value="0"> --- SELECT --- </option>';
						$j = 0;
						if($db->num_rows()){
							foreach($rowsx as $rowx){
								extract($rowx);
								
								if($designation_id == $app_role_ids[$i])
									echo '<option class="form-control" selected="selected" value="'.$designation_id.'">'.$designation_name.'</option>';
								else
									echo '<option class="form-control" value="'.$designation_id.'">'.$designation_name.'</option>';
							}
						}
						?>
						
					</select>
				</div>
				<?php 
				$i++;
				} 
				?>
				<div class="form-group">
					<input onclick = "return confirm('Are you sure you want to send this as a correct order for approval on Pool Fuel Accountability?');" type="submit" name="send5" value="Send" class="form-control btn btn-success"/>
				</div>
			</div>
			<div class="clearfix"></div>
			<br/><br/><br/>
		</form>
		<?php
		
	}
	
	
}
