
<?php    

class DbSunServer{
	// The database connection
	protected static $connection;
	public $number_of_rows;
	public $error = array();
	/**
	 * Connect to the database
	 * 
	 * @return bool false on failure / mysqli MySQLi object instance on success
	 */
	public function connect() {
		
		// Try and connect to the database
		if(!isset(self::$connection)) {
			// Load configuration as an array. Use the actual location of your configuration file
			// Put the configuration file outside of the document root
			
			self::$connection = new PDO('sqlsrv:server=CENTENARY\SQLEXPRESS;Database=SunsystemsData;Encrypt=0', 'sunepay', 'sunepay');
			
		}
	
		// If connection was not successful, handle the error
		if(self::$connection === false) {
			// Handle error - notify administrator, log to a file, show an error screen, etc.
			return false;
		}
		return self::$connection;
	}
	
	/**
	 * Query the database
	 *
	 * @param $query The query string
	 * @return mixed The result of the mysqli::query() function
	 */
	
	
	public function query($query, $params = array()) {
		// Connect to the database
		$connection = $this -> connect();
		
		// Query the database
		//$result = $connection -> query($query);
		
		try{
			
			// Query the database
			$result = $connection -> prepare($query);	
			
			//binding params
			$x=1;
			foreach($params as $param=>&$value){
				
				$result->bindParam($param, $value);//, $this->type($value)
			}	
			$result->execute();
			
			$this->error = $result->errorInfo();
		}catch(PDOException $e){
			$this->error = $e->getMessage();
		}
		
		return $result;
	}
	
	/**
	 * Fetch rows from the database (SELECT query)
	 *
	 * @param $query The query string
	 * @return bool False on failure / array Database rows on success
	 */
	public function select($query, $params=array()) {

		$rows = array();
		$result = $this -> query($query, $params);
		if($result === false) {
			return false;
		}
		$x=0;

		while ($row = $result -> fetch()) {
			$rows[] = $row;
			$x++;
		}

		if ($rows !== [] && is_array($rows[0])) {
            $this->number_of_rows = count(@$rows[0]);
        } 

		return $rows;
	}
	
	
	function insert($table, $params=array()) {
		$next_params = $params;
		try {

			//echo 'tota:'.count($params).'<br/>';
			$keys = array_keys($params);
			$fields = implode(", ", $keys);
			
			$values = ":" . implode(", :", $keys);
			
			$insert = "INSERT INTO $table ($fields) VALUES ($values)";
				
			$params = array();

			foreach ($next_params as $key => $value) {
				$params[':'.$key] = $value;
			}
			//echo '<br/>lllll tota:'.count($next_params).'<br/>';;
			
			$in = $this->query($insert, $params);

			if($table != "trail_of_users"){
				AuditTrail::registerTrail($insert, $db_id="",  $table, implode(' , ', $params));
			}
					
			return $in; 
			
		} catch(PDOException $e) {
			$this->error =  'ERROR: ' . $e->getMessage();
		}
        return null;
	}
	
	
	function update($table, $params=array(), $id = array()) {
		$next_params = $params;
		$next_id = $id;
		try {
			//echo 'tota:'.count($id).'<br/>';
			//print_r($id);
						
			$c = array();
			foreach($params as $i=>$value){
				$c[] = "$i=:$i";
			}
			
			$w = array();
			foreach($id as $i=>$value){
				$w[] = "$i=:$i";
			}
			
			$columns = implode(' , ', $c);
			$where = implode(' AND ', $w);
			$insert = "UPDATE $table SET $columns WHERE $where";
				
			$params = array();
			
			foreach ($next_params as $key => $value) {
				$params[':'.$key] = $value;
				//echo "<br/>params[':'.$key] = $value";
			}
			
			foreach ($next_id as $key => $value) {
				$params[':'.$key] = $value;
				//echo "<br/>params[':'.$key] = $value";
			}
			//echo '<br/>lllll tota:'.count($next_params).'<br/>';;
			
			$in = $this->query($insert, $params);

			if($table != "trail_of_users"){
				AuditTrail::registerTrail($insert, $db_id="",  $table, implode(' , ', $params));
			}
					
			return $in; 
			
		} catch(PDOException $e) {
			$this->error =  'ERROR: ' . $e->getMessage();
		}
        return null;
	}
	
	
	/**
	 * Fetch the last error from the database
	 * 
	 * @return string Database error message
	 */
	public function error() {
		if ((int)$this->error[0] === 0) {
            return '';
        }
		return '<ol><li>'.implode('</li><li>', $this -> error).'</li></ol>';
	}
	public function last_id($table, $column){
		$this->connect();
		//return $connection->lastInsertId();
		$row = $this->select("SELECT MAX($column) AS max FROM $table");
		return $row[0][0]['max'];
	}
	public function trans($type){
		$connection = $this->connect();
		$type = strtolower($type);
		if ($type === "begin") {
            $connection->beginTransation();
        } elseif ($type === "rollback") {
            $connection->rollBack();
        } elseif ($type === "commit") {
            $connection->commit();
        }
	}
	public function num_rows(){
		return $this->number_of_rows;
	}
}

$sun_db = new DbSunServer();
//echo $db->quote("sdf sdf");

//$x = $db->insert("department",["dept_date_added"=>43324, "dept_name"=>"JOHN MALE Jseph","dept_added_by"=>11]);

//$x = $db->update("department", ["dept_date_added"=>110011], ['dept_id'=>20]);
//echo $db->error();

//$x = $db->select("SELECT * FROM department");
//echo '<pre>';
//print_r($x);
//echo '</pre>';
//echo $db->error();
?>
