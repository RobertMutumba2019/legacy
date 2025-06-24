<?php 
Class PublicHolidays{
	private $days = array(
		"01/01"=>array(
			"from"=>"01", 
			"to"=>"15", 
			"message"=>"New Year",
		),

		"01/26"=>array(
			"from"=>"01", 
			"to"=>"26", 
			"message"=>"Liberation Day",
		),

		"02/14"=>array(
			"from"=>"14", 
			"to"=>"14", 
			"message"=>"Valentine's Day",
		),

		"02/16"=>array(
			"from"=>"16", 
			"to"=>"16", 
			"message"=>"Archbishop Janan Luwum's Day",
		),

		"03/08"=>array(
			"from"=>"08", 
			"to"=>"08", 
			"message"=>"Women's Day",
		),

		"05/01"=>array(
			"from"=>"01", 
			"to"=>"01", 
			"message"=>"Labour Day",
		),

		"06/03"=>array(
			"from"=>"03", 
			"to"=>"03", 
			"message"=>"Martyr's Day",
		),

		"12/25"=>array(
			"from"=>"18", 
			"to"=>"25", 
			"message"=>"Merry Christmas",
		),

		"12/26"=>array(
			"from"=>"26", 
			"to"=>"26", 
			"message"=>"Independence Day",
		),

		// "5/1"=>array(
		// 	"from"=>"3", 
		// 	"to"=>"6", 
		// 	"message"=>"Independence Day",
		// ),

		//Company Days
	);


	public function getDay($time){
		//check if time with days
		$day_month_now = date('d/m', $time);
		foreach($this->days as $day=>$details){
			$month = current(explode('/', $day));
			$from = $details['from'];
			$to = $details['to'];
			$year = date('Y');

			$actual = @strtotime($year.'-'.$month.'-'.end(explode('/', $day)).' 00:00:00');
			$start_date = strtotime($year.'-'.$month.'-'.$from.' 00:00:00');
			$end_date = strtotime($year.'-'.$month.'-'.$to.' 23:59:59');

			if($time >= $start_date && $time <= $end_date){
				echo "<div style='text-align:center;font-size:1.2em; color:yellow; margin:10px;'>Happy ".$details['message']."<br/>".strtoupper(date('M-d', $actual))."</div>";
			}elseif($time >= $start_date && $time <= $end_date){
				echo "<div style='text-align:center;font-weight:1.2em; color:yellow; margin:10px;'>Happy ".$details['message']."<br/>".strtoupper(date('M-d', $actual))."</div>";
			}
		}
	}
}