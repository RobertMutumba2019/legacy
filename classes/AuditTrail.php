<?php

include_once __DIR__ . "/Exporter.inc";
include_once __DIR__ . "/TableCreator.php";
include_once __DIR__ . "/BeforeAndAfter.php";

class AuditTrail extends BeforeAndAfter{

	public function __construct(){
		new AccessRights();
		
	}

	public static function getLinks(){
		$page = "AUDIT TRAIL";
		
		return array(
			array(
				"link_name"=>"View Audit Trail", 
				"link_address"=>"audit-trail/view-audit-trail",
				"link_icon"=>"fa-eye",
				"link_page"=>$page,
				"link_right"=>"V",
			)
		);
	}

	public static function registerTrail($sql="", $db_id="",  $db_table="", $description=""){
		
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

		//extract table name		
		$x = explode(' ', $sql);
		$action = trim(current($x));
				
		if(trim(strtoupper($action)) !== "SELECT")
		{
			$table = "";
			
			if(strtoupper($action) === "UPDATE"){
				$table = str_replace('`', '', strtoupper($x[1]));
				//$db_id = str_replace(';', '',end($x));
			}
			if(strtoupper($action) === "DELETE"){
				$table = str_replace('`', '', strtoupper($x[2]));
				//$db_id = str_replace(';', '',end($x));
			}
			if(strtoupper($action) === "INSERT"){
				$table = str_replace('`', '', strtoupper($x[2]));
				//$db_id = 1; // mysqli_insert_id($con);
			}

			$table = explode("(", $table);
			$table = $table[0];
		
		}
		
		//RECORD THE FOLLOWING:
		//-IP address
		//-URL VISITED
		//-USER
		//- ACTION
		//-SQL QUERY
	
		$time = time();
		$user=user_id();	
					
		$page = $_SERVER['REQUEST_URI'];

		$browser = $_SERVER['HTTP_USER_AGENT'];
		/*
		if(strpos($browser, 'MSIE') !== false){
		
		}
			*/
		$description = str_replace("'", "\'", addslashes(strip_tags(($description))));
		$sql = str_replace("'", "\'", addslashes(strip_tags(($sql))));

		$db = new Db();		
	
		if($action !== "UPDATE"){
			$x = $db->insert("trail_of_users",
					[
						"trail_sql"=>$sql, 
						"trail_date"=>$time, 
						"trail_ip"=>$ipaddress,
						"trail_action"=>$action,
						"trail_db_table"=>$table,
						"trail_db_id"=>$db_id,
						"trail_username"=>$user,
						"trail_page"=>$page,
						"trail_browser"=>$browser,
						"trail_description"=>$description,
					]
				);
			echo $db->error();
		}
		
	}	

	public function viewAuditTrailAction(){
		echo '<div id="tableArea">';
		$db = new Db();
		//SELECT `trail_id`, `trail_description`, `trail_date`, `trail_ip`, `trail_action`, `trail_db_table`, `trail_db_id`, `trail_username`, `trail_page`, `trail_browser`, `trail_sql` FROM `trail_of_users` WHERE 1
		$search = array();
		if(isset($_POST['show'])){
			$date = strtotime($_POST['date']);

			$username = $_POST['username'];
			$ip = $_POST['ip'];
			$actions = $_POST['actions'];

			if(!empty($username)){
				$search[] = 'trail_username = '.$username;
			}
			if(!empty($ip)){
				$search[] = 'trail_ip = \''.$ip.'\'';
			}
			if(!empty($actions)){
				$search[] = 'trail_action = \''.$actions.'\'';
			}
			
		}else{
			$date = strtotime(date('Y-m-d'));			
		}
		$to = $date+24*60*60;
		$search[] = 'trail_date >= '.$date.'';
		$search[] = 'trail_date < '.$to.'';

		$search_all = implode(' AND ', $search);
		//echo "SELECT * FROM trail_of_users WHERE $search_all ORDER BY trail_id DESC";
		$select = $db->select("SELECT * FROM trail_of_users WHERE $search_all ORDER BY trail_date DESC");

		$usernames = $this->rdfs("trail_of_users", "trail_username");

		echo '<form action="" method="post">';
		echo 'Date: &nbsp; <input type="date" name="date" value="'.date('Y-m-d', $date).'">';
		echo ' &nbsp;  &nbsp;  &nbsp; User: &nbsp;';
		echo '<select name="username">';
		echo '<option value="">All</option>';

		if(is_array($usernames)){
			foreach($usernames as $user){
				if (!empty($user)) {
                    extract($user);
                }
				if ($trail_username == $username) {
                    echo '<option value="'.$trail_username.'" selected="selected">'.$this->full_name($trail_username).'</option>';
                } else {
                    echo '<option value="'.$trail_username.'">'.$this->full_name($trail_username).'</option>';
                }
				
			}
		}

		echo '</select>';

		$valu = $this->rdfs("trail_of_users", "trail_ip");
		echo ' &nbsp; &nbsp; &nbsp; IP Adress <select name="ip">';
		echo '<option value="">All</option>';
		if(is_array($valu)){
			foreach($valu as $vale){
				if (!empty($vale)) {
                    extract($vale);
                }
				//echo "$trail_ip == $ip";
				if ($trail_ip == $ip) {
                    echo '<option value="'.$trail_ip.'" selected="selected">'.($trail_ip).'</option>';
                } else {
                    echo '<option value="'.$trail_ip.'">'.($trail_ip).'</option>';
                }
			}
		}
		echo '</select>';

		$values = $this->rdfs("trail_of_users", "trail_action");
		echo ' &nbsp; &nbsp; &nbsp; Action <select name="actions">';
		echo '<option value="">All</option>';
		if(is_array($values)){
			foreach($values as $valt){
				if (!empty($valt)) {
                    extract($valt);
                }
				//echo "sdfs $trail_action == $actions";
				if ($trail_action == $actions) {
                    echo '<option value="'.$trail_action.'" selected="selected">'.($trail_action).'</option>';
                } else {
                    echo '<option value="'.$trail_action.'">'.($trail_action).'</option>';
                }
			}
		}
		echo '</select>';

		echo ' &nbsp;  &nbsp;  &nbsp; <input type="submit" value="Show" name="show"/>';
		echo '<br/><br/>';
		echo '</form>';		

		echo '<table border="1" width="100%" id="table">';
		echo '<thead>';
		echo '<tr>';
		echo '<th>No.</th>';
		echo '<th>Date</th>';
		echo '<th>User</th>';		
		echo '<th>IP Address</th>';
		echo '<th style="width:100px">Table</th>';
		echo '<th>Action</th>';
		echo '<th>Page</th>';
		//echo '<th>Browser</th>';
		echo '</tr>';
	echo '</thead>';


				////////////////////////////REPORT STEP 1//////////////////
				$db_values = array();
				$db_values[] = array(
				
						"No",
						"Date",
						"User",		
						"IP Address",
						"Table",
						"Action",
						"Page",
						"Browser"
								);
				//////////////////////////////////////////////////////////////

		$no = 1;
		$db_values = []; //added the array by Mutumba Robert
		if (is_array($select) && $select !== []) {
		foreach($select as $row){
			extract($row);
			echo '<tr>';
			echo '<td>'.($no++).'.</th>';
			echo '<td>'.FeedBack::date_fm(($trail_date)).'</td>';
			echo '<td>'.$this->full_name($trail_username).'</td>';			
			echo '<td>'.$trail_ip.'</td>';			
			echo '<td>'.$trail_db_table.'</td>';
			echo '<td>'.$trail_action.'</td>';
			echo '<td>'.$trail_page.'</td>';
			//echo '<td>'.$trail_browser.'</td>';
			echo '</tr>';
			
					//////////////////////////////////REPORT STEP 2//////////////////////////////////	
					$db_values[] = array(
					
							($no-1),
							FeedBack::date_fm(($trail_date)),
							$this->full_name($trail_username),			
							$trail_ip,
							$trail_db_table,
							$trail_action,
							$trail_page,
							$trail_browser
					
					); 
					
					/////////////////////////////////////////////////////////////////////////////
			
		}

		echo '</table>';
		
					//////////////////////////////////////////REPORT STEP 3/////////////////////////////
				$t = new TableCreator();
				$heading = "AUDIT TRAIL"; //CHANGE
				$t->open($this->full_name(user_id()), $heading);
				$t->thd($db_values);
				$t->close();
				$t->results();

				$e = new Exporter();//If error persists, the correct version is in classes/Exporter(2)
				echo $e->getDisplay($heading, $t->results());
				/////////////////////////////////////////////////////////////////////////////
				
		
		echo '</div>';
		
	}

	}
}