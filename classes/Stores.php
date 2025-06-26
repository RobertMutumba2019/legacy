
<?php
Class Stores extends BeforeAndAfter{
	public $page = "STORES";
	
	public function __construct(){
		new AccessRights();
		
	}
	
	
	public static function getLinks(){
		$page = "STORES";
		
		return array(
			array(
				"link_name"=>"pending", 
				"link_address"=>"stores/pending",
				"link_icon"=>"fa-circle",
				"link_page"=>$page,
				"link_right"=>"A",
			),
			array(
				"link_name"=>"Pushed to SUN", 
				"link_address"=>"stores/pushed",
				"link_icon"=>"fa-check",
				"link_page"=>$page,
				"link_right"=>"V",
			),
		);
	}
	
	public function deletegroupAction(){
		$id = portion(3);
		$this->deletor("groups", "gr_id",  $id, 'groups/all-groups');
	}

    public function pendingAction(){
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
            //echo '<button class="btn btn-xs btn-danger" id="eagleResetBtn" data-url="'.return_url().'stores/pending" type="button"><i class="fa fa-fw fa-refresh"></i> Reset</button>';
            echo '<a class="eagle-load btn btn-xs btn-danger" id="eeagleResetBtn" href="'.return_url().'stores/pending"><i class="fa fa-fw fa-refresh"></i> Reset</a>';

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
                    url: $('#urlPath').val()+"ajax/_pending-table.php",
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
    
    public function pushedAction(){
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
            echo '<a class="eagle-load btn btn-xs btn-danger" id="eeagleResetBtn" href="'.return_url().'stores/pushed"><i class="fa fa-fw fa-refresh"></i> Reset</a>';

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
                    url: $('#urlPath').val()+"ajax/_pushed-table.php",
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
    
    public function pendingOLDAction(){

    require_once(__DIR__ . "/http://localhost:8080/myPHPapp/java/Java.inc");
    $world = new java("HelloWorld");

    $db = new Db();

    $errors = array();

    if(isset($_POST['monthDetails'])){
            $month = $_POST['month'];
            $year = $_POST['year'];

            if(empty($year)||empty($month)){
                FeedBack::error("Please select Year and Month");
            }else{

                if($year == "all"){
                    $start_date = strtotime('2020-10-20 12:00:00 am');
                }elseif($year !="all" && $month=="all"){                    
                    $start_date = strtotime("$year-01-01 12:00:00 am");
                }else{                  
                    $start_date = strtotime("$year-$month-01 12:00:00 am");
                }

                if($month == "all" && $year == "all"){
                    $end_date = time();
                }elseif($month=="all"){
                    $end_date = strtotime("$year-12-01 12:00:00 am");
                    $end_date = strtotime(date('Y-m-t', $end_date).' 11:59:59 pm');
                }else{
                    $end_date = strtotime("$year-$month-01 12:00:00 am");
                    $end_date = strtotime(date('Y-m-t', $end_date).' 11:59:59 pm');
                }
            }

            // echo 'Start:'.FeedBack::date_fm($start_date);
            // echo ' <br/>';
            // echo 'End :'.FeedBack::date_fm($end_date);

        }elseif(isset($_POST['searchPeroid'])){
            $from = $_POST['from'];
            $to = $_POST['to'];

            $errors = array();

            if(empty($from) || empty($to) ){
                FeedBack::error("Please Enter Both Dates (From and To)");
            }elseif($from && $to){
                $start_date = strtotime($from.' 12:00:00 am');
                $end_date = strtotime($to.' 11:59:59 pm');
            }

            if($start_date>$end_date){
                $errors[] = "From Date <b>(".date('Y-m-d', $start_date).")</b> cannot be greater than To Date <b>(".date('Y-m-d', $end_date).")</b>";
            }

            if ($errors !== []) {
                FeedBack::errors($errors);
            }

        }elseif(isset($_POST['searchRequisition'])){
            $search_type = $_POST['search_type'];
            $search_name = trim($_POST['search_name']);
            
            $errors = array();
            if ($search_name === '' || $search_name === '0') {
                $errors[] = "Please Enter Invoice Number";
            }

            if ($errors !== []) {
                FeedBack::errors($errors);
            }
        }

        if(empty($start_date) && empty($end_date)){
            $start_date = strtotime(date('2021-01-01').' 12:00:00 am');
            $end_date = time();
        }

        if(empty($year) && empty($month)){
            $year = date('Y');
            $month = date('m');
        }

        $from = date('Y-m-d', $start_date);
        $to = date('Y-m-d', $end_date);

        //echo $from;

    echo '<form action="" method="post">';
    ?>
    <div class="row">
            <div class="col-md-12">
    <button type="button" class="switch" data-id="rangeDetails">Date Range</button>
    <button type="button" class="switch" data-id="numberDetails">Requisition Number</button>
    <button type="button" class="switch" data-id="monthDetails">Month</button>

    <div id="rangeDetails" class="hide2">
    <?php
    echo '<div style="margin-top:20px;">';
    echo '<div style="float:left; margin-right:20px;">';
        echo '<label>From&nbsp;&nbsp;</label>';
        echo '<input id="date1" type="date" name="from" value="'.$from.'"><br/>';
        echo '</div>';
        echo '<div style="float:left; margin-right:20px;">';
        echo '<label>To&nbsp;&nbsp;</label>';
        echo '<input id="date2" type="date" name="to" value="'.$to.'">';
        echo '</div>';
            
        echo '<div style="float:left; margin-right:20px;">';
        echo '<button type="submit" name="searchPeroid" class="btn btn-primary btn-xs pt-0 pb-0"><i class="fa fa-fw fa-search"></i> Show</button>';
        echo '</div>';
        echo '</div>';
    ?>

    <script type="text/javascript">
        $(document).ready(function(){
            $('input[type ="date"]').bootstrapMaterialDatePicker({ 
                weekStart : 0, 
                time: false, 
                //minDate : new Date() 
            });
        });
    </script>
    </div>
    <div id="numberDetails" class="hide2">
    <?php
    echo '<div style=" margin-top:20px;">';
        echo '<label>Search All Requisition which &nbsp;</label>';
        $st = array('Contain', 'Start With', 'Ends With', 'Are Equal to');
        echo '<select name="search_type">';
        $v =0;
        foreach($st as $t){
            if ($search_type == $v) {
                echo '<option selected="selected" value="'.($v++).'">'.$t.'</option>';
            } else {
                echo '<option value="'.($v++).'">'.$t.'</option>';
            }
        }
        echo '</select> &nbsp; ';
        echo '<input type="text" value="'.$search_name.'" name="search_name" placeholder="Enter Invoice number" />';
        echo '<button name="searchRequisition"><i class="fa fa-fw fa-search"></i> Search</button>';
        echo '</div>';

    ?>
    </div>
    <div id="monthDetails" class="hide2">
    <?php
    echo '<div style="float:left; margin-top:20px;">';
        echo '<label>Year&nbsp;&nbsp;</label>';
        echo '<select name="year">';
        echo '<option value="">Select</option>';
        // if($year == "all")
        //  echo '<option selected value="all">All</option>';
        // else
        //  echo '<option value="all">All</option>';
        for($i=2020; $i<=date('Y'); $i++){
            if ($year == $i) {
                echo '<option selected value="'.$i.'">'.$i.'</option>';
            } else {
                echo '<option value="'.$i.'">'.$i.'</option>';
            }
        }
        echo '</select>';
        echo '</div>';
    echo '<div style="float:left; margin-top:20px;">';
        echo '<label>&nbsp; Month&nbsp;&nbsp;</label>';
        echo '<select name="month">';
        echo '<option value="">Select</option>';
        if ($month == "all") {
            echo '<option selected value="all">All</option>';
        } else {
            echo '<option value="all">All</option>';
        }
        for($i=1; $i<=12; $i++){
            if ($month == $i) {
                echo '<option selected value="'.$i.'">'.$this->month_name($i).'</option>';
            } else {
                echo '<option value="'.$i.'">'.$this->month_name($i).'</option>';
            }
        }
        echo '</select>';
        echo '</div>';
        echo '<div style="float:left; margin-left:20px; margin-top:20px;">';
        echo '<button type="submit" name="monthDetails" class="btn btn-primary btn-xs pt-0 pb-0"><i class="fa fa-fw fa-search"></i> Show</button>';
        echo '</div>';
        echo '</div>';
        echo '<div class="clearfix"></div>';

        $db = new Db();

        if (isset($_POST['searchRequisition'])) {
            if ($search_type==0) {
                $sql = "SELECT TOP 10 * FROM requisition WHERE req_number LIKE '%$search_name%' AND req_app1_user_id IS NOT NULL AND req_pushed IS NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==1) {
                $sql = "SELECT TOP 10 * FROM requisition WHERE req_number LIKE '$search_name%' AND req_app1_user_id IS NOT NULL AND req_pushed IS NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==2) {
                $sql = "SELECT TOP 10 * FROM requisition WHERE req_number LIKE '%$search_name' AND req_app1_user_id IS NOT NULL AND req_pushed IS NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==3) {
                $sql = "SELECT TOP 10 * FROM requisition WHERE req_number = '$search_name' AND req_app1_user_id IS NOT NULL AND req_pushed IS NULL ORDER BY req_number ASC, req_date_added ASC";
            }
        } elseif (isset($_POST['sun'])) {
            
        } else{
            $sql = "SELECT TOP 10 * FROM requisition WHERE req_date_added >= '$start_date' AND req_date_added <= '$end_date' AND req_app1_user_id IS NOT NULL AND req_pushed IS NULL ORDER BY req_number ASC, req_date_added ASC";
        }

		$push = array();
		$s = array();
        if(isset($_POST['sun'])){
        
            $s = $_POST['req'];
            $sql = $_POST['sql2'];
            $z = $_POST['z'];

            if(empty($s)){
                Feedback::error("Please Check some Requisition.");
            }else{

$xml = array();

                 foreach($s as $ss){
                    $sqlu = "SELECT * FROM requisition WHERE req_id = '$ss'";
                    $sel = $db->select($sqlu);                    
                    $db = new Db();
                    foreach($sel as $row){
                        extract($row);
                        $v = new Db(); 
$str = "<SSC><User><Name>PK1</Name></User><SunSystemsContext><BusinessUnit>CDB</BusinessUnit></SunSystemsContext><Payload><MovementOrder><MovementOrderDefinitionCode>SRO_ISSUE</MovementOrderDefinitionCode><MovementOrderReference></MovementOrderReference><SecondReference>$req_number</SecondReference><Status></Status><TransactionReferenceNumber></TransactionReferenceNumber>";
                            
$p = 0;

$vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
$non =1;
foreach($vv as $r){
    extract($r);
    $p++;
$p1 = $p;
$p = str_pad($p, 2, "0", STR_PAD_LEFT);
$req_date_added = time();//+24*60*60;
$str .= "<MovementOrderLine><AccountCode></AccountCode><DemandQuantity>$ri_quantity</DemandQuantity><Description></Description><FromLocationIdentifier>L01</FromLocationIdentifier><ItemCode>".trim($ri_code)."</ItemCode><LineNumber></LineNumber><OrderDate>".date('dmY',$req_date_added)."</OrderDate><TransactionPeriod></TransactionPeriod><UnitOfMovement></UnitOfMovement><UserLineNumber>".$p1."</UserLineNumber><AnalysisQuantity><Analysis><VMolCatAnalysis_AnlCode>".$this->rgf("approval_matrix", $req_division, "ap_id","ap_code")."</VMolCatAnalysis_AnlCode></Analysis></AnalysisQuantity><VLAB1><Base><VMolVlabEntry_Val>$ri_quantity</VMolVlabEntry_Val></Base></VLAB1></MovementOrderLine>";
}

$str .= "</MovementOrder></Payload></SSC>";
//echo '<pre>';
//echo htmlspecialchars($str);
//echo '</pre>';
                        $xml[$req_id] = simplexml_load_string($world->sendToSun(array($str)));
                    }           
                }
            }

            // echo '<pre>';
            // print_r($xml);
            // echo '</pre>';

            foreach($xml as $id=>$string){
                $status = $string->Payload->MovementOrder->attributes()['status'];
                if($status=="fail"){
                    $errors[] = '<b>'.$this->rgf("requisition", $id, "req_id", "req_number").'</b> => '. $string->Payload->MovementOrder->Messages->Message->Application->Message;
                }elseif($status == "success"){
                    $ref = $string->Payload->MovementOrder->MovementOrderReference;
                    //$issueNumber[$id] = $ref;
                    $push[$this->rgf("requisition", $id, "req_id", "req_number")] = $ref;
                   $db->update("requisition",["req_pushed"=>1, "req_date_pushed"=>time(), "req_pushed_by"=>time(), "req_issue_reference"=>$ref],["req_id"=>$id]);
                }
            }

            if($errors !== []){
                Feedback::errors($errors);
            }
            if($push !== []){
                $message = '<b>No. Pushed:</b> '.number_format(count($push)).'<br/><b>Requisition and Issue Reference: </b> ';
                    $o = 0;
                    foreach($push as $p=>$r){
                        $message .= $p.'=>'.$r;
                        $o++;
                        if ($o<count($push)) {
                            $message .= ', ';
                        }
                    }
                
                Feedback::success($message);
            }
        }

        $select = $db->select($sql);
        echo '<button type="button" id="checker" onclick="return multipleChecker();">Check All</button>';
        echo '<table border="1" id="table">';
        echo '<tr>';
        echo '<th></th>';
        echo '<th>No.</th>';
        echo '<th>Date<br/>Requested</th>';
        echo '<th>Division <br/>/Location</th>';
        echo '<th>Requisition <br/>Number</th>';
        echo '<th>Requisition Title</th>';
        echo '<th>Requested By</th>';
        echo '<th>Approved By</th>';
        echo '<th>Show / Hide</th>';
        echo '</tr>';
        $no=1;
        $non = 1;
        foreach($select as $row){
            extract($row);
            $v = new Db();
            $vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
            $non =1;

            if (array_key_exists($req_number, $push)) {
                echo '<tr style="display:none; font-weight: bold;">';
            } else {
                echo '<tr style="font-weight: bold;">';
            }

            $row = 2; //($this->total("requisition_item", "ri_ref", $req_ref))+1;
            if(in_array($req_id, $s)){
                echo '<td rowspan="'.$row.'"><input checked="checked" type="checkbox" class="AllCheckBoxes"  id="jj'.$no.'" name="req[]" value="'.$req_id.'"/><label for="jj'.$no.'"></label></td>';
            }else{
                echo '<td rowspan="'.$row.'"><input type="checkbox" class="AllCheckBoxes"  id="jj'.$no.'" name="req[]" value="'.$req_id.'"/><label for="jj'.$no.'"></label></td>';
            }
            echo '<td rowspan="'.$row.'">'.($no++).'.</td>';
            echo '<td>'.FeedBack::date_fm($req_date_added).'</td>';
            echo '<td>'.$this->rgf("approval_matrix", $req_division, "ap_id", "ap_code").'</td>';
            echo '<td>'.$req_number.'</td>';
            echo '<td>'.$req_title.'</td>';
            echo '<td>'.$this->full_name($req_added_by).'</td>';
            echo '<td>'.$this->full_name($req_app1_user_id).'</td>';
            echo '<td>
            <span data-id="'.$req_id.'" class="item-details btn btn-xs btn-primary"><span class="words-'.$req_id.'">Show Items</span> ('.number_format($v->num_rows()).')</span>
            </td>';
            echo '</tr>';

            if (array_key_exists($req_number, $push)) {
                echo '<tr style="display:none;">';
            } else {
                echo '<tr>';
            }
         
            echo '<td colspan="7"  style="">';

            echo '<div class="details-of-items-'.$req_id.'" style="display:none;padding:20px">';

            echo '<table id="table2" border="1" style="width:100%">';
            echo '<tr>';
            echo '<th style="width:20px;">No.</th>';
            echo '<th>Item Code</th>';
            echo '<th>Item Description</th>';
            echo '<th>Quantity</th>';
            echo '<th>UOM</th>';
            echo '<th>Price</th>';
            echo '</tr>';
            $vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
            $non =1;
            foreach($vv as $r){
                extract($r);
                $color = "";//($non%2==0)?"blue":"black";
                echo '<tr style="color:'.$color.'">';
                
                echo '<td>'.($non++).'.</td>';
                echo '<td>'.$ri_code.'</td>';
                echo '<td>'.$ri_description.'</td>';
                echo '<td>'.$ri_quantity.'</td>';
                echo '<td>'.$ri_uom.'</td>';
                //echo '<td>'.$ri_price.'</td>';
                echo '</tr>';
            }

            $non++;

            echo '</table>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
        //echo '</table>';



        if(!$db->num_rows()){
            echo '<tr><td colspan="11" style="font-weight:bold;"><center>No Data</center></td></tr>';
        }
        echo '</table>';

        ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $('.item-details').click(function(){
                    var req = $(this).attr('data-id');
                    $('.details-of-items-'+req).toggle();
                    $(this).toggleClass('btn-danger');
                    $(this).toggleClass('btn-primary');
                    if($('.words-'+req).html()=="Show Items"){
                        $('.words-'+req).html("Hide Items");
                    }else{                        
                        $('.words-'+req).html("Show Items");
                    }
                });
            });
        </script>
        <?php

        if($db->num_rows()){
            echo '<br/><button type="submit" name="sun" class="btn btn-primary"><i class="fa fa-fw fa-plane"></i> Push to SUN</button>';
            echo '<input type="hidden" value="'.$sql.'" name="sql2"/>';

            if (isset($_POST['monthDetails'])) {
                echo '<input type="hidden" value="monthDetails" name="z"/>';
            } elseif (isset($_POST['searchRequisition'])) {
                echo '<input type="hidden" value="searchRequisition" name="z"/>';
            } elseif (isset($_POST['searchPeroid'])) {
                echo '<input type="hidden" value="searchPeroid" name="z"/>';
            } else {
                echo '<input type="hidden" value="searchPeroid" name="z"/>';
            }
            
        }

        echo '</form>';

    ?>
    </div>
</div>
</div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.hide2').hide();
            <?php 
            if (isset($_POST['monthDetails']) || isset($monthDetails)) {
                echo "$('#monthDetails').show();";
            } elseif (isset($_POST['searchRequisition']) || isset($searchRequisition)) {
                echo "$('#numberDetails').show();";
            } elseif (isset($_POST['searchPeroid']) || isset($searchPeroid)) {
                echo "$('#rangeDetails').show();";
            } else {
                echo "$('#rangeDetails').show();";
            }
            ?>

            $('.switch').click(function(){
                $('.hide2').hide();
                $('.hide2').css("font-weight", "100");
                var id = $(this).attr('data-id');
                $(this).css("font-weight", "500");              
                $('#'+id).show();
            });
            
        });
    </script>

    <?php   
    }

    public function pushedOldAction(){

        error_reporting(true);
    require_once(__DIR__ . "/http://localhost:8080/myPHPapp/java/Java.inc");
    $world = new java("HelloWorld");

    $db = new Db();

    $errors = array();

    if(isset($_POST['monthDetails'])){
            $month = $_POST['month'];
            $year = $_POST['year'];

            if(empty($year)||empty($month)){
                FeedBack::error("Please select Year and Month");
            }else{

                if($year == "all"){
                    $start_date = strtotime('2020-10-20 12:00:00 am');
                }elseif($year !="all" && $month=="all"){                    
                    $start_date = strtotime("$year-01-01 12:00:00 am");
                }else{                  
                    $start_date = strtotime("$year-$month-01 12:00:00 am");
                }

                if($month == "all" && $year == "all"){
                    $end_date = time();
                }elseif($month=="all"){
                    $end_date = strtotime("$year-12-01 12:00:00 am");
                    $end_date = strtotime(date('Y-m-t', $end_date).' 11:59:59 pm');
                }else{
                    $end_date = strtotime("$year-$month-01 12:00:00 am");
                    $end_date = strtotime(date('Y-m-t', $end_date).' 11:59:59 pm');
                }
            }

            // echo 'Start:'.FeedBack::date_fm($start_date);
            // echo ' <br/>';
            // echo 'End :'.FeedBack::date_fm($end_date);

        }elseif(isset($_POST['searchPeroid'])){
            $from = $_POST['from'];
            $to = $_POST['to'];

            $errors = array();

            if(empty($from) || empty($to) ){
                FeedBack::error("Please Enter Both Dates (From and To)");
            }elseif($from && $to){
                $start_date = strtotime($from.' 12:00:00 am');
                $end_date = strtotime($to.' 11:59:59 pm');
            }

            if($start_date>$end_date){
                $errors[] = "From Date <b>(".date('Y-m-d', $start_date).")</b> cannot be greater than To Date <b>(".date('Y-m-d', $end_date).")</b>";
            }

            if ($errors !== []) {
                FeedBack::errors($errors);
            }

        }elseif(isset($_POST['searchRequisition'])){
            $search_type = $_POST['search_type'];
            $search_name = trim($_POST['search_name']);
            
            $errors = array();
            if ($search_name === '' || $search_name === '0') {
                $errors[] = "Please Enter Invoice Number";
            }

            if ($errors !== []) {
                FeedBack::errors($errors);
            }
        }

        if(empty($start_date) && empty($end_date)){
            $start_date = strtotime(date('Y-m-d').' 12:00:00 am');
            $end_date = time();
        }

        if(empty($year) && empty($month)){
            $year = date('Y');
            $month = date('m');
        }

        $from = date('Y-m-d', $start_date);
        $to = date('Y-m-d', $end_date);

        //echo $from;

    echo '<form action="" method="post">';
    ?>
    <div class="row">
            <div class="col-md-12">
    <button type="button" class="switch" data-id="rangeDetails">Date Range</button>
    <button type="button" class="switch" data-id="numberDetails">Requisition Number</button>
    <button type="button" class="switch" data-id="monthDetails">Month</button>

    <div id="rangeDetails" class="hide2">
    <?php
    echo '<div style="margin-top:20px;">';
    echo '<div style="float:left; margin-right:20px;">';
        echo '<label>From&nbsp;&nbsp;</label>';
        echo '<input id="date1" type="date" name="from" value="'.$from.'"><br/>';
        echo '</div>';
        echo '<div style="float:left; margin-right:20px;">';
        echo '<label>To&nbsp;&nbsp;</label>';
        echo '<input id="date2" type="date" name="to" value="'.$to.'">';
        echo '</div>';
            
        echo '<div style="float:left; margin-right:20px;">';
        echo '<button type="submit" name="searchPeroid" class="btn btn-primary btn-xs pt-0 pb-0"><i class="fa fa-fw fa-search"></i> Show</button>';
        echo '</div>';
        echo '</div>';
    ?>

    <script type="text/javascript">
        $(document).ready(function(){
            $('input[type ="date"]').bootstrapMaterialDatePicker({ 
                weekStart : 0, 
                time: false, 
                //minDate : new Date() 
            });
        });
    </script>
    </div>
    <div id="numberDetails" class="hide2">
    <?php
    echo '<div style=" margin-top:20px;">';
        echo '<label>Search All Requisition which &nbsp;</label>';
        $st = array('Contain', 'Start With', 'Ends With', 'Are Equal to');
        echo '<select name="search_type">';
        $v =0;
        foreach($st as $t){
            if ($search_type == $v) {
                echo '<option selected="selected" value="'.($v++).'">'.$t.'</option>';
            } else {
                echo '<option value="'.($v++).'">'.$t.'</option>';
            }
        }
        echo '</select> &nbsp; ';
        echo '<input type="text" value="'.$search_name.'" name="search_name" placeholder="Enter Invoice number" />';
        echo '<button name="searchRequisition"><i class="fa fa-fw fa-search"></i> Search</button>';
        echo '</div>';

    ?>
    </div>
    <div id="monthDetails" class="hide2">
    <?php
    echo '<div style="float:left; margin-top:20px;">';
        echo '<label>Year&nbsp;&nbsp;</label>';
        echo '<select name="year">';
        echo '<option value="">Select</option>';
        // if($year == "all")
        //  echo '<option selected value="all">All</option>';
        // else
        //  echo '<option value="all">All</option>';
        for($i=2020; $i<=date('Y'); $i++){
            if ($year == $i) {
                echo '<option selected value="'.$i.'">'.$i.'</option>';
            } else {
                echo '<option value="'.$i.'">'.$i.'</option>';
            }
        }
        echo '</select>';
        echo '</div>';
    echo '<div style="float:left; margin-top:20px;">';
        echo '<label>&nbsp; Month&nbsp;&nbsp;</label>';
        echo '<select name="month">';
        echo '<option value="">Select</option>';
        if ($month == "all") {
            echo '<option selected value="all">All</option>';
        } else {
            echo '<option value="all">All</option>';
        }
        for($i=1; $i<=12; $i++){
            if ($month == $i) {
                echo '<option selected value="'.$i.'">'.$this->month_name($i).'</option>';
            } else {
                echo '<option value="'.$i.'">'.$this->month_name($i).'</option>';
            }
        }
        echo '</select>';
        echo '</div>';
        echo '<div style="float:left; margin-left:20px; margin-top:20px;">';
        echo '<button type="submit" name="monthDetails" class="btn btn-primary btn-xs pt-0 pb-0"><i class="fa fa-fw fa-search"></i> Show</button>';
        echo '</div>';
        echo '</div>';
        echo '<div class="clearfix"></div>';

        $db = new Db();

        if (isset($_POST['searchRequisition'])) {
            if ($search_type==0) {
                $sql = "SELECT * FROM requisition WHERE req_number LIKE '%$search_name%' AND req_app1_user_id IS NOT NULL AND req_pushed IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==1) {
                $sql = "SELECT * FROM requisition WHERE req_number LIKE '$search_name%' AND req_app1_user_id IS NOT NULL AND req_pushed IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==2) {
                $sql = "SELECT * FROM requisition WHERE req_number LIKE '%$search_name' AND req_app1_user_id IS NOT NULL AND req_pushed  NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            } elseif ($search_type==3) {
                $sql = "SELECT * FROM requisition WHERE req_number = '$search_name' AND req_app1_user_id IS NOT NULL AND req_pushed IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
            }
        } elseif (isset($_POST['sun'])) {
            
        } else{
            $sql = "SELECT * FROM requisition WHERE req_date_added >= '$start_date' AND req_date_added <= '$end_date' AND req_app1_user_id IS NOT NULL AND req_pushed IS NOT NULL ORDER BY req_number ASC, req_date_added ASC";
        }

		$s = array();
		$pushed = array();

        if(isset($_POST['sun'])){
        
            $s = $_POST['req'];
            $sql = $_POST['sql2'];
            $z = $_POST['z'];

            if(empty($s)){
                Feedback::error("Please Check some Requisition.");
            }else{

$xml = array();

                 foreach($s as $ss){
                    $sqlu = "SELECT * FROM requisition WHERE req_id = '$ss'";
                    $sel = $db->select($sqlu);                    
                    $db = new Db();
                    foreach($sel as $row){
                        extract($row);
                        $v = new Db(); 
$str = "<SSC><User><Name>PK1</Name></User><SunSystemsContext><BusinessUnit>CDB</BusinessUnit></SunSystemsContext><Payload><MovementOrder><MovementOrderDefinitionCode>SRO_ISSUE</MovementOrderDefinitionCode><MovementOrderReference></MovementOrderReference><SecondReference>$req_number</SecondReference><Status></Status><TransactionReferenceNumber></TransactionReferenceNumber>";
                            
$p = 0;

$vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
$non =1;
foreach($vv as $r){
    extract($r);
    $p++;
$p1 = $p;
$p = str_pad($p, 2, "0", STR_PAD_LEFT);
$req_date_added = time()+24*60*60;
$str .= "<MovementOrderLine><AccountCode></AccountCode><DemandQuantity>$ri_quantity</DemandQuantity><Description></Description><FromLocationIdentifier>L".($p)."</FromLocationIdentifier><ItemCode>1101154</ItemCode><LineNumber></LineNumber><OrderDate>".date('dmY',$req_date_added)."</OrderDate><TransactionPeriod></TransactionPeriod><UnitOfMovement></UnitOfMovement><UserLineNumber>".$p1."</UserLineNumber><Analysis2><VMolCatAnalysis_AnlCode></VMolCatAnalysis_AnlCode></Analysis2><VLAB1><Base><VMolVlabEntry_Val>$ri_quantity</VMolVlabEntry_Val></Base></VLAB1></MovementOrderLine>";
}

$str .= "</MovementOrder></Payload></SSC>";
// echo '<pre>';
// echo htmlspecialchars($str);
// echo '</pre>';
                        $xml[$req_id] = simplexml_load_string($world->sendToSun(array($str)));
                    }           
                }
            }

            //echo '<pre>';
            //print_r($xml);
            //echo '</pre>';

            foreach($xml as $id=>$string){
                $status = $string->Payload->MovementOrder->attributes()['status'];
                if($status=="fail"){
                    $errors[] = '<b>'.$this->rgf("requisition", $id, "req_id", "req_number").'</b> => '. $string->Payload->MovementOrder->Messages->Message->Application->Message;
                }
            }

            if($errors !== []){
                Feedback::errors($errors);
            }
        }

        $select = $db->select($sql);
        //echo '<button type="button" id="checker" onclick="return multipleChecker();">Check All</button>';
        echo '<table border="1" id="table">';
        echo '<tr>';
        //echo '<th></th>';
        echo '<th>No.</th>';
        echo '<th>Date<br/>Requested</th>';
        echo '<th>Division <br/>/Location</th>';
        echo '<th>Requisition <br/>Number</th>';
        echo '<th>Requisition Title</th>';
        echo '<th>Requested By</th>';
        echo '<th>Approved By</th>';
        echo '<th>Issue Reference</th>';
        echo '<th>Show / Hide</th>';
        echo '</tr>';
        $no=1;
        $non = 1;
        foreach($select as $row){
            extract($row);
            $v = new Db();
            $vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
            $non =1;

            echo '<tr style="font-weight: bold;">';
            $row = 2; //($this->total("requisition_item", "ri_ref", $req_ref))+1;
            if(in_array($req_id, $s)){
               // echo '<td rowspan="'.$row.'"><input checked="checked" type="checkbox" class="AllCheckBoxes"  id="jj'.$no.'" name="req[]" value="'.$req_id.'"/><label for="jj'.$no.'"></label></td>';
            }else{
                //echo '<td rowspan="'.$row.'"><input type="checkbox" class="AllCheckBoxes"  id="jj'.$no.'" name="req[]" value="'.$req_id.'"/><label for="jj'.$no.'"></label></td>';
            }
            echo '<td rowspan="'.$row.'">'.($no++).'.</td>';
            echo '<td>'.FeedBack::date_fm($req_date_added).'</td>';
            echo '<td>'.$this->rgf("approval_matrix", $req_division, "ap_id", "ap_code").'</td>';
            echo '<td>'.$req_number.'</td>';
            echo '<td>'.$req_title.'</td>';
            echo '<td>'.$this->full_name($req_added_by).'</td>';
            echo '<td>'.$this->full_name($req_app1_user_id).'</td>';
            echo '<td>'.($req_issue_reference).'</td>';
            echo '<td>
            <span data-id="'.$req_id.'" class="item-details btn btn-xs btn-primary"><span class="words-'.$req_id.'">Show Items</span> ('.number_format($v->num_rows()).')</span>
            </td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td colspan="7"  style="">';

            echo '<div class="details-of-items-'.$req_id.'" style="display:none;padding:20px">';

            echo '<table id="table2" border="1">';
            echo '<tr>';
            echo '<th style="width:20px;">No.</th>';
            echo '<th>Item Code</th>';
            echo '<th>Item Description</th>';
            echo '<th>Quantity</th>';
            echo '<th>UOM</th>';
            echo '<th>Price</th>';
            echo '</tr>';
            $vv = $v->select("SELECT * FROM requisition_item WHERE ri_ref = '$req_ref' ORDER BY ri_date_added ASC");
            $non =1;
            foreach($vv as $r){
                extract($r);
                $color = "";//($non%2==0)?"blue":"black";
                echo '<tr style="color:'.$color.'">';
                
                echo '<td>'.($non++).'.</td>';
                echo '<td>'.$ri_code.'</td>';
                echo '<td>'.$ri_description.'</td>';
                echo '<td>'.$ri_quantity.'</td>';
                echo '<td>'.$ri_uom.'</td>';
                //echo '<td>'.$ri_price.'</td>';
                echo '</tr>';
            }

            $non++;

            echo '</table>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
        //echo '</table>';



        if(!$db->num_rows()){
            echo '<tr><td colspan="11" style="font-weight:bold;"><center>No Data</center></td></tr>';
        }
        echo '</table>';

        ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $('.item-details').click(function(){
                    var req = $(this).attr('data-id');
                    $('.details-of-items-'+req).toggle();
                    $(this).toggleClass('btn-danger');
                    $(this).toggleClass('btn-primary');
                    if($('.words-'+req).html()=="Show Items"){
                        $('.words-'+req).html("Hide Items");
                    }else{                        
                        $('.words-'+req).html("Show Items");
                    }
                });
            });
        </script>
        <?php

        if($db->num_rows()){
            //echo '<br/><button type="submit" name="sun" class="btn btn-primary"><i class="fa fa-fw fa-plane"></i> Push to SUN</button>';
            echo '<input type="hidden" value="'.$sql.'" name="sql2"/>';

            if (isset($_POST['monthDetails'])) {
                echo '<input type="hidden" value="monthDetails" name="z"/>';
            } elseif (isset($_POST['searchRequisition'])) {
                echo '<input type="hidden" value="searchRequisition" name="z"/>';
            } elseif (isset($_POST['searchPeroid'])) {
                echo '<input type="hidden" value="searchPeroid" name="z"/>';
            } else {
                echo '<input type="hidden" value="searchPeroid" name="z"/>';
            }
            
        }

        echo '</form>';

    ?>
    </div>
</div>
</div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.hide2').hide();
            <?php 
            if (isset($_POST['monthDetails']) || isset($monthDetails)) {
                echo "$('#monthDetails').show();";
            } elseif (isset($_POST['searchRequisition']) || isset($searchRequisition)) {
                echo "$('#numberDetails').show();";
            } elseif (isset($_POST['searchPeroid']) || isset($searchPeroid)) {
                echo "$('#rangeDetails').show();";
            } else {
                echo "$('#rangeDetails').show();";
            }
            ?>

            $('.switch').click(function(){
                $('.hide2').hide();
                $('.hide2').css("font-weight", "100");
                var id = $(this).attr('data-id');
                $(this).css("font-weight", "500");              
                $('#'+id).show();
            });
            
        });
    </script>

    <?php   
    }

	

    public function numPending(){
        $db = new Db();
        //return 3;
        $db->select("SELECT req_id FROM requisition WHERE req_app1_user_id IS NOT NULL AND req_pushed IS NULL");
       return $db->num_rows();
    }
	
	
}