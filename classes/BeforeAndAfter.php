<?php
class BeforeAndAfter{
	//public $user_id = $_SESSION['USER_ID'];
	
	public $show1 = "Active";
	public $show2 = "Inactive";
	
	public $show11 = "Active";
	public $show22 = "Inactive";

	public function __call($name, $args){

		//run code before
		$method = "{$name}Action";
		if(method_exists($this, $method)){
			if($this->before() !== false){				
				call_user_func_array([$this, $method],$args);
				$this->after();
			}
		}else{
			echo $method.' Method doesnot exists';
		}
		
	}

	
	public function isApprovalNotDelegate($user_id){

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

	public function divApproval($division){
		$db = new Db();
		$select = $db->select("SELECT gr_name, apg_user, user_email FROM approval_group, approval_matrix, groups, sysuser WHERE ap_id = '$division' AND gr_matrix = ap_id AND gr_id = apg_name AND apg_user = user_id");
		
		$users_emails = array();

		foreach($select as $row){
			extract($row);
			$users_emails[]=$apg_user;
		}

		return $users_emails;
	}


	public function findDiv($user_id){
		//$user_id = 4221;
		$db = new Db();
		//$select = $db->select("SELECT * FROM approval_group WHERE apg_user = '$user_id'");
		$sql = "SELECT ap_id FROM approval_group, approval_matrix, groups, sysuser WHERE gr_matrix = ap_id AND gr_id = apg_name AND apg_user = '$user_id'";
		$select = $db->select($sql);
		if($db->num_rows()){
			extract($select[0]);
		}

		return $ap_id;
	}
	
	public function month_name($num){
	    $monthNum  = $num;
	    $dateObj   = DateTime::createFromFormat('!m', $monthNum);
	    return $monthName = $dateObj->format('F'); 
	}

	public function month_name_short($num){
	    $monthNum  = $num;
	    $dateObj   = DateTime::createFromFormat('!m', $monthNum);
	    return $monthName = $dateObj->format('M'); 
	}


	public function paginationMonth(){
		
	    $class = portion(1);
	    $method = portion(2);
	    $year = (int)portion(3);
	    $month = (int)portion(4);

	    if(empty($year)) $year = date('Y');
	    if(empty($month)) $month = date('m');

	    for($i=2020; $i<=date('Y'); $i++){
	    	if($i==2020) echo 'Years: > ';
	    	if($year == $i)
	    		echo '<span style="font-size:12px; padding:0 5px; margin:2px; font-weight:bold;">'.($i).'</span>';
	    	else
	    		echo '<a href="'.return_url().$class.'/'.$method.'/'.$i.'/'.$month.'" class="btn btn-sm" style="font-size:12px; padding:0 5px; margin:2px;">'.($i).'</a>';

	    	
	    }
	    echo '<br/>';
    	for($j=1; $j<=12; $j++){
    		if($j==1) echo 'Months: > ';
    		if($month == $j)
    			echo '<span style="font-size:12px; padding:0 5px; margin:2px; font-weight:bold;">'.$this->month_name_short($j).'</span>';
    		else
    			echo '<a href="'.return_url().$class.'/'.$method.'/'.$year.'/'.$j.'" class="btn btn-sm" style="font-size:12px; padding:0 5px; margin:2px;">'.$this->month_name_short($j).'</a>';

    		if(date('Y')==$year && date('m')==$j)
    			break;
    	}
    	echo '<div class="clearfix" style="margin-top:10px;"></div>';

    	$start = strtotime($year.'-'.$month.'-'.'01 12:00:00 am');
    	$end = strtotime( date('Y-m-t', $start).' 11:59:59 pm');
    	return array('start'=>$start, 'end'=>$end);
	}


	

	public function pArray($array){
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}

	public function penc($p){
		return md5($p);
	}
	
	protected function deletor($table, $column,  $id, $return_to){
		$db = new Db();
		
		$id = portion(3);
		$sql = "DELETE FROM $table WHERE $column = '$id'";
		
		AuditTrail::registerTrail($sql, $id,  $table, $column);

		$delete = $db->query($sql);
		
		if($delete){
			FeedBack::success('Deleting. Please wait ...');
			FeedBack::refresh(1,return_url().$return_to);
		}else{
			FeedBack::error('Not Deleted '.$db->error());
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
	
	public function action($type, $link, $word = ""){
		
		$type = strtolower($type);
		
		if($type == "edit")
			return '<a href="'.return_url().$link.'" class="btn btn-xs btn-success" style="margin:0 2px;">'.$word.'</a>';
		
		if($type == "delete")
			return '<a href="'.return_url().$link.'" class="btn btn-xs btn-danger" style="margin:0 2px;">'.$word.'</a>';
		
		if($type == "view")
			return '<a href="'.return_url().$link.'" class="btn btn-xs btn-warning"> '.$word.'</a>';
			
		return 'none';
	}
	protected function before(){
			
	}
	public function removeSpaces($string){
		return str_replace(" ", "&nbsp;", $string);
	}

	public function full_name($id){
		$db = new Db();
		$row = $db->select("SELECT user_surname, user_othername FROM sysuser WHERE user_id = '$id'");
		$db->error();
		return ucwords(strtolower($row[0]['user_surname'].' '.$row[0]['user_othername']));
	}
	//return user field
	public function ruf($id, $column){
		$db = new Db();
		$row = $db->select("SELECT $column FROM sysuser WHERE user_id = '$id'");
		echo $db->error();
		return $row[0][$column];
	}
	//return generic field from generic table
	public function rgf($table, $id, $look_up, $column){
		$db = new Db();
		$sql = "SELECT $column FROM $table WHERE $look_up = '$id' ";
		$row = @$db->select($sql);
		echo $db->error();
		return @$row[0][$column];
	}

	public function rgf22($table, $id, $look_up, $column){
		$db = new Db();
		echo $sql = "SELECT $column FROM $table WHERE $look_up = '$id' ";
		$row = @$db->select($sql);
		echo $db->error();
		return @$row[0][$column];
	}

	//return distinct field from generic table
	protected function rdfs($table, $column, $order=array()){
		$db = new Db();
		$row = $db->select("SELECT distinct $column FROM $table ");
		echo $db->error();
		return $row;
	}

	protected function hod($id){
		return 1;
	}


	protected function hod_delegate($id){
		$db = new Db();
		$row = $db->select("SELECT user_branch_id FROM sysuser WHERE user_id = '$id'");
		$user_branch = $row[0]['user_branch_id'];
		$hod = static_hod_id();
		$b = new Db();

		//echo "SELECT user_id FROM sysuser WHERE user_role = '$hod' AND user_branch_id = '$user_branch'";
		$bb = $b->select("SELECT user_id FROM sysuser WHERE user_role = '$hod' AND user_branch_id = '$user_branch'");					
		extract($bb[0]);
		$user_id;

		$d = $this->myDelegate($user_id, time());	
		
		if($d['delegate']) $user_id = $d['delegate'];	

		return $user_id;
	}
	protected function dept_name($id){
		$db = new Db();
		$row = $db->select("SELECT dept_name FROM department WHERE dept_id = '$id'");
		echo $db->error();
		return $row[0]['dept_name'];
	}
	
	protected function sec_name($id){
		$db = new Db();
		$row = $db->select("SELECT section_name FROM section WHERE section_id = '$id'");
		echo $db->error();
		return $row[0]['section_name'];
	}
	
	protected function hod_id($id){
		$db = new Db();
		$row = $db->select("SELECT hod_user_id FROM hod WHERE hod_dept_id = '$id'");
		echo $db->error();
		return @$row[0]['hod_user_id'];
	}
	
	public function total($table, $column="", $column_value=""){
		//echo "SELECT * FROM $table WHERE $column = '$column_value'";
		$db = new Db();

		if(empty($column))
			$sql = ("SELECT * FROM $table");
		else
			$sql = ("SELECT * FROM $table WHERE $column = '$column_value'");
		
		$row = $db->select($sql);
		$db->num_rows();
		echo $db->error();
		return @$db->num_rows();
	}
	
	
	protected function branch_name($id){
		$db = new Db();
		$row = $db->select("SELECT branch_name FROM branch WHERE branch_id = '$id'");
		echo $db->error();
		return $row[0]['branch_name'];
	}
	
	protected function grade($id){
		$db = new Db();
		$row = $db->select("SELECT grade FROM users WHERE user_id = $id limit 1");
		
		return $row[0]['grade'];
	}
	protected function staff_code($id){
		$db = new Db();
		$row = $db->select("SELECT staff_code FROM users WHERE user_id = $id limit 1");
		
		return $row[0]['staff_code'];
	}
	
	
	protected function role_id($id){
		$db = new Db();
		$row = $db->select("SELECT users.role_id as role_id FROM users, roles WHERE users.user_id = $id AND users.role_id = roles.role_id limit 1");
		
		return $row[0]['role_id'];
	}
	
	protected function role_name($id){
		$db = new Db();
		$row = $db->select("SELECT role_name FROM users, roles WHERE users.user_id = $id AND users.role_id = roles.role_id limit 1");
		
		return $row[0]['role_name'];
	}
	
	protected function designation_name($id){
		$db = new Db();
		$row = $db->select("SELECT designation_name FROM designation WHERE designation_id = '$id'");
		echo $db->error();
		return $row[0]['designation_name'];
	}
	
	protected function requestor_id($id){
		$db = new Db();
		$row = $db->select("SELECT vr_requestor_id FROM vehicle_request WHERE vr_id = '$id'");
		echo $db->error();
		return $row[0]['vr_requestor_id'];
	}
	
	public function rejector($table, $column_name=array(), $column_prefix=array()){
		$u = new Db();
		
		foreach ($column_name as $key => $value) {
			$column_name_and_value = $key.'='.$value.'';
		}
		
		foreach ($column_prefix as $key => $value) {
			$column_prefix_db = $key;
			$column_count = $value;
		}

		for($i=0; $i<$column_count-1; $i++){
			$lev[] = ' '.$column_prefix_db.($i+1)." = NULL ";
		}
		$levs = implode(" , ", $lev);

		$sql = "UPDATE $table SET $levs WHERE $column_name_and_value";

		$db = new Db();
		$update = $db->query($sql);
	}


	public function myDelegate($user_id, $time){
		
		$db = new Db();

		$sql = "SELECT del_to, del_start_date,del_added_by, del_end_date FROM delegation WHERE del_added_by = '$user_id' AND del_start_date <= '$time' AND del_end_date >= '$time' ORDER BY del_start_date ASC";
		$select = $db->select($sql);
		if($db->num_rows()){
			extract($select[0]);
		}

		$duration = ceil(((int)$del_end_date-(int)$del_start_date)/(24*60*60));
		return array('delegate'=>$del_to, 'from'=>$del_start_date, 'to'=>$del_end_date ,'duration'=>$duration, 'by'=>$del_added_by);
	}

	public function isDelegate($user_id, $time){
		
		$db = new Db();

		$sql = "SELECT del_to, del_start_date, del_end_date, del_added_by FROM delegation WHERE del_to = '$user_id' AND del_start_date <= '$time' AND del_end_date >= '$time' ORDER BY del_start_date ASC";
		$select = $db->select($sql);

		if($db->num_rows()){
			extract($select[0]);
			$duration = ceil(((int)$del_end_date-(int)$del_start_date)/(24*60*60));	
		
			return array('delegate'=>$del_to, 'from'=>$del_start_date, 'to'=>$del_end_date ,'duration'=>$duration, 'by'=>$del_added_by);
		}else{
			return array();
		}
	}

	protected function department($id, $table = false){
		$db = new Db();
		

		if($table==true){
			$row = $db->select("SELECT dp_name FROM department WHERE dp_id = '$id'");
		}else{
			$row = $db->select("SELECT dp_name FROM users, department WHERE users.user_id = $id AND users.user_dp_id = department.dp_id limit 1");
		}
		
		return $row[0]['dp_name'];
	}


	public function nextApproval(){
		$next = $this->isApproval(user_id());
		if($next < $this->levels()){
			$next = $next+1;
			$db = new Db();
			$select = $db->select("SELECT app_role_id FROM approval_order WHERE app_id = '$next'");

			if($db->num_rows()==0){					
				$app_role_id = 0;			
			}else{			
				extract($select[0]);			
			}

			return $this->rgf("sysuser", $app_role_id, "user_role", "user_id");

		}else{
			return -1;
		}
	}
	
	public function check_de_status($user_id, $type="SMS"){
		//$db = new Db();
		//SELECT de_id, de_status, de_name, de_user_id FROM disable_enable WHERE 1
		//$row = $db->select("SELECT de_status FROM disable_enable WHERE de_user_id = $user_id AND de_name = '$type' limit 1");
		
		//if($db->num_rows()==0){
			//return 1;
		//}

		//return !($row[0]['de_status']);
		return 1;
	}

		
	public function department_id($id){
		$db = new Db();
		$row = $db->select("SELECT user_dp_id FROM users WHERE users.user_id = $id limit 1");
		
		return @$row[0]['user_dp_id'];
	}

	public function cost_center1($id){
		$db = new Db();
		$row = $db->select("SELECT cost_center_name FROM cost_center WHERE cost_center_id = '$id'");
		
		return @$row[0]['cost_center_name'];
	}

	public function isThere($table, $params = array()){
		$db = new Db();

		foreach ($params as $key => $value) {
			$vals[] = "$key='$value'";
		}

		$all_vals = implode(' AND ', $vals);

		$select = $db->select("SELECT * FROM $table WHERE $all_vals;");

		if($db->num_rows()){
			return true;
		}else{
			return false;
		}

	}


	public function isThereEdit($table, $params = array()){
		
		$db = new Db();
		$i=0;
		$all = count($params);

		foreach ($params as $key => $value) {
			$i++;
			
			if($all == $i){
				$vals[] = "$key != '$value'";
			}else{
				$vals[] = "$key = '$value'";
			}
		}

		$all_vals = implode(' AND ', $vals);
		//echo "SELECT * FROM $table WHERE $all_vals;";
		$select = $db->select("SELECT * FROM $table WHERE $all_vals;");

		if($db->num_rows()){
			return true;
		}else{
			return false;
		}
	}

	protected function slashValuesTrim(&$params){
		foreach($params as &$var){
			is_array($var)? slashValues($var) : $var = trim(addslashes($var));
			unset($var);
		}
	}


	protected function stripValues(&$params){
		foreach($params as &$var){
			is_array($var)? html($var) : $var = trim(stripslashes($var));
			unset($var);
		}
	}

	protected function start_print($desc){
		echo '<style type="text/css">.t{display: none;}</style>';
		echo '<span class="pull-right btn btn-primary btn-xs" href="" onclick="return print1(\'printMe\');"><i class="fa fa-print fa-fw"></i> Print &nbsp; </span>';

		echo '<div class="clearfix"></div>';
		echo '<br/>';
		echo '<div id="printMe" class="" style="font-family: arial">';
		echo '<div class="t">';
		echo '<div style="border-bottom:5px double black;margin-bottom: 10px;padding-bottom: 10px;">';
		
		echo '<table width="100%"><tr valign="middle" style="line-height: 14px;">';
		echo '<td style="width:50px;height:50px;"><img src="'.return_url().'images/centenary.png" style="width:50px;"></td>';
		echo '<td align=""><B>';
		echo '<h2 style="text-transform: uppercase;text-align: left;">Centenary Bank</h2>';
		echo '</td>';
		echo '</tr></table>';
		echo '</div>';

		echo '<div style="font-size:14px; width:400px;border:5px double black;margin:auto; padding:1px 2px; text-align: center;">'.$desc.'</div>';

		echo '<div style="font-size: 10px; text-align: right;line-height: 12px;margin-bottom: 10px;">';
		echo 'Printed By:<b>'.$this->full_name(user_id()).'</b><br/>';
		echo 'Date Printed: <b>'.FeedBack::date_fm(time()).'</b>'; 
		echo '</div>';
		echo '<div class="clearfix"></div>';
		echo '</div>';
	}

	protected function end_print(){
		?>
		</div>

		<script>
		function print1(x){
			//if(confirm("Do you really want to print"))
			if(1){					
				var printing = window.open('','','left=0,top=0,width=700,height=400,toolbar=0,scrollbars=0,status=0');
				printing.document.write(document.getElementById('printMe').innerHTML);
				printing.document.close();
				printing.focus();
				printing.print();
				printing.close();
			}else{
				return false;
			}
		}
		</script>
		<?php
	}
	

	public function period($date){

		$mo = array(
			"JAN"=>"07",
			"FEB"=>"08",
			"MAR"=>"09",
			"APR"=>"10",
			"MAY"=>"11",
			"JUN"=>"12",
			"JUL"=>"01",
			"AUG"=>"02",
			"SEP"=>"03",
			"OCT"=>"04",
			"NOV"=>"05",
			"DEC"=>"06", 
		);
		$p = strtotime("+6 months", $date);
		return date("m/Y", $p);
	}

	public function isDriverLocked($driver_id){
		$db = new Db();
		$select = $db->select("SELECT TOP 1 dl_unlock_date FROM driver_lock WHERE dl_driver_id = '$driver_id' ORDER BY dl_lock_date DESC");

		if($db->num_rows()){
			extract($select[0]);
			
			if(empty($dl_unlock_date)){
				return 0;
			}else{
				//echo $dl_unlock_date;
				return $dl_unlock_date;
			}
		}else{
			return 1;
		}
	}

	protected function after(){
		
	}


}
?>