<?php
Class PendingApprovals extends BeforeAndAfter{
	public $page = "PENDING APPROVALS";
	
	public function __construct(){
		$access = new AccessRights();
		//$access->pageAccess(user_id(), $this->page, 'V');
	}
	
	public static function getLinks(){
		$page = "PENDING APPROVALS";
		$links = array(
			array(
				"link_name"=>"Pending Approvals", 
				"link_address"=>"pending-approvals/requisitions",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"My Requisitions", 
				"link_address"=>"pending-approvals/view-requisitions",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			),
		);
		
		return $links;
	}
	
	
	
	public function requisitionsAction(){
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
		            url: $('#urlPath').val()+"ajax/_pending-requisitions-by-approvers-table.php",
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

	public function isApproved($user_id, $req_id){
		
		$db = new db();
		$num = 0;
		
		$select = $db->select("SELECT req_id FROM requisition WHERE req_added_by = $user_id AND req_status = 0 AND req_date_added > ".MONTHS_ACTIVE);				
		return $db->num_rows();	
	}

	public function notApproved2($user_id){
		$div = $this->findDiv($user_id);
		$db = new db();
		$num = 0;

		$sql = "SELECT * FROM requisition WHERE  req_status = 1 AND req_app1_date IS NULL AND req_division = '$div' $search";

		$select = $db->select($sql);


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

		$status = $u->select($sql);
		
		if($u->num_rows()==1){
			return true;
		}else{
			return false;
		}
	}

	public function levels(){
		$db = new Db();
		$select = $db->select("SELECT app_id FROM approval_order WHERE app_role_id != '0'");
		return $db->num_rows();		
	}
	

	public function isApproval($user_id){

		$t = $this->isDelegate($user_id, time());

		if(count($t)){
			$user_id = $t['by'];
			// echo '<pre>';
			// print_r($t);
			// echo '</pre>';
		}

		$db = new Db();
		$role_id = $this->rgf("sysuser", $user_id, "user_id", "user_role"); 
		$select = $db->select("SELECT app_id FROM approval_order WHERE app_role_id = '$role_id'");
		
		if($db->num_rows()==0){
			return 0;			
		}else{			
			extract($select[0]);
			return $app_id;			
		}
		
	}
}