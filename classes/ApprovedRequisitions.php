<?php
Class ApprovedRequisitions extends BeforeAndAfter{
	public $page = "APPROVED REQUISITIONS";
	
	public function __construct(){
		new AccessRights();
		//$access->pageAccess(user_id(), $this->page, 'V');
	}
	
	public static function getLinks(){
		$page = "APPROVED REQUISITIONS";
		
		return array(
			array(
				"link_name"=>"New Requisition", 
				"link_address"=>"requisition/approved-requisitions",
				"link_icon"=>"fa-plus",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"My Requisitions", 
				"link_address"=>"requisition/view-requisitions",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			),
		);
	}
	
	public function requisitionsAction(){
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
		            url: $('#urlPath').val()+"ajax/_approved-requisitions-by-approvers-table.php",
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
	public function old(){
		$db = new Db();
		$user_id = user_id();
		
		//$app_id = $this->isApprovalNotDelegate(user_id());
		$div = $this->findDiv($user_id);
	
		$sql = "SELECT * FROM requisition WHERE req_status = '1' AND req_division = '$div' AND req_app1_user_id IS NOT NULL ORDER BY req_app4_user_id ASC, req_app3_user_id ASC, req_app2_user_id ASC, req_app1_user_id ASC,  req_date_added DESC";
		
		
		$query = array(
			"select"=>array("*"),
			"tables"=>array("requisition"),
			"whereAnd"=>array(
				//"req_added_by = '$user_id'", 
				//"req_status != 0 ", 
				"req_status = '1'",
				"req_app1_date IS NOT NULL",
				//"req_pushed IS NOT NULL",
				"req_division = '$div'"
			),
			"order"=>array(
				"req_status"=>"ASC",
				"req_app4_user_id"=>"ASC",
				"req_app3_user_id"=>"ASC",
				"req_app2_user_id"=>"ASC",
				"req_app1_user_id"=>"ASC",
				"req_date_added"=>"ASC",
			),
		);

        $numPerPage = 20;
        $start = portion(3);
        $searchWord = portion(5);
        $check = portion(5);
        $url = return_url().'requisition/pending-requisitions/';
        $searchColumns = array('req_title'=>'Requisition Title', 'req_number'=>'Number');

        $p = new Pagination();
        $select = $p->setUp($check,$searchColumns, $searchWord, $url, $query, $numPerPage, $start);
        //print_r($select);
        echo $p->search();   
			
		
		//$select = $db->select($sql);
		if(count($select) > 0){
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

		echo $p->links();
	}
}