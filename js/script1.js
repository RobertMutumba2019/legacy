function __(x){
	return document.getElementsByClassName(x);
}
function _(x){
	return document.getElementById(x);
}


function multipleChecker(){
	var allboxes = document.getElementsByClassName('AllCheckBoxes');

	var i=0;
	var checker = document.getElementById("checker");

	for(i=0; i<allboxes.length; i++){
		if(checker.innerHTML == "Check All"){
			allboxes[i].checked = true; 
		}else{			
			allboxes[i].checked = false; 
		}
	}

	if(checker.innerHTML == "Check All"){
		checker.innerHTML = "Uncheck All";
	}else{
		checker.innerHTML = "Check All";
	}
}

function multipleCheckerLine(number){
	var allboxes = document.getElementsByClassName('AllCheckBoxesLine'+number);

	var i=0;
	var checker = document.getElementById("checkerLine"+number);

	for(i=0; i<allboxes.length; i++){
		if(checker.innerHTML == "C"){
			allboxes[i].checked = true; 
		}else{			
			allboxes[i].checked = false; 
		}
	}

	if(checker.innerHTML == "C"){
		checker.innerHTML = "U";
	}else{
		checker.innerHTML = "C";
	}
}

function multipleCheckerColumn(number){
	var allboxes = document.getElementsByClassName('AllCheckBoxesColumn'+number);

	var i=0;
	var checker = document.getElementById("checkerColumn"+number);

	for(i=0; i<allboxes.length; i++){
		if(checker.innerHTML == "C"){
			allboxes[i].checked = true; 
		}else{			
			allboxes[i].checked = false; 
		}
	}

	if(checker.innerHTML == "C"){
		checker.innerHTML = "U";
	}else{
		checker.innerHTML = "C";
	}
}


$(document).ready(function(){
	var urlPath = $("#urlPath").val();
	$(document).off('keypress').on('keypress',function(e) {
	    if(e.which == 13) {
	    	e.preventDefault();
	        login();
	    }
	});

	$(document).off('click','#loginBtn').on('click', '#loginBtn', function(e){
		login();
	});

	function login(){
		var username = $('#username').val();
		var password = $('#password').val();
		//var captcha = $('#captcha').val();
		if(!username || !password ){
			alert("Please enter Username and Password");
		}else{
			$('#loginStatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/> Please wait...');
			$('#loginBtn').html("Checking Credentials");
			$('#loginBtn').attr("disabled", true);
			var form_data = new FormData();
			form_data.append('username', username);
			form_data.append('password', password);
			//form_data.append('captcha', captcha);
			$.ajax({
			url: urlPath+"ajax/login.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				var	values = JSON.parse(data);
				if (values.message=='Error') {
					$('#loginBtn').html("Sign in");
					$('#loginBtn').attr('disabled',false);
					$('#loginStatus').html('<div class="text-danger">Wrong Username, Password or Captcha Code</div>');
				}else{
					location.reload();
					$('#loginStatus').html('Logged in. Redirecting, Please wait.');
				}
			}
		});	
		}
	}



$('#submitBtn2').click(function(){
		var designation = $('#designation').val();
		//var user_id  = $('#user_id').val();
		var errors = new Array();
		alert();

		if(designation==""){
			errors.push("Please Enter Role Name");
		}
		if(errors.length >= 1){
			alert("Please Review the following:\n "+errors.join("\n"));
		}else{
			$('#roleStatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			//$(this).html("Adding Role");
			$(this).attr("disabled", true);
			var form_data = new FormData();
			form_data.append('designation', designation);
			//form_data.append('user_id', user_id);
			$.ajax({
			url: urlPath+"ajax/addRole.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				alert(data);
				var	values = JSON.parse(data);
				if (values.message=='Error') {
					$('#submitBtn2').html("AddRole");
					$('#submitBtn2').attr('disabled',false);
					$('#roleStatus').html('Not Added');
				}else{
					//location.reload();
					$('#designation').val("");
					$('#roleStatus').html('Successfully Saved');
				}
			}
		
		});	
		}

	});

$('#editBtn').click(function(){
		var designation = $('#designation').val();
		var ur_id  = $('#ur_id').val();
		var errors = new Array();

		if(designation==""){
			errors.push("Please Enter Role Name");
		}
		if(errors.length >= 1){
			alert("Please Review the following:\n "+errors.join("\n"));
		}else{
			$('#editrolestatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			//$(this).html("Editing Role");
			$(this).attr("disabled", true);
			var form_data = new FormData();
			form_data.append('designation', designation);
			form_data.append('ur_id', ur_id);
			$.ajax({
			url: urlPath+"ajax/editRole.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				// alert(data);
				var	values = JSON.parse(data);
				if (values.message=='Error') {
					$('#editBtn').html("Edit Role");
					$('#editBtn').attr('disabled',false);
					$('#editrolestatus').html('Not Added');
				}else{
					//location.reload();
					$('#editrolestatus').html('Successfully Saved');
				}
			}
		
		});	
		}

	});

$('#submitttBtn').off().on('click',function(e){
	e.preventDefault();
	if(!confirm("Are sure you want to save group?")){
		return false;
	}
		var groupname = $('#groupname').val();
		var matrix  = $('#matrix').val();
		var errors = new Array();

		if(!groupname){
			errors.push("Please Enter Group Name");
		}

		if(!matrix){
			errors.push("Please Select Matrix");
		}
		if(errors.length >= 1){
			alert("Please Review the following:\n "+errors.join("\n"));
		}else{
			$('#NewGroupStatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			$(this).html("Adding Group");
			$(this).attr("disabled", true);
			var form_data = new FormData();
			form_data.append('groupname', groupname);
			form_data.append('matrix', matrix);
			$.ajax({
			url: urlPath+"ajax/addGroup.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				// alert(data);
				var	values = JSON.parse(data);
				if (values.message=='Error') {
					$('#submitttBtn').html("Add Group");
					$('#submitttBtn').attr('disabled',false);
					$('#NewGroupStatus').html('Not Added');
				}else{
					//location.reload();
					$('#NewGroupStatus').html('Successfully Saved');
				}
			}
		
		});	
		}
	});


$('#editGroupbtn').click(function(){
		var groupname = $('#groupname').val();
		var matrix  = $('#matrix').val();
		var gr_id  = $('#gr_id').val();
		var errors = new Array();

		if(groupname==""){
			errors.push("Please Enter Group Name");
		}

		if(matrix==""){
			errors.push("Please Select Matrix");
		}
		if(errors.length >= 1){
			alert("Please Review the following:\n "+errors.join("\n"));
		}else{
			$('#editgroupstatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			$(this).html("Editing Group");
			$(this).attr("disabled", true);
			var form_data = new FormData();
			form_data.append('groupname', groupname);
			form_data.append('matrix', matrix);
			form_data.append('gr_id', gr_id);
			$.ajax({
			url: urlPath+"ajax/editGroup.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				// alert(data);
				var	values = JSON.parse(data);
				if (values.message=='Error') {
					$('#editGroupbtn').html("Edit Group");
					$('#editGroupbtn').attr('disabled',false);
					$('#editgroupstatus').html('Not Added');
				}else{
					location.reload();
					$('#editgroupstatus').html('Successfully Saved');
				}
			}
		
		});	
		}

	});





});

$(function(){

	var urlPath = $("#urlPath").val();

//$('#EditUserToGroup').off('click').on('click',function(e){
$(document).off('click','#EditUserToGroup').on('click', '#EditUserToGroup', function(e){
	e.preventDefault();
	if(!confirm('Are you want to Edit User to Group')){
		return false;
	}
	$('#EditUserToGroup').attr('disabled', true);
	var group = $('#group').val();
	var user  = $('#user').val();
	var ggid  = $('#ggid').val();

	var errors = new Array();

	if(group==""){
		errors.push("Select Group");
	}
	if(user==""){
		errors.push("Select User");
	}

	if(errors.length >= 1){
		$('#status').html('');
		swal({
			title: "Please review the following:",
			text:errors.join('\n'),
			type:"error",
			icon:"error",
		});
		$('#EditUserToGroup').attr('disabled', false);
	}else{
		$(this).attr("disabled", true);
		$('#status').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			
		var form_data = new FormData();

		form_data.append('user', user);
		form_data.append('group', group);
		form_data.append('ggid', ggid);

		$.ajax({
			url: urlPath+"ajax/addUserToGroup.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				var	values = JSON.parse(data);
				$('#EditUserToGroup').attr('disabled', false);
				$('#status').html('');

				if(values.message=="Error"){
					swal({
						title: "Please review the following:",
						text:values.details,
						type:"error",
						icon:"error",
					});
				}else{
					swal({
					    text: 'User Added',
					    type: "success",
					    icon: "success",
					}).then(function() {
					   // location.reload();
					});
				}
			}

		});
	}
});
	//$('#approveBtn').off('click').on('click',function(e){	
	$(document).off('click','#approveBtn').on('click', '#approveBtn', function(e){

		e.preventDefault();
		
		$(this).attr('disabled', true);

		var reqId = $('#reqId').val();
		var comment = $('#comment').val();
		var status = $("input[name='status']:checked").val();
		var reqId = $('#reqId').val();
		//alert(option+" >>>>>>>>>>>>>> ");
		if(status == "Rejected" && !comment){
			if(!confirm('Reject requisition?')){
				$(this).attr('disabled', false);
				return false;
			}

			$("#statusBtn").html('<img src="'+urlPath+'images/loading.gif" alt="loading" /> Please wait');
			swal({
				text: "Enter Comment, its mandatory for Rejection",
				type: "error",
			});	
			$(this).attr('disabled', false);
			$("#statusBtn").html('');		
		}else{
			if(!confirm('Approve requisition?')){
				$(this).attr('disabled', false);
				return false;
			}
			$("#statusBtn").html('<img src="'+urlPath+'images/loading.gif" alt="loading" /> Please wait');
			$(this).attr('disabled', true);

			var form_data = new FormData();
			form_data.append('status', status);
			form_data.append('comment', comment);
			form_data.append('reqId', reqId);

			$.ajax({
				url: urlPath+"ajax/saveApproval.php",
				type: "POST",
				data: form_data,
				contentType: false,
				cache: false,
				processData:false,
				success: function(data){
					$('#approveBtn').attr('disabled',true);
					$("#statusBtn").html('');
										
					swal({
					    text: data,
					    type: "success",
					    icon: "success",
					}).then(function() {
					    //location.replace(urlPath+"pending-approvals/requisitions");
					});				
					
				}
			});
		}
	});
	//$('.eagle-load').off('click').on('click',function(e){
	
	$(document).off('click','.eagle-load').on('click', '.eagle-load', function(e){
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

//$('#AccsettingBtn').off('click').on('click',function(e){
$(document).off('click','#AccsettingBtn').on('click', '#AccsettingBtn', function(e){
	e.preventDefault();
	if(!confirm('Are you sure you want to change password?')){
		return false;
	}
		var email_password = $('#email_password').val();
		var password  = $('#password').val();
		var cpassword  = $('#cpassword').val();
		var username = $('#username').val();
		var errors = new Array();
		if(username==""){
			errors.push("Please Enter Username");
		}
		if(email_password==""){
			errors.push("Please Enter Old Password");
		}
		if(password==""){
			errors.push("Please Enter a New Password");
		}
		if(cpassword==""){
			errors.push("Please Confirm Your New Password");
		}
		// if($cpassword !== $password){
		// 	errors.push("New password does not Match with Confirm password");
		// }
		if(errors.length >= 1){
			swal({
				title: "Please review the following:",
				text:errors.join('\n'),
				type:"error",
				icon:"error",
			});
		}else{
			$('#settingStatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			//$(this).attr("disabled", true);
			var form_data = new FormData();
			form_data.append('email_password', email_password);
			form_data.append('password', password);
			form_data.append('cpassword', cpassword);
			form_data.append('username', username);
			$.ajax({
			url: urlPath+"ajax/userSettings.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				// alert(data);
				var	values = JSON.parse(data);
				if (values.message=='Error') {
					$('#AccsettingBtn').attr('disabled',false);
					$('#settingStatus').html('Not Changed');
					swal({
						text:'Not Changed. '+ values.details,
						type:"error",
						icon:"error",
					}).then(function() {
					    //location.reload();
					});	
				}else{
					$('#addUserStatus').html('Successfully Saved');								
					swal({
					    text: 'You are required to Login using the new password. \nYour Password has been changed and sent to you email address',
					    type: "success",
					    icon: "success",
					}).then(function() {
					    location.reload();
					});
					$('#settingStatus').html('Successfully Saved');
					$('#AccsettingBtn').attr('disabled',false);
				}
			}
		
		});	
		}

	});



	//$('#addUserBtn').off('click').on('click',function(e){	
	$(document).off('click','#addUserBtn').on('click', '#addUserBtn', function(e){
		e.preventDefault();

		if(!confirm('Are you sure you want to save.')){
			return false;
		}

		var check_number = $('#check_number').val();
		var user_role = $('#user_role').val();
		//var check_number = $('#check_number').val();
		var surname = $('#surname').val();
		var othername = $('#othername').val();
		var telephone = $('#telephone').val();
		var email = $('#email').val();
		var user_gender = $('#user_gender').val();

		var errors = new Array();

		if(check_number==""){
			errors.push("please enter PF No");
		}
		if(user_role==""){
			errors.push("please enter role Name");
		}
		if(surname==""){
			errors.push("please enter surname");
		}
		if(othername==""){
			//errors.push("please enter othername");
		}
		// if(telephone==""){
		// 	errors.push("please enter telephone");
		// }
		if(email==""){
			errors.push("please enter email address");
		}
		if(user_gender==""){
			errors.push("please enter gender");
		}

		if(errors.length >= 1){
			alert("Please Review the following:\n "+errors.join("\n"));
		}else{
			$('#addUserStatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			//$(this).html("Adding User");
			$(this).attr("disabled", true);
			var form_data = new FormData();
			form_data.append('check_number', check_number);
			form_data.append('user_role', user_role);
			form_data.append('surname', surname);
			form_data.append('telephone', telephone);
			form_data.append('othername', othername);
			form_data.append('email', email);
			form_data.append('user_gender', user_gender);
			$.ajax({
			url: urlPath+"ajax/addsystemuser.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,

			success: function(data){
				var	values = JSON.parse(data);
				if (values.message=='Error') {
					$('#addUserBtn').html("Adding User");
					$('#addUserBtn').attr('disabled',false);
					$('#addUserStatus').html('Not Added. '+ values.details);							
					swal({
						text:'Not Added. '+ values.details,
						type:"error",
						icon:"error",
					}).then(function() {
					    //location.reload();
					});	
				}else{						
					$('#addUserStatus').html('');								
					swal({
					    text: 'Successfully Saved',
					    type: "success",
					    icon: "success",
					}).then(function() {
					    $('#check_number').val('');
						$('#user_role').val('');
						$('#surname').val('');
						$('#othername').val('');
						$('#telephone').val('');
						$('#email').val('');
						$('#user_gender').val('');
						$('#addUserBtn').attr('disabled', false);
					});						
				}
			}
		
		});	
		}

	});


	//$('#editUserBtn').off('click').on('click',function(e){
	$(document).off('click','#editUserBtn').on('click', '#editUserBtn', function(e){
		e.preventDefault();

		if(!confirm('Are you sure you want to save.')){
			return false;
		}

		var check_number = $('#check_number').val();
		var user_role = $('#user_role').val();
		//var check_number = $('#check_number').val();
		var surname = $('#surname').val();
		var othername = $('#othername').val();
		var telephone = $('#telephone').val();
		var email = $('#email').val();
		var user_gender = $('#user_gender').val();
		var user_id  = $('#user_id').val();
		var change_password = $('#change_password').val();
		var status = $('#status').val();

		var errors = new Array();

		if(check_number==""){
			errors.push("please enter PF No");
		}
		if(user_role==""){
			errors.push("please enter role Name");
		}
		if(surname==""){
			errors.push("please enter surname");
		}
		if(othername==""){
			//errors.push("please enter othername");
		}
		// if(telephone==""){
		// 	errors.push("please enter telephone");
		// }
		if(email==""){
			errors.push("please enter email address");
		}
		if(user_gender==""){
			errors.push("please enter gender");
		}

		if(errors.length >= 1){
			alert("Please Review the following:\n "+errors.join("\n"));
		}else{
			$('#editStatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			$(this).html("Editing User");
			$(this).attr("disabled", true);
			var form_data = new FormData();
			form_data.append('check_number', check_number);
			form_data.append('user_role', user_role);
			form_data.append('surname', surname);
			form_data.append('telephone', telephone);
			form_data.append('othername', othername);
			form_data.append('email', email);
			form_data.append('user_gender', user_gender);
			form_data.append('user_id', user_id);
			form_data.append('change', change_password);
			form_data.append('status', status);
			$.ajax({
			url: urlPath+"ajax/editUser.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				$('#editUserBtn').attr('disabled',false);
				var	values = JSON.parse(data);
				if (values.message=='Error') {
					$('#editUserBtn').html("Edit User");
					$('#editStatus').html('Not Edited. '+ values.details);
					swal({
						text:'Not Added. '+ values.details,
						type:"error",
						icon:"error",
					}).then(function() {
					    //location.reload();
					});	
				}else{
					$('#editStatus').html('Successfully Reset');							
					swal({
					    text: 'Successfully Reset',
					    type: "success",
					    icon: "success",
					}).then(function() {
					    //location.reload();
					});
				}
			}
		
		});	
		}

	});


	//$('#forward').off('click').on('click',function(e){
	$(document).off('click','#forward').on('click', '#forward', function(e){
		//alert("sdf");
		e.preventDefault();

		if(!confirm('Are you sure you want to forward this requisition')){
			return false;
		}
		var division = $('#division').val();
		var title = $('#title').val();
		var totalItems = parseInt($('#totalItems').val());
		$(this).attr('disabled', true);
		$("#statusBtn").html('<img src="'+urlPath+'images/loading.gif" alt="loading" /> Please wait');
		if(totalItems==0){
			alert("Please Select Some Items.");
		}else{			
			var errors = Array();	
			if(!title){
				errors.push("Enter Title");
			}				
			if(!division){
				errors.push("Enter Division");
			}

			for(i=0; i<totalItems; i++){				
				var uom = $('#uom'+i).val();			
				var qty = parseInt($('#qty'+i).val());			
				var item_description = $('#item_description'+i).val();			
				var item_code = $('#item_code'+i).val();
					//errors.push(item_description+'===='+item_code);
				if(!item_description||!item_code){
					errors.push("Line "+(i+1)+": Select Item Code and description");
				}	
				if(!qty){
					errors.push("Line "+(i+1)+": Enter Qantity");
				}
				if(!uom){
					errors.push("Line "+(i+1)+": Enter Unit of Measure");
				}	
			}
		}

		if(errors.length > 0){
			$(this).attr('disabled', false);
			$("#statusBtn").html('');
			swal({
				title: "Please review the following:",
				text:errors.join('\n'),
				type:"error",
				icon:"error",
			});
			//$("#status").html("<b>Please Review the following:</b><br/><ol><li>"+errors.join("</li><li>")+"</li></ol>").addClass("alert alert-danger");
		}else{			
			$(this).attr('disabled', true);
			$("#status").html("").removeClass("alert alert-danger");
			var form_data = new FormData();
			form_data.append('title', title);
			form_data.append('division', division);
			form_data.append('totalItems', totalItems);
			form_data.append('reqId', $('#reqId').val());

			for(i=0;i<totalItems; i++){
				form_data.append('uom'+i, $('#uom'+i).val());			
				form_data.append('qty'+i, $('#qty'+i).val());			
				form_data.append('item_description'+i, $('#item_description'+i).val());			
				form_data.append('item_code'+i,$('#item_code'+i).val());
			}

			$.ajax({
				url: urlPath+"ajax/saveRequisition.php",
				type: "POST",
				data: form_data,
				contentType: false,
				cache: false,
				processData:false,
				success: function(data){
					$('#forward').attr('disabled',true);
					$("#statusBtn").html('');
					if (data == 'Error') {
						$('#status').html("Error").addClass("alert alert-danger");;
						// $('#loginBtn').attr('disabled',false);
					}else{						
						swal({
						    text: "Successfully Sent",
						    type: "success",
						    icon: "success",
						}).then(function() {
						    //location.replace(urlPath+"requisition/pending-requisitions");
						    $('#division').val("");
							$('#title').val("");

							for(i=0;i<totalItems; i++){
								$('#uom'+i).val("");			
								$('#qty'+i).val("");			
								$('#item_description'+i).val("");			
								$('#item_code'+i).val("");
							}
							$('#forward').attr('disabled',false);
						});
						
					}
				}
			});
		}
	});
	

//$('#AddUserToGroup').off('click').on('click',function(e){
$(document).off('click','#AddUserToGroup').on('click', '#AddUserToGroup', function(e){
	e.preventDefault();
	if(!confirm('Are you want to Add User to Group')){
		return false;
	}
	$('#AddUserToGroup').attr('disabled', true);
	var group = $('#group').val();
	var user  = $('#user').val();

	var errors = new Array();

	if(group==""){
		errors.push("Select Group");
	}
	if(user==""){
		errors.push("Select User");
	}

	if(errors.length >= 1){
		$('#status').html('');
		swal({
			title: "Please review the following:",
			text:errors.join('\n'),
			type:"error",
			icon:"error",
		});
		$('#AddUserToGroup').attr('disabled', false);
	}else{
		$(this).attr("disabled", true);
		$('#status').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			
		var form_data = new FormData();

		form_data.append('user', user);
		form_data.append('group', group);

		$.ajax({
			url: urlPath+"ajax/addUserToGroup.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				var	values = JSON.parse(data);
				$('#AddUserToGroup').attr('disabled', false);
				$('#status').html('');

				if(values.message=="Error"){
					swal({
						title: "Please review the following:",
						text:values.details,
						type:"error",
						icon:"error",
					});
				}else{
					swal({
					    text: 'User Added',
					    type: "success",
					    icon: "success",
					}).then(function() {
					    $('#group').val("");
						$('#user').val("");
					});
				}
			}

		});
	}
});
$(document).off('click','#addApprovalMatrixBtn').on('click', '#addApprovalMatrixBtn', function(e){
	e.preventDefault();
	if(!confirm('Are you want to save Approval Matrix')){
		return false;
	}
	$('#addApprovalMatrixBtn').attr('disabled', true);
		var code = $('#code').val();
		var unit_name  = $('#unit_name').val();
		var errors = new Array();

	if(code==""){
		errors.push("Enter Code");
	}
	if(unit_name==""){
		errors.push("Enter Unit Name");
	}

	if(errors.length >= 1){
		$('#addApprovalMatrixStatus').html('');
		swal({
			title: "Please review the following:",
			text:errors.join('\n'),
			type:"error",
			icon:"error",
		});
		$('#addApprovalMatrixBtn').attr('disabled', false);
	}else{
		$(this).attr("disabled", true);
		$('#addApprovalMatrixStatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			
		var form_data = new FormData();

		form_data.append('code', code);
		form_data.append('unit_name', unit_name);

		$.ajax({
			url: urlPath+"ajax/addApprovalMatrix.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				var	values = JSON.parse(data);
				$('#addApprovalMatrixBtn').attr('disabled', false);
				$('#addApprovalMatrixStatus').html('');

				if(values.message=="Error"){
					swal({
						title: "Please review the following:",
						text:values.details,
						type:"error",
						icon:"error",
					});
				}else{
					swal({
					    text: 'Successfully Added',
					    type: "success",
					    icon: "success",
					}).then(function() {
					    //location.reload();
					    $('#code').val("");
						$('#id').val("");
						$('#unit_name').val("");
					});
				}
			}

		});
	}
});
$(document).off('click','#editApprovalMatrixBtn').on('click', '#editApprovalMatrixBtn', function(e){
	e.preventDefault();
	if(!confirm('Are you want to save Approval Matrix')){
		return false;
	}
	$('#editApprovalMatrixStatus').attr('disabled', true);
		var code = $('#code').val();
		var id = $('#id').val();
		var unit_name  = $('#unit_name').val();
		var errors = new Array();

	if(code==""){
		errors.push("Enter Code");
	}
	if(unit_name==""){
		errors.push("Enter Unit Name");
	}

	if(errors.length >= 1){
		$('#addApprovalMatrixStatus').html('');
		swal({
			title: "Please review the following:",
			text:errors.join('\n'),
			type:"error",
			icon:"error",
		});
		$('#editApprovalMatrixBtn').attr('disabled', false);
	}else{
		$(this).attr("disabled", true);
		$('#editApprovalMatrixStatus').html('<img src="'+urlPath+'images/loading.gif" alt="loading..."/>');
			
		var form_data = new FormData();

		form_data.append('id', id);
		form_data.append('code', code);
		form_data.append('unit_name', unit_name);

		$.ajax({
			url: urlPath+"ajax/editApprovalMatrix.php",
			type: "POST",
			data: form_data,
			contentType: false,
			cache: false,
			processData:false,
			success: function(data){
				var	values = JSON.parse(data);
				$('#editApprovalMatrixBtn').attr('disabled', false);
				$('#editApprovalMatrixStatus').html('');

				if(values.message=="Error"){
					swal({
						title: "Please review the following:",
						text:values.details,
						type:"error",
						icon:"error",
					});
				}else{
					swal({
					    text: 'Successfully Updated',
					    type: "success",
					    icon: "success",
					}).then(function() {
					    //location.reload();
					});
				}
			}

		});
	}
});

});