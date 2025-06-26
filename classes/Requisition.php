<?php
Class Requisition extends BeforeAndAfter{
	public $id = "";
	public $page = "REQUISITION";
	
	public function id($id){
		$this->id = $id;
	}

	public function __construct(){
		new AccessRights();
		//$access->pageAccess(user_id(), $this->page, 'V');
	}
	
	public static function getLinks(){
		$r = new Requisition();

		$num = $r->rejected(user_id());
		$rejected = "";
		if($num){
			$rejected = " (".number_format($num).")";
		}

		$num = $r->pending(user_id());
		$pending = "";
		if($num){
			$pending = " (".number_format($num).")";
		}

		$num = $r->draft(user_id());
		$draft = "";
		if($num){
			$draft = " (".number_format($num).")";
		}

		$page = "REQUISITION";
		
		return array(
			array(
				"link_name"=>"New Requisition", 
				"link_address"=>"requisition/new-requisition",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"Approved", 
				"link_address"=>"requisition/approved-requisitions",
				"link_icon"=>"fa-check",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Draft".$draft, 
				"link_address"=>"requisition/draft-requisitions",
				"link_icon"=>"fa-table",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Pending".$pending,
				"link_address"=>"requisition/pending-requisitions",
				"link_icon"=>"fa-info",
				"link_page"=>$page,
				"link_right"=>"V",
			),
			array(
				"link_name"=>"Rejected".$rejected, 
				"link_address"=>"requisition/rejected-requisitions",
				"link_icon"=>"fa-times",
				"link_page"=>$page,
				"link_right"=>"V",
			),
		);
	}
	
	public function deleteVehicleRequestAction(){
		$id = portion(3);
		$this->deletor("vehicle_request", "vr_id",  $id, 'vehicle-request/all-vehicle-requests');
	}
	
	public function AddVehicleRequestAction(){
		$this->full_name($this->hod(user_id()));
		$this->vehicle_request_number();

	}

	public function requisition_number(){		
		$dm = date('y').date('m');
		$suffix = "RQN".$dm;
		$db = new Db();
		$sql = "SELECT TOP 1 req_number FROM requisition WHERE req_number  <> '' AND req_number IS NOT NULL ORDER BY req_id DESC";
		$select = $db->select($sql);
		$x = "";
		if($db->num_rows()){
			if (is_array($select) && isset($select[0]) && is_array($select[0])) {
			extract($select[0]);
			}
			$x = explode($suffix, $req_number);
			$x = end($x);
		}
		//echo '>>'.$req_number;
		$f = (int)$x+1;
		return $suffix.str_pad($f, 5, "0", STR_PAD_LEFT);	
	}

	public function status($req){
		new Db();
		$s = $this->rgf("requisition", $req, "req_id", "req_status");
		if($s=='-1'){
			return 'Draft';
		}elseif($s=='0'){
			return 'Rejected';
		}else{
			$i=1;
			while($i<4){
				$s = $this->rgf("requisition", $req, "req_id", "req_app".$i."_user_id");
				if(empty($s)){
					return 'Pending Approval ('.$i.'/'.$this->levels().')';
				}elseif($i==$this->levels()){
					return 'Approved';
				}
				$i++;
			}
		}
        return null;
	}


	public function newRequisitionAction(){

		if($this->rgf("approval_group", user_id(), "apg_user", "apg_id")){
			Feedback::error("You can not create any requisition because you are an Approver");
		}else{	


		if(isset($_POST['add_more'])){

			$title = $_POST['title']; $division = $_POST['division'];
			$item_code = $_POST['item_code'];
			$item_quantity = $_POST['item_quantity'];
			$uom = $_POST['uom'];
			$up = $_POST['up'];
			$item_description = $_POST['item_description'];

			for($i=0; $i<$_SESSION['line_number']; $i++){
				${"item_code".$i} = $item_code[$i];
				${"item_quantity".$i} = $item_quantity[$i];
				${"uom".$i} = $uom[$i];
				${"up".$i} = $up[$i];
				${"item_description".$i} = $item_description[$i];
			}

			$add_more_number = $_POST['add_more_number'];
			if(isset($_SESSION['line_number'])){
				$_SESSION['line_number'] += $add_more_number;
			}else{
				$_SESSION['line_number'] = 1+$add_more_number;
			}
		}

		if(isset($_POST['remove'])){

			$title = $_POST['title']; $division = $_POST['division'];
			$item_code = $_POST['item_code'];
			$item_quantity = $_POST['item_quantity'];
			$uom = $_POST['uom'];
			$up = $_POST['up'];
			$item_description = $_POST['item_description'];

			if (isset($_SESSION['line_number'])) {
                if($_SESSION['line_number'] != 1){
					$_SESSION['line_number'] -= 1;
				}
            } elseif ($numbers) {
                $_SESSION['line_number'] = $numbers-1;
            }

		}
        //if(isset($_POST['remove']) || isset($_POST['add_more']) ){
        $title = $_POST['title'];
        $division = $_POST['division'];
        $item_code = $_POST['item_code'];
        $item_quantity = $_POST['item_quantity'];
        $uom = $_POST['uom'];
        $up = $_POST['up'];
        $item_description = $_POST['item_description'];
        for($i=0; $i<$_SESSION['line_number']; $i++){
				${"item_code".$i} = $item_code[$i];
				${"item_quantity".$i} = $item_quantity[$i];
				${"uom".$i} = $uom[$i];
				${"up".$i} = $up[$i];
				${"item_description".$i} = $item_description[$i];
			}

		if(isset($_POST['draft'])||isset($_POST['forward'])){
			$db = new Db();
			$title = $_POST['title']; $division = $_POST['division'];
			$item_code = $_POST['item_code'];
			$item_quantity = $_POST['item_quantity'];
			$uom = $_POST['uom'];
			$up = $_POST['up'];
			$item_description = $_POST['item_description'];

			if(isset($_POST['draft'])||isset($_POST['forward'])){
				if(isset($_POST['forward'])){
					if(empty($title)){
						$errors[] = "Enter Title";
						${"title_border"} = "border:1px solid red;";
					}else{
						${"title_border"} = "";							
					}

					if(empty($division)){
						$errors[] = "Select Division / Location";
						${"division_border"} = "border:1px solid red;";
					}else{
						${"division_border"} = "";							
					}
					
				}

				for($i=0; $i<$_SESSION['line_number']; $i++){

					$item = $item_code[$i];
					$qty = $item_quantity[$i];
					$measure = $uom[$i];
					$price = $up[$i];
					$desc = $item_description[$i];

					if (isset($_POST['forward']) && (!empty($item) || !empty($qty) || !empty($measure) || !empty($price) || !empty($desc))) {
                        if(empty($item)){
								$errors[] = "Check <b>Line ".($i+1)."</b>, Item code must not be empty";
								${"item_code_border".$i} = "border:1px solid red;";
							}else{
								${"item_code_border".$i} = "";							
							}
                        if(empty($qty)){
								$errors[] = "Check <b>Line ".($i+1)."</b>, Quantity must not be empty";
								${"item_quantity_border".$i} = "border:1px solid red;";
							}else{
								${"item_quantity_border".$i} = "";							
							}
                        if(empty($measure)){
								$errors[] = "Check <b>Line ".($i+1)."</b>, Unit of Measure must not be empty";
								${"uom_border".$i} = "border:1px solid red;";
							}else{
								${"uom_border".$i} = "";							
							}
                        // if(empty($price)){
                        // 	$errors[] = "Check <b>Line ".($i+1)."</b>, Price must not be empty";
                        // 	${"up_border".$i} = "border:1px solid red;";
                        // }else{
                        // 	${"up_border".$i} = "";							
                        // }
                        if(empty($desc)){
								$errors[] = "Check <b>Line ".($i+1)."</b>, Description must not be empty";
								${"item_description_border".$i} = "border:1px solid red;";
							}else{
								${"item_description_border".$i} = "";							
							}
                    }
				}

				if($errors === []){
					$ref = user_id().time();

					if(isset($_POST['draft'])){
						$insert = $db->insert("requisition", ["req_number"=>NULL, "req_title"=>$title, "req_division"=>$division, "req_ref"=>$ref, "req_added_by"=>user_id(), "req_date_added"=>time(), "req_status"=>-1]);
					}

					if(isset($_POST['forward'])){
						//======================================================
						// $rr = $this->hod(user_id());
						// $ad = $this->myDelegate($rr, time());
						// $activeDelegate = $ad['delegate'];
						//========================================================
						$rg = $this->requisition_number();
						$insert = $db->insert("requisition", ["req_number"=>$rg, "req_title"=>$title, "req_division"=>$division, "req_ref"=>$ref, "req_added_by"=>user_id(), "req_date_added"=>time(), "req_status"=>1, "req_hod_id"=>$this->hod(user_id()), "req_delegate1"=>$activeDelegate, "req_delegator1"=>$this->hod(user_id())]);
						//sending email to 


						//================ SEND EMAIL T0 NEXT APPROVAL ===================

						//req_division === 
						$db = new Db();
						$select = $db->select("SELECT gr_name, apg_user, user_email FROM approval_group, approval_matrix, groups, sysuser WHERE ap_id = '$division' AND gr_matrix = ap_id AND gr_id = apg_name AND apg_user = user_id");
						
						$users_emails = array();
						//print_r($select);
						if(is_array($select)){

						
						foreach($select as $row){
							extract($row);
							$users_emails[]=$user_email;
						}
					}

						
						// if($activeDelegate){
						// 	$next = $activeDelegate;
						// 	$hod_name = $this->ruf($next, "user_othername");
						// 	$hod_telephone = $this->ruf($next, "user_telephone");
						// 	$hod_email = $this->ruf($next, "user_email");
						// 	$x = " (Delegation)";
						// }else{
						// 	$next = $this->hod(user_id());
						// 	$hod_name = $this->ruf($next, "user_othername");
						// 	$hod_telephone = $this->ruf($next, "user_telephone");
						// 	$hod_email = $this->ruf($next, "user_email");
						// }

						$to = $hod_email;
						$subject = "PENDING APPROVAL $x";
						
						$link = return_url()."requisition/view-requisition/".$rg;

						$message = "Hello ".$gr_name.",\n";
						$message .= "\r\n<br/><br/>You have a pending requisition $x with No.: <b>$rg</b>\r\n<br/><br/> ";
						$message .= "\r\nYou can use this link: <a href='$link'>$link</a>";
						
						FeedBack::sendmailz($users_emails,$subject,$message,$hod_name);
						//============================================================
					}

					echo $db->error();
					for($i=0; $i<$_SESSION['line_number']; $i++){
					
						$item = $item_code[$i];
						$qty = $item_quantity[$i];
						$measure = $uom[$i];
						$price = $up[$i];
						$desc = $item_description[$i];

						$insert = $db->insert("requisition_item", [
							"ri_code"=>$item,
							"ri_quantity"=>$qty,
							//"ri_price"=>$price,
							"ri_uom"=>$measure,
							"ri_description"=>$desc,
							"ri_ref"=>$ref,
						]);
						echo $db->error();
					}
					$db->query("UPDATE attachments SET at_req_id = '$ref' WHERE at_req_id IS NULL");
					echo $db->error();
				}else{
					Feedback::errors($errors);					
				}
			}

			if(isset($_POST['forward'])&&$errors === []){
				Feedback::success();
				Feedback::refresh(3, return_url()."requisition/pending-requisitions");
				unset($_SESSION['line_number']);
			}elseif(isset($_POST['draft'])){
				Feedback::success();
				Feedback::refresh(3, return_url()."requisition/draft-requisitions");
				unset($_SESSION['line_number']);
			}

		}
		if (empty($_SESSION['line_number'])) {
            $_SESSION['line_number'] = 1;
        }
		$line_number = (empty($_SESSION['line_number']))? 1 : $_SESSION['line_number'];

		?>
		<div class="row">
			<form action="" method="post" class="search-area-container">
			<div class="col-md-12" style="display:none;">
				<div class="row">
					<div class="col-md-3" style="margin-bottom:20px;">
						<label>Requisition Number</label>
						<input type="text" disabled class="form-control" name="requisition_number" value="<?php echo $this->requisition_number();?>">
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="row">					
					<div class="col-md-5" style="margin-bottom:20px;">
						<label>Location/Division</label>
						<select style="width:100%; <?php echo ${"division_border"}; ?>" name="division" class="select3" id="division">
							<option value="">Select</option>
							<?php 
							$db = new Db();
							$select = $db->select("SELECT * FROM approval_matrix ORDER BY ap_unit_code ASC");
							if(is_array($select)){

							
							foreach($select as $row){
								extract($row);
								if ($division == $ap_id) {
                                    echo '<option selected="selected" value="'.$ap_id.'">'.$ap_unit_code.' - '.$ap_code.'</option>';
                                } else {
                                    echo '<option value="'.$ap_id.'">'.$ap_unit_code.' - '.$ap_code.'</option>';
                                }
							}
							}
							?>
						</select>
					</div>
					<div class="col-md-7" style="margin-bottom:20px;">
						<label>Title</label>
						<input style="<?php echo ${"title_border"}; ?>" type="text" class="form-control" name="title" value="<?php echo $title; ?>" id="title">
					</div>
				</div>
				<?php for($i=0; $i< $line_number ; $i++){ ?>
				<?php 
				$vop = ($i%2!=0)?"background-color:#ebefff;padding:0 10px !important":"background-color:#fff;padding:0 10px !important;";
				// for($i=0; $i<1; $i++){
				?>				
				<div class="item-line-group" style="padding:0;margin:0;border:none;background-color: inherit;padding:0 30px;">
					<?php if($i!=0&&$i==$line_number-1){ ?>
					<button type="submit" style="margin-top: 0px;z-index: 10" name="remove" class="btn btn-danger btn-xs lap-remover" title="Click to Remove Line <?php echo $i+1; ?>"><i class="fa fa-fw fa-times"></i></button>			
					<?php } ?>
					<div class="row" style="padding:0;">
						<div class="line-title" style="display:none;">Line <?php echo $i+1; ?></div>
						<div class="col-md-2" style="padding:0;margin:0;">
							<?php 							
							if ($i==0) {
                                echo '<label>Item Code</label>';
                            }
							if(!empty(${"item_code".$i})){
								$oio = '<option value="'.${"item_code".$i}.'">'.${"item_code".$i}.'</option>';
							}else{
								$oio = '';//'<option value="">--Select--</option>';
							}
							echo '<select data-id="'.$i.'" id="item_code'.$i.'"name="item_code[]" style="padding:0; width:100%; '.${"item_code_border".$i}.$vop.'" class="popup-list form-control" name="">';
							echo $oio;
							// foreach($sun_select[0] as $row){
							// 	extract($row);
							// 	if($db_item_code == ${"item_code".$i}){
							// 		echo '<option selected value="'.$db_item_code.'" name="">'.$db_item_code.'</option>';
							// 	}else{
							// 		echo '<option value="'.$db_item_code.'" name="">'.$db_item_code.'</option>';
							// 	}
							// }
							echo '</select>';
							?>						
						</div>						
						<div class="col-md-6" style="padding:0;margin:0;">
							<?php 

							if ($i==0) {
                                echo '<label>Item Description</label>';
                            }
							if(!empty(${"item_description".$i})){
								$oio = '<option value="'.${"item_description".$i}.'">'.${"item_description".$i}.'</option>';
							}else{
								$oio = '';//'<option value="">--Select--</option>';
							}
							echo '<select data-id="'.$i.'" id="item_description'.$i.'" name="item_description[]" style="padding:0; width:100%; '.${"item_description_border".$i}.$vop.'" class="form-control popup-list" name="">';
							echo $oio;							
							echo '</select>';
							?>

						</div>
						<div class="col-md-2" style="padding:0;margin:0;">
							<?php

							if ($i==0) {
                                echo '<label>Quantity</label>';
                            }
							if(${"item_quantity".$i}==""){
								${"item_quantity".$i}=1;
							}
							?>
							<input autocomplete="off" style="<?php echo ${"item_quantity_border".$i}.$vop; ?>" type="number" min="1" name="item_quantity[]" value="<?php echo ${"item_quantity".$i}; ?>" class="form-control" id="<?php echo "qty".$i; ?>"/>
						</div>
						<div class="col-md-2" style="padding:0;margin:0;">
							<?php if ($i==0) {
                                echo '<label>Unit of Measure</label>';
                            } ?>
							<input style="<?php echo ${"uom_border".$i}.$vop; ?>" type="text" name="uom[]" id="<?php echo "uom".$i; ?>" value="<?php echo ${"uom".$i}; ?>" class="form-control"/>
						</div>
					</div>
				</div>
				<?php } ?>
				<input type="hidden" value="<?php echo $i; ?>" id="totalItems"/>
				<input type="hidden" value="0" id="reqId"/>

				<script type="text/javascript">
					$(document).ready(function(){
						$('.search-area').hide();
						$('.popup-list').click(function(e){
							e.preventDefault();
							e.stopPropagation();
							$('.search-area').show();
							form_data = new FormData();

							form_data.append('value', $(this).val());
							form_data.append('id', $(this).attr('data-id'));

							$.ajax({
						        url: '<?php echo return_url(); ?>ajax/search-sun-data.php',
						        type: "POST",
					            data: form_data,
					            contentType: false,
					            cache: false,
					            processData:false,
					            success: function(data){
					               $('.search-area').html(data);
						        }
						    });
							/////////////////////////////////////
						    
						});
					});
				</script>
				<div class="search-area">
					Loading...
				</div>
				<div class="pull-right" style="width:200px;margin-top:10px;"><button type="submit" name="add_more" class="btn btn-black btn-xs" style="background-color:black;color:white;font-size:12px;text-transform: uppercase;float:right;"><i class="fa fa-plus" style="font-size:10px;margin-right:2px; top:0;"></i> Add Line(s)</button><input name="add_more_number" value="1" type="number" style="height:20px; padding:0; width:40px;float:right;"/></div>
				<div class="clearfix" style="padding:0;margin:0;"></div>

			<div class="pull-left" style="">
				<button name="draft" type="submit" style="margin-top:0;" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save As Draft</button>
				<button name="forward" type="button" id="forward" style="margin-top:0;" class="btn btn-primary"><i class="fa fa-fw fa-plane"></i> Send for Approving</button>
				<span id="statusBtn"></span>
			</div>
			<div class="clearfix"></div>
			<div id="status" style="margin-top:20px;"></div>
			</div>
			<div class="col-md-3">

				<div class="item-line-group">
					
					<div class="row">
						<div class="line-title">Added Attachments</div>
						<div class="col-md-12"><br/>
							<span style="display:block;font-size:12px; color:black;font-weight:bold;">Attachment Name</span>
							<input id="flName" type="text" autocomplete="off" class="form-control" name="name" value="" placeholder="Attachment Name">
							<label style="margin-top:10px; padding:10px;border-radius:20px; cursor:pointer; background-color: #000;color:#fff;" for="fl" class=""><i class="fa fa-fw fa-plus"></i> Add Attachment </label>
							<input id="fl" hidden type="file" accept="application/pdf" style="display:none;" class="form-control" name="file" value="Add Attachment">
							<span style="display:block;font-size:12px; color:red;">Only PDFs, Less than 2MBs</span>
							<br/>
							<div id="flFile" style="overflow-wrap:anywhere;"></div>
							<div id="attachments">
								<?php 
								$user_id = user_id();
								$db = new Db();
								$select = $db->select("SELECT * FROM attachments WHERE at_added_by = '$user_id' AND at_req_id IS NULL");
								if(!$db->num_rows()){
									echo 'There are no attachments';
								}else{
									echo '<ol class="attachment-list">';
									
									if(is_array($select)){

									
									foreach($select as $row){
										extract($row);
										echo '<li id="list'.$at_id.'">';
										echo '<button type="button" data-id="'.$at_id.'" data-name="'.$at_name.'" class="btn-attachment-remove bg-danger text-danger"><i class="fa fa-fw fa-times"></i></button>';
										echo '&nbsp; ';
										echo '<a href="'.$at_path.'" target="_blank">'.$at_name.'</a>';
										
										echo '</li>';
									}
									}
									echo '</ol>';
								}
								?>
							</div>
						</div>
						<script>
							$(document).ready(function(){
								$('.btn-attachment-remove').click(function(){
									var attachmentID = $(this).attr("data-id");
									var attachmentName = $(this).attr("data-name");
									if(confirm('Do you really want to delete: '+attachmentName)){
										form_data = new FormData();
										form_data.append('attachmentID', attachmentID);

										$.ajax({
									        url: '<?php echo return_url(); ?>ajax/remove-document.php',
									        type: "POST",
								            data: form_data,
								            contentType: false,
								            cache: false,
								            processData:false,
								            success: function(data){
								               $('#list'+attachmentID).remove();
									        }
									    });
									}else{
										return false;
									}
								});
								$('#fl').click(function(){
									if($('#flName').val()==""){
										alert("Enter Attachment Name");
										return false;
									}else{
										//check if file name already exists
									}
								});
								function fileSize(input) {
								  if (input.files && input.files[0]) {
								    if(input.files[0].size > 2*1024*1024){
								    	alert("Attached Pdf should not exceed 2MBs");
								    	return false;
								    }else{
								    	return true;
								    }
								  }
								}
								$('#fl').change(function(){

									if(fileSize(this)){
										var doc = $('#fl').val().split("\\");
										$('#flFile').html(doc[doc.length - 1]);

										//file upload
										form_data = new FormData();
										wl = $('#fl').prop('files')[0];
										form_data.append('file', wl);
										form_data.append('type','fl');
										form_data.append('fileName',$('#flName').val());

										$.ajax({
									        url: '<?php echo return_url(); ?>ajax/documents.php',
									        type: "POST",
								            data: form_data,
								            contentType: false,
								            cache: false,
								            processData:false,
								            success: function(data){
								               //console.log(data);
								               $("#attachments").html(data);
									        }
									    });
									}

								});

							});
						</script>
									
					</div>
				</div>
			</div>
			</form>
		</div>

		<?php
		}
	}

	
	public function editRequisitionAction(){
		
		$db = new Db();
		$number = portion(3);
		$number2 = (int)portion(3);
		$select = $db->select("SELECT * FROM requisition WHERE req_number = '$number' OR req_id = '$number2'");
		if (is_array($select) && isset($select[0]) && is_array($select[0])) {
		extract($select[0]);}
		$sel = $db->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref'");
		$lll = $db->num_rows();
		$i = 0;
		if(is_array($sel){

		
		foreach($sel as $row){
			extract($row);
			${"item_code".$i} = $ri_code;
			${"item_quantity".$i} = $ri_quantity;
			${"uom".$i} = $ri_uom;
			//${"up".$i} = $ri_price;
			${"item_description".$i} = $ri_description;
			$i++;
		}
		}		

		$title = $req_title;
		$division = $req_division;
		$requisition_number = $req_number;
		if (empty($requisition_number)) {
            $requisition_number = $this->requisition_number();
        }
		if(isset($_POST['add_more'])){

			$title = $_POST['title']; $division = $_POST['division'];
			$item_code = $_POST['item_code'];
			$item_quantity = $_POST['item_quantity'];
			$uom = $_POST['uom'];
			$up = $_POST['up'];
			$item_description = $_POST['item_description'];

			for($i=0; $i<$_SESSION['line_number']; $i++){
				${"item_code".$i} = $item_code[$i];
				${"item_quantity".$i} = $item_quantity[$i];
				${"uom".$i} = $uom[$i];
				//${"up".$i} = $up[$i];
				${"item_description".$i} = $item_description[$i];
			}

			$add_more_number = $_POST['add_more_number'];
			if(isset($_SESSION['line_number'])){
				$_SESSION['line_number'] += $add_more_number;
			}else{
				$_SESSION['line_number'] = 1+$add_more_number;
			}
		}


		if(isset($_POST['remove'])){

			$title = $_POST['title']; $division = $_POST['division'];
			$item_code = $_POST['item_code'];
			$item_quantity = $_POST['item_quantity'];
			$uom = $_POST['uom'];
			//$up = $_POST['up'];
			$item_description = $_POST['item_description'];

			if (isset($_SESSION['line_number'])) {
                if($_SESSION['line_number'] != 1){
					$_SESSION['line_number'] -= 1;
				}
            } elseif ($numbers) {
                $_SESSION['line_number'] = $numbers-1;
            }

		}

		if(isset($_POST['remove']) || isset($_POST['add_more']) ||isset($_POST['draft'])||isset($_POST['forward']) ){
		//if(1){
			$title = $_POST['title']; $division = $_POST['division'];
			$item_code = $_POST['item_code'];
			$item_quantity = $_POST['item_quantity'];
			$uom = $_POST['uom'];
			$up = $_POST['up'];
			$item_description = $_POST['item_description'];

			for($i=0; $i<$_SESSION['line_number']; $i++){
				${"item_code".$i} = $item_code[$i];
				${"item_quantity".$i} = $item_quantity[$i];
				${"uom".$i} = $uom[$i];
				//${"up".$i} = $up[$i];
				${"item_description".$i} = $item_description[$i];
			}
		}

		if (isset($_POST['draft'])||isset($_POST['forward'])) {
            $db = new Db();
            $title = $_POST['title'];
            $division = $_POST['division'];
            $item_code = $_POST['item_code'];
            $item_quantity = $_POST['item_quantity'];
            $uom = $_POST['uom'];
            //$up = $_POST['up'];
            $item_description = $_POST['item_description'];
            if(isset($_POST['forward'])){
				if(empty($title)){
					$errors[] = "Enter Title";
					${"title_border"} = "border:1px solid red;";
				}else{
					${"title_border"} = "";							
				}

				if(empty($division)){
					$errors[] = "Select Division / Location";
					${"division_border"} = "border:1px solid red;";
				}else{
					${"division_border"} = "";							
				}
			}
            if(isset($_POST['draft'])||isset($_POST['forward'])){
				for($i=0; $i<$_SESSION['line_number']; $i++){

					$item = $item_code[$i];
					$qty = $item_quantity[$i];
					$measure = $uom[$i];
					//$price = $up[$i];
					$desc = $item_description[$i];

					if (isset($_POST['forward'])) {
                        if(empty($item)){
								$errors[] = "Check <b>Line ".($i+1)."</b>, Item code must not be empty";
								${"item_code_border".$i} = "border:1px solid red;";
							}else{
								${"item_code_border".$i} = "";							
							}
                        if(empty($qty)){
								$errors[] = "Check <b>Line ".($i+1)."</b>, Quantity must not be empty";
								${"item_quantity_border".$i} = "border:1px solid red;";
							}else{
								${"item_quantity_border".$i} = "";							
							}
                        if(empty($measure)){
								$errors[] = "Check <b>Line ".($i+1)."</b>, Unit of Measure must not be empty";
								${"uom_border".$i} = "border:1px solid red;";
							}else{
								${"uom_border".$i} = "";							
							}
                        // if(empty($price)){
                        // 	$errors[] = "Check <b>Line ".($i+1)."</b>, Price must not be empty";
                        // 	${"up_border".$i} = "border:1px solid red;";
                        // }else{
                        // 	${"up_border".$i} = "";							
                        // }
                        if(empty($desc)){
								$errors[] = "Check <b>Line ".($i+1)."</b>, Description must not be empty";
								${"item_description_border".$i} = "border:1px solid red;";
							}else{
								${"item_description_border".$i} = "";							
							}
                    }
				}

				if(empty($errors)){
					$ref = user_id().time();

					if(isset($_POST['draft'])){
						$insert = $db->update("requisition", ["req_title"=>$title, "req_division"=>$division, "req_added_by"=>user_id(), "req_date_added"=>time(), "req_status"=>-1],['req_id'=>$req_id]);
					}

					if(isset($_POST['forward'])){
						//======================================================
						$rr = $this->hod(user_id());
						$ad = $this->myDelegate($rr, time());
						$activeDelegate = $ad['delegate'];
						//========================================================
						$rg = $this->requisition_number();
						
						$insert = $db->update("requisition", ["req_number"=>$rg, "req_title"=>$title, "req_division"=>$division, "req_added_by"=>user_id(), "req_date_added"=>time(), "req_status"=>1, "req_delegate1"=>$activeDelegate, "req_delegator1"=>$this->hod(user_id()), "req_hod_id"=>$this->hod(user_id())],["req_id"=>$req_id]);
						//sending email to 
						echo $db->error();

						//================ SEND EMAIL T0 NEXT APPROVAL ===================

						//req_division === 
						$db = new Db();
						$select = $db->select("SELECT gr_name, apg_user, user_email FROM approval_group, approval_matrix, groups, sysuser WHERE ap_id = '$division' AND gr_matrix = ap_id AND gr_id = apg_name AND apg_user = user_id");
						
						$users_emails = array();
						//print_r($select);
						foreach($select as $row){
							extract($row);
							$users_emails[]=$user_email;
						}

						
						// if($activeDelegate){
						// 	$next = $activeDelegate;
						// 	$hod_name = $this->ruf($next, "user_othername");
						// 	$hod_telephone = $this->ruf($next, "user_telephone");
						// 	$hod_email = $this->ruf($next, "user_email");
						// 	$x = " (Delegation)";
						// }else{
						// 	$next = $this->hod(user_id());
						// 	$hod_name = $this->ruf($next, "user_othername");
						// 	$hod_telephone = $this->ruf($next, "user_telephone");
						// 	$hod_email = $this->ruf($next, "user_email");
						// }

						$to = $hod_email;
						$subject = "PENDING APPROVAL $x";
						
						$link = return_url()."requisition/view-requisition/".$rg;

						$message = "Hello ".$gr_name.",\n";
						$message .= "\r\n<br/><br/>You have a pending requisition $x with No.: <b>$rg</b>\r\n<br/><br/> ";
						$message .= "\r\nYou can use this link: <a href='$link'>$link</a>";
						
						FeedBack::sendmailz($users_emails,$subject,$message,$hod_name);
						//============================================================
					}

					//DELETE ALL OLD LINES AND INSERT NEW ONES
					$db->query("DELETE FROM requisition_item WHERE ri_ref = '$req_ref'");

					echo $db->error();
					for($i=0; $i<$_SESSION['line_number']; $i++){
						
						$item = $item_code[$i];
						$qty = $item_quantity[$i];
						$measure = $uom[$i];
						//$price = $up[$i];
						$desc = $item_description[$i];

						$insert = $db->insert("requisition_item", [
							"ri_code"=>$item,
							"ri_quantity"=>$qty,
							//"ri_price"=>$price,
							"ri_uom"=>$measure,
							"ri_description"=>$desc,
							"ri_ref"=>$req_ref,
						]);
						echo $db->error();
					}
					$db->query("UPDATE attachments SET at_req_id = '$req_ref' WHERE at_req_id IS NULL");
					echo $db->error();
				}else{
					Feedback::errors($errors);					
				}
			}
            if(isset($_POST['forward'])&&empty($errors)){
				Feedback::success();
				Feedback::refresh(3, return_url()."requisition/pending-requisitions");
				unset($_SESSION['line_number']);
			}elseif(isset($_POST['draft'])){
				Feedback::success();
				Feedback::refresh(3, return_url()."requisition/draft-requisitions");
				unset($_SESSION['line_number']);
			}
        } elseif (isset($_POST['remove']) || isset($_POST['add_more'])) {
            
        } else{
				$_SESSION['line_number']=$lll;
			}
		if (empty($_SESSION['line_number'])) {
            $_SESSION['line_number'] = 1;
        } 
		$line_number = (empty($_SESSION['line_number']))? 1 : $_SESSION['line_number'];

		?>
		<div class="row">
			<form action="" method="post" class="search-area-container">
			<div class="col-md-12" style="display:none;">
				<div class="row">
					<div class="col-md-3" style="margin-bottom:20px;">
						<label>Requisition Number</label>
						<input type="text" disabled class="form-control" name="requisition_number" value="<?php echo $this->requisition_number();?>">
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="row">					
					<div class="col-md-5" style="margin-bottom:20px;">
						<label>Location/Division</label>
						<select id="division" style="width:100%; <?php echo ${"division_border"}; ?>" name="division" class="select3">
							<option value="">Select</option>
							<?php 
							$db = new Db();
							$select = $db->select("SELECT * FROM approval_matrix ORDER BY ap_unit_code ASC");
							foreach($select as $row){
								extract($row);
								if ($division == $ap_id) {
                                    echo '<option selected="selected" value="'.$ap_id.'">'.$ap_unit_code.' - '.$ap_code.'</option>';
                                } else {
                                    echo '<option value="'.$ap_id.'">'.$ap_unit_code.' - '.$ap_code.'</option>';
                                }
							}
							?>
						</select>
					</div>
					<div class="col-md-7" style="margin-bottom:20px;">
						<label>Title</label>
						<input style="<?php echo ${"title_border"}; ?>" type="text" class="form-control" name="title" value="<?php echo $title; ?>" id="title">
					</div>
				</div>
				<?php for($i=0; $i< $line_number ; $i++){ ?>
				<?php 
				$vop = ($i%2!=0)?"background-color:#ebefff;padding:0 10px !important":"background-color:#fff;padding:0 10px !important;";
				// for($i=0; $i<1; $i++){
				?>				
				<div class="item-line-group" style="padding:0;margin:0;border:none;background-color: inherit;padding:0 30px;">
					<?php if($i!=0&&$i==$line_number-1){ ?>
					<button type="submit" style="margin-top: 0px;z-index: 10" name="remove" class="btn btn-danger btn-xs lap-remover" title="Click to Remove Line <?php echo $i+1; ?>"><i class="fa fa-fw fa-times"></i></button>			
					<?php } ?>
					<div class="row" style="padding:0 15px;">
						<div class="line-title" style="display:none;">Line <?php echo $i+1; ?></div>

						<div class="col-md-2" style="padding:0;margin:0;">
							<?php 
							if ($i==0) {
                                echo '<label>Item Code</label>';
                            }
							if(!empty(${"item_code".$i})){
								$oio = '<option value="'.${"item_code".$i}.'">'.${"item_code".$i}.'</option>';
							}else{
								$oio = '';//'<option value="">--Select--</option>';
							}
							echo '<select data-id="'.$i.'" id="item_code'.$i.'"name="item_code[]" style="padding:0; width:100%; '.${"item_code_border".$i}.$vop.'" class="popup-list form-control" name="">';
							echo $oio;
							// foreach($sun_select[0] as $row){
							// 	extract($row);
							// 	if($db_item_code == ${"item_code".$i}){
							// 		echo '<option selected value="'.$db_item_code.'" name="">'.$db_item_code.'</option>';
							// 	}else{
							// 		echo '<option value="'.$db_item_code.'" name="">'.$db_item_code.'</option>';
							// 	}
							// }
							echo '</select>';
							?>						
						</div>
						<div class="col-md-6"  style="padding:0;margin:0;">
							<?php 							
							if ($i==0) {
                                echo '<label>Item Description</label>';
                            }
							if(!empty(${"item_description".$i})){
								$oio = '<option value="'.${"item_description".$i}.'">'.${"item_description".$i}.'</option>';
							}else{
								$oio = '';//'<option value="">--Select--</option>';
							}
							echo '<select data-id="'.$i.'" id="item_description'.$i.'" name="item_description[]" style="padding:0; width:100%; '.${"item_description_border".$i}.$vop.'" class="form-control popup-list" name="">';
							echo $oio;							
							echo '</select>';
							?>

						</div>
						<div class="col-md-2" style="padding:0;margin:0;">
							<?php							
							if ($i==0) {
                                echo '<label>Quantity</label>';
                            }
							if(${"item_quantity".$i}==""){
								${"item_quantity".$i}=1;
							}
							?>
							<input autocomplete="off" style="<?php echo ${"item_quantity_border".$i}.$vop; ?>" type="number" min="1" id="<?php echo 'qty'.$i; ?>" name="item_quantity[]" value="<?php echo ${"item_quantity".$i}; ?>" class="form-control"/>
						</div>
						<div class="col-md-2"  style="padding:0;margin:0;">
							<?php 
							if ($i==0) {
                                echo '<label>Unit of Measure</label>';
                            }
							?>
							<input style="<?php echo ${"uom_border".$i}.$vop; ?>" type="text" name="uom[]" id="<?php echo "uom".$i; ?>" value="<?php echo ${"uom".$i}; ?>" class="form-control"/>
						</div>
					</div>
				</div>
				<?php } ?>
				<input type="hidden" value="<?php echo $i; ?>" id="totalItems"/>
				<input type="hidden" value="<?php echo $req_id; ?>" id="reqId"/>

				<script type="text/javascript">
					$(document).ready(function(){
						$('.search-area').hide();
						$('.popup-list').click(function(e){
							e.preventDefault();
							e.stopPropagation();
							$('.search-area').show();
							form_data = new FormData();

							form_data.append('value', $(this).val());
							form_data.append('id', $(this).attr('data-id'));

							$.ajax({
						        url: '<?php echo return_url(); ?>ajax/search-sun-data.php',
						        type: "POST",
					            data: form_data,
					            contentType: false,
					            cache: false,
					            processData:false,
					            success: function(data){
					               $('.search-area').html(data);
						        }
						    });
							/////////////////////////////////////
						    
						});
					});
				</script>
				<div class="search-area">
					Loading...
				</div>
				<div class="pull-right" style="width:200px;margin-top:10px;"><button type="submit" name="add_more" class="btn btn-black btn-xs" style="background-color:black;color:white;font-size:12px;text-transform: uppercase;float:right;"><i class="fa fa-plus" style="font-size:10px;margin-right:2px; top:0;"></i> Add Line(s)</button><input name="add_more_number" value="1" type="number" style="height:20px; padding:0; width:40px;float:right;"/></div>
				<div class="clearfix" style="padding:0;margin:0;"></div>

			<div class="pull-left" style="">
				<button name="draft" type="submit" style="margin-top:0;" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save As Draft</button>
				<button name="forward" type="button" id="forward" style="margin-top:0;" class="btn btn-primary"><i class="fa fa-fw fa-plane"></i> Send for Approving</button>
				<span id="statusBtn"></span>
			</div>
			
			<div class="clearfix"></div>
			<div id="status" style="margin-top:20px;"></div>
			</div>

			<div class="col-md-3">

				<div class="item-line-group">
					
					<div class="row">
						<div class="line-title">Added Attachments</div>
						<div class="col-md-12"><br/>
							<span style="display:block;font-size:12px; color:black;font-weight:bold;">Attachment Name</span>
							<input id="flName" type="text" autocomplete="off" class="form-control" name="name" value="" placeholder="Attachment Name">
							<label style="margin-top:10px; padding:10px;border-radius:20px; cursor:pointer; background-color: #000;color:#fff;" for="fl" class=""><i class="fa fa-fw fa-plus"></i> Add Attachment </label>
							<input id="fl" hidden type="file" accept="application/pdf" style="display:none;" class="form-control" name="file" value="Add Attachment">
							<span style="display:block;font-size:12px; color:red;">Only PDFs, Less than 2MBs</span>
							<br/>
							<div id="flFile" style="overflow-wrap:anywhere;"></div>
							<div id="attachments">
								<?php 
								$user_id = user_id();
								$db = new Db();
								$select = $db->select("SELECT * FROM attachments WHERE at_added_by = '$user_id' AND at_req_id IS NULL OR at_req_id = '$req_ref'");
								if(!$db->num_rows()){
									echo 'There are no attachments';
								}else{
									echo '<ol class="attachment-list">';
									foreach($select as $row){
										extract($row);
										echo '<li id="list'.$at_id.'">';
										echo '<button type="button" data-id="'.$at_id.'" data-name="'.$at_name.'" class="btn-attachment-remove bg-danger text-danger"><i class="fa fa-fw fa-times"></i></button>';
										echo '&nbsp; ';
										echo '<a href="'.$at_path.'" target="_blank">'.$at_name.'</a>';
										
										echo '</li>';
									}
									echo '</ol>';
								}
								?>
							</div>
						</div>
						<script>
							$(document).ready(function(){
								$('.btn-attachment-remove').click(function(){
									var attachmentID = $(this).attr("data-id");
									var attachmentName = $(this).attr("data-name");
									if(confirm('Do you really want to delete: '+attachmentName)){
										form_data = new FormData();
										form_data.append('attachmentID', attachmentID);

										$.ajax({
									        url: '<?php echo return_url(); ?>ajax/remove-document.php',
									        type: "POST",
								            data: form_data,
								            contentType: false,
								            cache: false,
								            processData:false,
								            success: function(data){
								               $('#list'+attachmentID).remove();
									        }
									    });
									}else{
										return false;
									}
								});
								$('#fl').click(function(){
									if($('#flName').val()==""){
										alert("Enter Attachment Name");
										return false;
									}else{
										//check if file name already exists
									}
								});
								function fileSize(input) {
								  if (input.files && input.files[0]) {
								    if(input.files[0].size > 2*1024*1024){
								    	alert("Attached Pdf should not exceed 2MBs");
								    	return false;
								    }else{
								    	return true;
								    }
								  }
								}
								$('#fl').change(function(){

									if(fileSize(this)){
										var doc = $('#fl').val().split("\\");
										$('#flFile').html(doc[doc.length - 1]);

										//file upload
										form_data = new FormData();
										wl = $('#fl').prop('files')[0];
										form_data.append('file', wl);
										form_data.append('type','fl');
										form_data.append('fileName',$('#flName').val());
										form_data.append('req_ref', <?php echo $req_ref; ?>);
										$.ajax({
									        url: '<?php echo return_url(); ?>ajax/documents.php',
									        type: "POST",
								            data: form_data,
								            contentType: false,
								            cache: false,
								            processData:false,
								            success: function(data){
								               //console.log(data);
								               $("#attachments").html(data);
									        }
									    });
									}

								});

							});
						</script>
									
					</div>
				</div>
			</div>
			</form>
		</div>

		<?php
	}

	public function draftRequisitionsAction(){
		new AccessRights();
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
			echo '<button class="btn btn-xs btn-danger" id="eagleResetBtn" data-url="'.return_url().'customers/all-customers" type="button"><i class="fa fa-fw fa-refresh"></i> Reset</button>';

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
		            url: $('#urlPath').val()+"ajax/_draft-requisitions-table.php",
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

	public function approvedRequisitionsAction(){
		new AccessRights();
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
		            url: $('#urlPath').val()+"ajax/_approved-requisitions-table.php",
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
	public function pendingRequisitionsAction(){
		new AccessRights();
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
		            url: $('#urlPath').val()+"ajax/_pending-requisitions-table.php",
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
	public function rejectedRequisitionsAction(){
		new AccessRights();
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
		            url: $('#urlPath').val()+"ajax/_rejected-requisitions-table.php",
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
		
	public function requisitionListAction(){
		//echo $this->notApproved2(user_id());
		//to APPROVALS
		$db = new Db();
		$user_id = user_id();
		$app = array();
		$app_id = $this->isApprovalNotDelegate(user_id());

		$div = $this->findDiv($user_id);

		// if($app_id==1){
		// 	$app[] = "req_app".$app_id."_user_id IS NULL ";
		// 	$app[] = "req_app".$app_id."_user_id IS NOT NULL ";
		// 	$first = ' (req_hod_id = '.user_id().'  OR req_delegate1 = '.user_id().')  AND ';
		// }else{
		// 	if(empty($app_id)){
		// 		$app[] = "(req_app1_user_id IS NULL AND req_delegate1 = '$user_id')";
		// 		$app[] = "(req_app1_user_id IS NOT NULL AND req_delegate1 = '$user_id')";
		// 	}else{
		// 		$app[] = "(req_app".$app_id."_user_id IS NULL AND req_app".($app_id-1)."_user_id IS NOT NULL )";
		// 		$app[] = "(req_app".$app_id."_user_id IS NOT NULL AND req_app".($app_id-1)."_user_id IS NOT NULL )";
		// 	}
		// 	//$first = ' (req_hod_id = '.user_id().'  OR req_delegate1 = '.user_id().')  AND ';
		// }

		// $app = '('.implode(' OR ', $app).')';

	
		$sql = "SELECT * FROM requisition WHERE req_status = '1' AND req_division = '$div' ORDER BY req_app4_user_id ASC, req_app3_user_id ASC, req_app2_user_id ASC, req_app1_user_id ASC,  req_date_added DESC";
		

		$select = $db->select($sql);
		if($db->num_rows()){
		$no = 1;
		echo '<table border="1" id="table">';
		echo '<tr>';
		echo '<th>No.</th>';
		echo '<th>Date</th>';
		echo '<th>Req. No.</th>';
		echo '<th>Title</th>';
		echo '<th>Total Lines</th>';
		echo '<th>Status</th>';
		echo '<th>Action</th>';
		echo '</tr>';
		foreach($select as $row){
			extract($row);
			echo '<tr>';
			echo '<td width="30px">'.($no++).'.</td>';
			echo '<td>'.Feedback::date_fm($req_date_added).'</td>';
			echo '<td>'.$req_number.'</td>';
			echo '<td>'.$req_title.'</td>';
			echo '<td>'.$this->total("requisition_item", "ri_ref", $req_ref).'</td>';

			$w = 1;
			////${"req_app".$w."_user_id"};

			if((${"req_app".$w."_user_id"}) ){
				echo '<td>Approved</td>';
			}elseif($gg){
				echo '<td>Approved</td>';
			}else{
				echo '<td><span style="background-color:orange; padding:1px 5px; border-radius:5px ">Pending Your approval</span></td>';
			}

			echo '<td><a href="'.return_url().'requisition/view-requisition/'.$req_number.'">View Details</a></td>';
			echo '</tr>';
		}
		echo '</table>';
		}else{
			echo 'No Data to Display';
		}
	}

	public function viewRequisitionAction(){

		// if(!$this->id){
        // 	$number = portion(3);
        // 	$number2 = (int)portion(3);
        // }else
        $number = $this->id;
        $number2 = $this->id;
        ///////////////////////////////////////////////////////////////
		if(!empty($number)){
			$db = new Db();
			$select = $db->select("SELECT * FROM requisition WHERE req_number = '$number' OR req_id = '$number2'");
			extract($select[0]);

			if (isset($_POST['status']) && $_POST['status']=="Rejected") {
                $comment = $_POST['comment'];
                $req = $_POST['id'];
                if(empty($comment)){
					$errors[] = "Please Enter comment for your Rejection";
					FeedBack::errors($errors);
				}else{
					//this->rejector("requisition", ["req_id"=>$req_id], ["req_app"=>$req]);

					//removing comment
					$n = new Db();
					$n->update("requisition", ["req_status"=>0, "req_app1_user_id"=>NULL, "req_app2_user_id"=>NULL, "req_app4_user_id"=>NULL, "req_app3_user_id"=>NULL], ["req_id"=>$req_id]);

					$n = new Db();
					$nn = $n->query("DELETE FROM comment WHERE comment_to = '$req_added_by' AND comment_part_id = '$req_id' AND comment_type = 'REQ' ");

					$nn = $n->insert("rejected_copy_master", [
						"rcm_comment"=>$comment,
						"rcm_date_added"=>time(),
						"rcm_added_by"=>user_id(),
						"rcm_rejected_by"=>user_id(),	
						"rcm_type_id"=>$req_id,
						"rcm_type"=>"REQ",
					]);

					//email notification 
					$next_name = $this->ruf($req_added_by, "user_othername");
					$next_telephone = $this->ruf($req_added_by, "user_telephone");
					$next_email = $this->ruf($req_added_by, "user_email");

					$message = "Hello ".ucwords(strtolower($next_name)).",\n\r <br/><br/>";

					if($this->check_de_status($next_id, "SMS")){
						$message .= "Your Requistion with No.: <b>$req_number</b> has been rejected by <b>".$this->full_name(user_id())."</b><br/>";
						$next_telephone = "###".$next_telephone;
						$next_telephone = str_replace("###07", "+2567", $next_telephone);
						FeedBack::sms($message,$next_telephone);
					}
						
					if($this->check_de_status($next_id, "EMAIL")){
						$to = $next_email;
						$subject = "REQUISIITON REJECTION";
						
						$link = return_url()."requisition/view-requisition/".$req_number;
						
						$message .= "\r\nYou can use this link: <a href='$link'>$link</a>";
						
						FeedBack::sendmail($to,$subject,$message,$next_name);
					}
					//-----------------------------------------------------------------

					Feedback::refresh(3, return_url()."requisition/requisition-list");
					Feedback::success("Rejection Complete");
				}
            } elseif (isset($_POST['send_btn'])) {
                $db = new Db();
                $comment = $_POST['comment'];
                $req = $_POST['i'];
                $insert = $db->insert("comment", 
				[
					"comment_from"=>user_id(),
					"comment_to"=>$req_added_by,
					"comment_message"=>"$comment",
					"comment_type"=>"REQ",
					"comment_date"=>time(),
					"comment_part_id"=>$req_id,
					"comment_level"=>$req,
				]
				);
                $level["req_app".$req."_user_id"]=user_id();
                $level["req_app".$req."_designation_id"]=$this->ruf(user_id(), "user_designation");
                $level["req_app".$req."_date"]=time();
                $update = $db->update("requisition", $level, ["req_id"=>$req_id]);
                //======== creating file ==============================
                $list = array();
                $list[] = array(
					"Date",
					"Item Code",
					"Quantity",
					"UoM",
					"Price",
					"Division",
					"Req. No.",
				);
                $db = new Db();
                $select = $db->select("SELECT * FROM requisition WHERE req_id = '$req_id'");
                extract($select[0]);
                $select = $db->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
                $no = 1;
                $total = 0;
                foreach($select as $row){
    				extract($row);
    				$list[]=array(
    					date('d/M/Y', $req_date_added),
    					$ri_code,
    					$ri_quantity,
    					$ri_uom,
    					//$ri_price,
    					$this->rgf("approval_matrix", $req_division, "ap_id", "ap_code"),
    					$req_number,
    				);
    			}
                $fp = fopen('StoreRequisitions/'.$req_number.'.csv', 'w');
                foreach ($list as $fields) {
					fputcsv($fp, $fields);
				}
                fclose($fp);
                //==================================================
                if(!$db->error()){
					$next =$this->nextApproval();
					if(0 !== 0){
						$hod_name = $this->ruf($next, "user_othername");
						$hod_telephone = $this->ruf($next, "user_telephone");
						$hod_email = $this->ruf($next, "user_email");

						$message = "Hello ".ucwords(strtolower($hod_name)).",\n";

						if($this->check_de_status($hod_id, "SMS")){
							$message .= " $pfar_number ";
							$hod_telephone = "###".$hod_telephone;
							$hod_telephone = str_replace("###07", "+2567", $hod_telephone);
							FeedBack::sms($message,$hod_telephone);
						}
							
						if($this->check_de_status($hod_id, "EMAIL")){
							$to = $hod_email;
							$subject = "REQUISITION APPROVAL";
							
							$link = return_url()."requisition/view-requisition/".$req_number;
							$message .= "\r\n<br/><br/>Please Check Requisition with No.: <b>$req_number</b> is pending approval.\r\n<br/><br/> ";
							$message .= "\r\nYou can use this link: <a href='$link'>$link</a>";
							
							FeedBack::sendmail($to,$subject,$message,$hod_name);
						}
					}else{
						$next = $req_added_by;
						$hod_name = $this->ruf($next, "user_othername");
						$hod_telephone = $this->ruf($next, "user_telephone");
						$hod_email = $this->ruf($next, "user_email");

						$message = "Hello ".ucwords(strtolower($hod_name)).",\n";

						if($this->check_de_status($hod_id, "SMS")){
							$message .= " $pfar_number ";
							$hod_telephone = "###".$hod_telephone;
							$hod_telephone = str_replace("###07", "+2567", $hod_telephone);
							FeedBack::sms($message,$hod_telephone);
						}
							
						if($this->check_de_status($hod_id, "EMAIL")){
							$to = $hod_email;
							$subject = "REQUISITION COMPLETION";
							
							$link = return_url()."requisition/view-requisition/".$req_number;
							$message .= "\r\n<br/><br/>Your requisition with No.: <b>$req_number</b> is successfully Approved.\r\n<br/><br/> ";
							$message .= "\r\nYou can use this link: <a href='$link'>$link</a>";
							
							FeedBack::sendmail($to,$subject,$message,$hod_name);
						}
					}

					FeedBack::success();
					FeedBack::refresh();

				}else{
					FeedBack::error($db->error());
				}
            }
			
		
			if($req_status != 1){
				echo '<a href="'.return_url().'requisition/edit-requisition/'.$req_id.'" class="btn btn-primary btn-xs sm"><i class="fa fa-fw fa-edit"></i> Edit '.$this->status($req_id).'</a>';
				//echo '>>'.$req_status;
			}
			/////////////////////////////////////////////////
			//echo '<label style="font-size:16px;">Requisition</label>';
			$this->start_print("STORE REQUISITION");
			echo '<div style="background-color:#FFF;padding:20px;border-radius:10px;">';
			echo '<style>table tr td{ font-size:12px !important; position:relative !important;} table tr th{font-size:12px !important; font-family:arial}</style>';
			echo '<div style="position:relative">';
        echo '<table cellpadding="4" width="100%"  style="font-size:12px;">';
            echo '<tr>';
                echo '<td>';                
                echo '<span style="color:blue;position:absolute;left:40px;font-weight:bold;">'.$this->rgf("approval_matrix", $req_division, "ap_id", "ap_unit_code").' - '.$this->rgf("approval_matrix", $req_division, "ap_id", "ap_code").'</span>';
                    echo 'From:_________________________________________(Branch/Dept)';
                echo '</td>';
                echo '<td style="position:relative;">';
                		echo '<span style="color:red;position:absolute;left:30px;font-weight:bold;">'.$req_number.'</span>';
                        echo '&nbsp;&nbsp;&nbsp;No:______________________';
                    echo '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td>';
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;To:_____________________';
                echo '</td>';
                echo '<td style="position:relative">';
                echo '<span style="color:blue;position:absolute;left:30px;font-weight:bold;">'.FeedBack::date_s($req_date_added).'</span>';
                        echo 'Date:______________________';
                    echo '</td>';
            echo '</tr>';
        echo '</table>';
        echo '<br>';

        echo '<table id="table" style="font-size:10px;  font-family:arial border:none;" cellpadding="1" cellspacing="0" width="100%">';
           
            echo '<tr>';
                echo '<th style="border-bottom:1px solid black;border-top:1px solid black;border-left:1px solid black;width:30px;">';
                        echo 'No:';
                    echo '</th>';
                echo '<th style="border-bottom:1px solid black;border-top:1px solid black;border-left:1px solid black;">';
                    echo 'NATURE OF GOODS';
                    echo '</th>';
                echo '<th style="width:100px;border-bottom:1px solid black;border-top:1px solid black;border-left:1px solid black;">';
                        echo 'CODE';
                    echo '</th>';
                echo '<th style="width:100px;border-bottom:1px solid black;border-top:1px solid black;border-left:1px solid black;">';
                        echo 'UNIT';
                    echo '</th>';
                echo '<th style="width:100px;border-bottom:1px solid black;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;">';
                        echo 'QUANTITY';
                    echo '</th>';
                // echo '<th style="border-bottom:1px solid black;border-top:1px solid black;border-left:1px solid black;">';
                //     echo 'QUALITY';
                // echo '</th>';
            echo '</tr>';

            $select = $db->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
            $no = 1;
            $total = 0;
            foreach($select as $row){
            	extract($row);
            echo '<tr style="font-size:10px;  font-family:arial">';
                echo '<td style="border-bottom:1px solid black;border-left:1px solid black;">';
                        echo '<span>'.($no++).'.</span>';
                    echo '</td>';
                echo '<td style="border-bottom:1px solid black;border-left:1px solid black;">';
                    echo '<span>'.$ri_description.'</span>';
                    echo '</td>';
                echo '<td style="border-bottom:1px solid black;border-left:1px solid black;">';
                        echo '<span>'.$ri_code.'</span>';
                    echo '</td>';
                echo '<td style="border-bottom:1px solid black;border-left:1px solid black;">';
                        echo '<span>'.$ri_uom.'</span>';
                    echo '</td>';
                echo '<td style="border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;">';
                        echo '<span>'.number_format($ri_quantity).'</span>';
                    echo '</td>';
                // echo '<td style="border-bottom:1px solid black;border-left:1px solid black;">';
                //         echo '&nbsp;';
                //     echo '</td>';
                   
            echo '</tr>';
        }
            
         
        echo '</table>';
        echo '<br>';
        echo '<table  style="font-size:12px;" cellpadding="4" padding="5" cellspacing="0" width="100%">';
            echo '<tr valign="top" style="height:30px;">';
                echo '<td style="width:10px;text-align:right;">';
                        echo 'Requisition(Name)';
                    echo '</td>';
                echo '<td>';
                    echo '';
                echo '</td>';
                echo '<td>';
                	echo '<span style="color:blue;position:absolute;left:10px;font-weight:bold;">'.$this->full_name($req_added_by).'</span>';
                    echo '__________________________________';
                echo '</td>';
                echo '<td style="width:10px;">';
                    echo '<span style="margin-left:20px;">Date: </span>';
                echo '</td>';
                echo '<td>';
                echo '<span style="color:blue;position:absolute;left:10px;font-weight:bold;">'.Feedback::date_fm($req_date_added).'</span>';
                    echo '__________________________________';
                echo '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td style="text-align:right;">';
                    echo str_replace(' ', '&nbsp;', 'Approved by (Name)');
                echo '</td>';
                echo '<td>';
                    echo '';
                echo '</td style="text-align:right;">';
                echo '<td style="width:10px;">';
                	echo '<span style="color:blue;position:absolute;left:10px;font-weight:bold;">'.$this->full_name($req_app1_user_id).'</span>';
                    echo '__________________________________';
                echo '</td>';
                echo '<td>';
                    echo '<span style="margin-left:20px;">Date: </span>';
                echo '</td>';
                echo '<td>';
                if ($req_app1_user_id) {
                    echo '<span style="color:blue;position:absolute;left:10px;font-weight:bold;">'.Feedback::date_fm($req_app1_date).'</span>';
                }
                echo '__________________________________';
                echo '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td style="text-align:right;">';
                    echo 'Title&nbsp;';
                    echo '</td>';
                echo '<td>';
                    echo '';
                echo '</td>';
                echo '<td>';
                	if ($req_app1_user_id) {
                        echo '<span style="color:blue;position:absolute;left:10px;font-weight:bold;">'.$this->rgf("designation", $req_app1_designation_id, "designation_id", "designation_name").'</span>';
                    }
                    echo '__________________________________';
                echo '</td>';
                echo '<td>';
                    echo '&nbsp;';
                    echo '</td>';
                echo '<td>';
                    echo '&nbsp;';
                    echo '</td>';
            echo '</tr>';
        echo '</table>';
        echo '<br>';
        echo '<table cellpadding="4" padding="5" cellspacing="0" width="100%">';
            echo '<tr style="height:40px">';
                echo'<td style="text-align:right;">';
                        echo 'DR:__________________________________';
                    echo '</td>';
                echo'<td style="text-align:right;">';
                    echo 'ISSUED BY:___________________________';
                    echo '</td>';
                echo'<td style="text-align:right;">';
                    echo 'Received:_______________________________';
                    echo '</td>';
                
            echo'</tr>';
            echo'<tr>';
                    echo '<td style="text-align:right;">';
                    	
                        echo 'CR:___________________________________';
                    echo '</td>';
                echo '<td style="text-align:right;">';
                    echo 'Date:____________________________________';
                    echo '</td>';
                echo '<td style="text-align:right;position:relative;">';
                if($req_status != 1){
                		if($req_status == "0"){
                    	echo '<div style="height:70px; overflow:hidden;font-size:50px;transform:rotate(-90deg);color:red;text-shadow:1px solid #000; position:absolute;right:-50px;bottom:0px;width:500px;font-weight:bold;">'.$this->status($req_id).'</div>';
                    	
                    	}else{
                    	echo '<div style="height:70px; overflow:hidden;font-size:50px;transform:rotate(-90deg);color:#969696;text-shadow:1px solid #000; position:absolute;right:-50px;bottom:0px;width:500px;font-weight:bold;">'.$this->status($req_id).'</div>';
                    	}
                    }
                    echo 'Date:________________________________';
                    echo '</td>';
            echo '</tr>';
        echo '</table>';  
        $this->end_print();
        echo '<br/><br/>';
        $sql = "SELECT * FROM attachments WHERE at_req_id = '$req_ref' ";
        $select = $db->select($sql);
        if($db->num_rows()){
        	echo '<b>Attachments:</b>';
        	echo '<ol>';
        	foreach($select as $row){
        		extract($row);
        		echo '<li><a href="'.$at_path.'" target="_blank">'.$at_name.'</a></li>';
        	}
        	echo '</ol>';
        }
        echo '</div>';
        echo '</div>';

        //////////////////////////////////
        $db = new Db();
		$xx = $db->select("SELECT * FROM rejected_copy_master WHERE rcm_type_id = '$req_id' AND rcm_type = 'REQ' ORDER BY rcm_date_added ASC");
		$i=1;
		if($db->num_rows()){
        echo '<div style="margin-top:20px;"></div>';
		echo '<label style="font-size:16px;">Rejections</label>';
		echo '<div>';
			$i=1;
			echo '<div style="padding-bottom:15px;" class="panel panel-success">';
			echo '<ol style="color:#000;list-style:none;padding:0; margin:0;">';
			foreach($xx as $row){
				if ($db->num_rows()) {
                    extract($row);
                }				
 
					if($req_status == 0){
						echo '<li style="border-bottom:1px solid #CCC;color:#000;padding:0 10px;"><h5>'.($i).'. Rejection ';
						echo ($i++);
						echo '</h5></li>';
						echo '<div style="padding:0 30px">';
						echo '<b>Rejected by: </b>'.$this->full_name($rcm_added_by).'';
						echo '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
						echo '<b>Date: </b>'.Feedback::date_fm($rcm_date_added).'';
						echo "<br/><b>Comment/Reason:</b><br/>";
						echo nl2br($rcm_comment);
						echo '<br/>';
						echo '</div>';
					}else{
						echo '<li style="border-bottom:1px solid #CCC;color:#000;padding:0 10px;"><h5>'.($i).'. Revised copy ';
						echo ($i++);
						echo '</h5></li>';
						echo '<div style="padding:0 30px">';
						echo '<b>Rejection by:</b> '.$this->full_name($rcm_added_by).'';
						echo '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
						echo '<b>Date:</b> '.Feedback::date_fm($rcm_date_added).'';
						echo "<br/><b>Comment/Reason:</b><br/>";
						echo nl2br($rcm_comment);
						echo '<br/>';
						echo '</div>';
					}
			}
			echo '</ol>';
			echo '</div>';
		}
		//echo '<br/>';
		echo '</div>';
        //////////////////////////////////

        if($req_status == 1){
        echo '<div style="margin-top:20px;"></div>';
		echo '<label style="font-size:16px;">Approval</label>';
		echo '<div style="padding-bottom:15px;padding-top:15px; border:1px solid #ccc; border-radius:10px; background-color:#FFF;">';
		

		$t = new Db();
		$tt = $t->select("SELECT * FROM approval_order");
		
		echo '<ol style="list-style:none;margin:0;padding:0;">';
		$i = 0;

		foreach($tt as $ttt){
			extract($ttt);
			if(!empty($app_role_id)){
				$i++;
				
				//echo '<br/><li style="border-bottom:1px solid #CCC;color:#000;padding:0 5px;"><b>'.$i.' . '.strtoupper($this->rgf("user_role", $app_role_id, "ur_id", "ur_name")).'</b></li>';
				
				if ($i==1) {
                    $from = $this->hod($req_added_by);
                }
				$from = $this->rgf("sysuser", $app_role_id, "user_designation", "user_id");
                //echo user_id()." == $tvr_driver_id";									
                $c = "req_app".$i."_user_id";
                // if($i==1){
                // 	$cb = "1";
                // }else{
                // 	$cb = ${"req_app".($i-1)."_user_id"};
                // }
                // if(${"req_delegate".$i}){
                // 	echo '<div>';
                // 	echo '<span style="margin-left:25px;"><B>Delegated to: </B>'.$this->full_name(${"req_delegate".$i}).'</span>';
                // 	echo ' &nbsp; &nbsp; ';
                // 	echo '<span style="margin-left:25px;"><B>Delegated by: </B>'.$this->full_name($req_hod_id).'</span>';
                // 	echo '</div>';
                // }
                if(empty($$c)){

			// 			$w = $this->isApproval($user_id);
			// ////${"req_app".$w."_user_id"};

			// for($v=4; $v>=1; $v--){
			// 	if(${"req_delegate".$v}==user_id() && ${"req_app".$w."_date"}){
			// 		$gg = 1;
			// 		break;
			// 	}else{
			// 		$gg = 0;
			// 	}
			// }

			// if((${"req_app".$w."_date"}==user_id())){
			// 	echo '<td>Approved</td>';
			// }elseif($gg){
			// 	echo '<td>Approved</td>';
			// }else{
			// 	echo '<td><span style="background-color:orange; padding:1px 5px; border-radius:5px ">Pending Your approval</span></td>';
			// }
						
						//@@@@
						if(empty($$c) && (in_array(user_id(), $this->divApproval($req_division)))){

							echo '<div class="col-md-6">';
							echo '<div  style="padding:15px; margin:10px 0; background-color:#fdfdfd; border:1px solid #cdcbd2;">';
							
							echo '<div class="demo-radio-button">';

							echo '<form action="" method="post" enctype="multipart/form-data">';
							echo '<input type="hidden" value="'.$req_id.'" id="reqId">';
							echo '<input type="hidden" name="return_id" value="'.$i.'"/>';
                        echo '<input name="status" checked="checked" type="radio" id="radio_7" value="Approved" class="radio-col-red" >';
                        echo '<label for="radio_7">Approve</label>';
                        echo '<input name="status" type="radio" id="radio_8" value="Rejected" class="radio-col-red">';
                        echo '<label for="radio_8">Reject</label>';
                        echo '</div>';

							echo '<input  type="hidden" value="'.$i.'" name="i"/>';

							echo '<label>Comment:</label>';
							echo '<textarea class="form-control" name="comment" id="comment"></textarea>';
							echo '<br/>';

							echo '<button type="button" id="approveBtn" class="btn btn-primary btn-xs" name="send_btn"><i class="fa fa-fw fa-save"></i> Post</button><span id="statusBtn"></span>';
							echo '</div>';
							echo '</div>';

							echo '</form>';

						}else{
							echo '<div style="margin-left:25px">';
							echo "Not Yet";
							echo '</div>';
						}								
						
					}else{
						//echo 'Approved by:';
						echo '<div style="margin:0; margin-left:25px">';

						echo "<b>Name:</b> ".$this->full_name(${"req_app".$i."_user_id"}).' &nbsp;  &nbsp;  &nbsp; ';	
						echo "<b>Designation:</b> ".$this->rgf("designation", ${"req_app".$i."_designation_id"}, "designation_id", "designation_name").' &nbsp;  &nbsp;  &nbsp; ';	
						echo '<b>Date:</b> &nbsp; '.FeedBack::date_fm(${"req_app".$i."_date"}).'';		
									

						$sql = "SELECT * FROM comment WHERE comment_to = '$req_added_by' AND comment_message != ''  AND comment_level = '$i' AND comment_type = 'REQ' AND comment_part_id = '$req_id'";
						$select1 = $db->select($sql);
						if ($db->num_rows()) {
                        echo '<br/><b>Comment:</b><br/>';
                    }
						foreach($select1 as $row){
							extract($row);
							echo nl2br($comment_message);
							echo '<br/>';
						}
						echo '</div>';	
						
					}

				echo '<div class="clearfix"></div>';

			}
		}
		echo '</ol>';


		echo '</div>';
	}

			/////////////////////////////////////////////////

		}else{
			//Feedback::error("error");
			$db = new Db();
			$number2 = (int)portion(3);
			$number = portion(3);
			$select = $db->select("SELECT req_number, req_id FROM requisition WHERE req_number = '$number' OR req_id = '$number2'");
			extract($select[0]);
			echo '<a href="'.return_url().'requisition/view-requisition/'.$req_id.'" class="eagle-load btn btn-xs btn-primary">View Requisition: '.$req_number.'</a>';
		}
	}

	public function rejected($user_id){
		
		$db = new db();
        $db->select("SELECT req_id FROM requisition WHERE req_added_by = $user_id AND req_status = 0 ");				
		return $db->num_rows();	
	}

	public function pending($user_id){
		
		$db = new db();
        $db->select("SELECT req_id FROM requisition WHERE req_added_by = $user_id AND req_status = 1 AND req_app1_date IS NULL ");				
		return $db->num_rows();	
	}

	public function draft($user_id){
		
		$db = new db();
        $db->select("SELECT req_id FROM requisition WHERE req_added_by = $user_id AND req_status = '-1' AND req_app1_date IS NULL ");				
		return $db->num_rows();	
	}

	public function isApproved($user_id, $req_id){
		
		$db = new db();
        $db->select("SELECT req_id FROM requisition WHERE req_added_by = $user_id AND req_status = 0 AND req_date_added > ".MONTHS_ACTIVE);				
		return $db->num_rows();	
	}

	public function notApproved2($user_id){
		
		$db = new db();
		$num = 0;

		$select = $db->select("SELECT req_division, req_number, req_app1_user_id, req_id,req_added_by, req_delegate1, req_date_added FROM requisition WHERE req_status = 1 AND req_date_added > ".MONTHS_ACTIVE);


		foreach($select as $row){
			extract($row); 
				
			if(empty($req_app1_user_id)){
				$da = $this->divApproval($req_division);

				if(in_array($user_id, $da)){
					$num++;
				}
			}	
			
		}
		
		return $num;
	}

	public function isApproval2($user_id){
		return 1;		
	}

	public function level($req_id, $count=0, $last=0){
		
		//pfar_app
		$u = new Db();
		$lev = array();
		for($i=0; $i<$count-1; $i++){
			$lev[] = "req_app".($i+1)."_user_id  IS NOT NULL ";
		}
		if($last==0){ 
		//$last = "NULL";
			$lev[]= "req_app{$count}_user_id IS NULL";
		}else{
			$lev[]= "req_app{$count}_user_id = $last";
		}
		$leve = implode(" AND ", $lev);

		$sql = "SELECT * FROM requisition WHERE $leve AND req_id = '$req_id'";

		$u->select($sql);
		
		if($u->num_rows()==1){
			return true;
		}else{
			return false;
		}
	}


	
}