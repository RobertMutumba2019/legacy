<?php 
Class FeedBack{
	
	public static function errors($error_array) {
		echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
		echo "Please review the following fields:<br />";
		foreach($error_array as $error) {
			echo " - " . $error . "<br />";
		}
		echo "</div>";
	}
	public static function error($string="") {
		echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';	
		echo $string;
		echo "</div>";
	}

	public static function success($string="Successfully saved"){
		echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$string.'</div>';
	}

	public static function warning($string=""){
		echo '<div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$string.'</div>';
	}
	public static function error_r($string="") {
		return '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$string."</div>";
	}

	public static function success_r($string="Successfully saved"){
		return '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$string.'</div>';
	}

	public static function warning_r($string=""){
		return '<div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$string.'</div>';
	}
	
	public static function refresh($seconds = 1, $link = ""){
		
		$seconds = ($seconds >= 4)?$seconds:1; 

		if($link == ""){
			echo '<meta http-equiv="refresh" content="'.$seconds.'"/>';
		}else{
			echo '<meta http-equiv="refresh" content="'.$seconds.';'.$link.'"/>';
		}
	}
	
	public static function redirect($url){
		header("Location:$url");
	}
	
	public static function status($start_date, $end_date){
		$time = time();
		if($start_date <= $time && $end_date >= $time){
				return '<button class="btn btn-xs btn-success"><i class="fa fa-fw fa-check-circle"></i>Event Started </button>';
		}elseif($start_date >= $time && $end_date >= $time){
			return '<button class="btn btn-xs btn-danger"><i class="fa fa-fw fa-times"></i> Not Active </button>';
		}else{
			return '<button class="btn btn-xs btn-danger"><i class="fa fa-fw  fa-times"></i>Event Closed</button>';
		}		
	}
	
	public static function date_fm($time){
		//return date("d",$time)."<sup>".date("S", $time)."</sup> ".date("M", $time).date(" Y", $time).', '.date('h:i:s A', $time);
		if (empty($time)) {
            return "";
        }
		return "".date("M dS Y, h:i:s A", $time);
	}
	
	public static function date_tr($time){
		//return date("d",$time)."<sup>".date("S", $time)."</sup> ".date("M", $time).date(" Y", $time).', '.date('h:i:s A', $time);
		
		return "".date("M", $time).' '.date("d",$time).date("S", $time).' '.date(" Y", $time);
	}
	
	public static function date_s($time){
		//return date("d",$time)."<sup>".date("S", $time)."</sup> ".date("M", $time).date(" Y", $time).', '.date('h:i:s A', $time);
		
		return "".date("M", $time).' '.date("d",$time).date("S", $time).' '.date(" Y", $time);
	}
	public static function dateFormatFixer($d, $time=0, $format='yyyy-mm-dd'){
		//echo '<br/>Date: '.$d.'<br/>';
		//echo '<br/>Format: '.$format.'<br/>';
		$separator = '-';
		$date = explode($separator, $d);

		if(count($date)==1){
			$separator = '/';
			$a = explode($separator, $date[0]);
		}else{
			$a = $date;
		}


		if($format == 'yyyy-mm-dd'){
			$year = strtotime($a[0].'-'.$a[1].'-'.$a[2].' '.$time);
		}elseif($format == 'mm-dd-yyyy'){			
			$year = strtotime($a[2].'-'.$a[0].'-'.$a[1].' '.$time);
		}elseif($format == 'dd-mm-yyyy'){			
			$year = strtotime($a[2].'-'.$a[1].'-'.$a[0].' '.$time);
		}elseif($format == 'yyyy-dd-mm'){			
			$year = strtotime($a[0].'-'.$a[2].'-'.$a[1].' '.$time);
		}else{
			$year = strtotime($a[0].'-'.$a[1].'-'.$a[2].' '.$time);
		}

		return $year;
		
	}

	public static function check_status($bool, $true="Hidden", $false="Showing"){
		if($bool==0){
			return $true;
		}else{
			return $false;
		}
	}
	
	public static function display_small($content, $length="60", $start="0"){
		if(strlen($content) > $length){
			return substr($content,$start, $length)."...";
		}
		return $content;
	}
	
	public static function user_status($id){
		
		if($id==0){
			return "Saved";
		}elseif($id == 1){
			return "Sent for Approving";
		}elseif($id == 10){
			return "Approved";
		}
        return null;
		
	}

	public static function ip_address(){
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

		return $ipaddress;
	}
	
    public static function sendmail1($to,$subject,$message,$name, $telephone="+256788229210"){ 
		return 0;
	}
	public static function sendmailzzzzzzzzz($to,$subject,$message,$name, $telephone="+256788229210"){
		$from = 'info@codeeagles.com'; 
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		 
		// Create email headers
		$headers .= 'From: '.$from."\r\n".
		    'Reply-To: '.$from."\r\n" ;
			
		// POST data in array
		$post = [
			'to_email' => $to,
			'subject' => $subject,
			'message' => $message,
			'headers' => $headers,
		];

		// Create a new cURL resource with URL to POST
		$ch = curl_init('http://www.codeeagles.com/email-api.php');

		// We set parameter CURLOPT_RETURNTRANSFER to read output
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Let's pass POST data
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		// We execute our request, and get output in a $response variable
		$response = curl_exec($ch);

		// Close the connection
		curl_close($ch);
		
		if(!$response) {		
		  return 0;
		} else {
			return 1;
		}
    }

    public static function sendmail($to,$subject,$message,$name, $telephone="+256788229210"){
    	$subject = "STORE REQUISITION ".$subject;
		if(!isInternetOn()){
			//FeedBack::warning("Email not sent, No Internet, Try checking the Network cables, Modem or Router");
			return 0;
		}
		require_once(ABSPATH.'../PHPMailer-master/src/PHPMailer.php');
		require_once(ABSPATH.'../PHPMailer-master/src/SMTP.php');
		$mail             = new PHPMailer();
		$body             = $message;
		$mail->IsSMTP();
		//$mail->SMTPDebug = 2;

		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);

		/////////////////////////////////////////////////
		// $mail->Host       = "10.222.140.233";                  
		// $mail->SMTPAuth   = false;
		// $mail->Port       = 25;
		// $mail->Username   = "";
		// $mail->Password   = "";
		// //$mail->SMTPSecure = 'tls';

		$mail->Host       = "smtp.gmail.com";                  
		$mail->SMTPAuth   = true;
		$mail->Port       = 587;
		$mail->Username   = "fse@flaxem.com";
		$mail->Password   = "p@55W0RD";
		$mail->SMTPSecure = 'tls';

		/////////////////////////////////////////////////
		$mail->SetFrom('sunsystems@flaxem.com', 'Stores requisitioning');
		$mail->AddReplyTo("sunsystems@flaxem.com","Stores requisitioning");


		$mail->Subject    = $subject;
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
		$mail->MsgHTML($body);
		$address = $to;
		$mail->AddAddress($address, $name);
		if(!$mail->Send()) {
		  //echo "Failed ".$mail->ErrorInfo;
		  return 0;
		} else {
			//echo "Sent ";
			return 1;
		}
    }
    
    public static function sendmailz($to,$subject,$message,$name, $telephone="+256788229210"){
    	$subject = "STORE REQUISITION ".$subject;
		if(!isInternetOn()){
			FeedBack::warning("Email not sent, No Internet, Try checking the Network cables, Modem or Router");
			return 0;
		}
		require_once(ABSPATH.'../PHPMailer-master/src/PHPMailer.php');
		require_once(ABSPATH.'../PHPMailer-master/src/SMTP.php');
		$mail             = new PHPMailer();
		$body             = $message.$msg;
		$mail->IsSMTP();
		//$mail->SMTPDebug = 2;

		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);

		/////////////////////////////////////////////////
		// $mail->Host       = "10.222.140.233";                  
		// $mail->SMTPAuth   = false;
		// $mail->Port       = 25;
		// $mail->Username   = "";
		// $mail->Password   = "";
		//$mail->SMTPSecure = 'tls';


		$mail->Host       = "smtp.gmail.com";                  
		$mail->SMTPAuth   = true;
		$mail->Port       = 587;
		$mail->Username   = "fse@flaxem.com";
		$mail->Password   = "p@55W0RD";
		$mail->SMTPSecure = 'tls';

		/////////////////////////////////////////////////
		$mail->SetFrom('sunsystems@flaxem.com', 'Stores requisitioning');
		$mail->AddReplyTo("sunsystems@flaxem.com","Stores requisitioning");


		$mail->Subject    = $subject;
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
		$mail->MsgHTML($body);
		//$address = $to;
		foreach($to as $address){
			$mail->AddAddress($address, $name);
		}
		if(!$mail->Send()) {
		  //echo "Failed ".$mail->ErrorInfo;
		  return 0;
		} else {
			//echo "Sent ";
			return 1;
		}
    }
    public static function sms($message, $number="+256788229210"){ return 0; }
	public static function sms1($message, $number="+256788229210"){
		//return true;
		// Be sure to include the file you've just downloaded
		require_once(__DIR__ . '/SMS/AfricasTalkingGateway.php');
		// Specify your login credentials
		$username   = "musiitwa";
		$apikey     = "a9cb69df39cecf8d58f4ce2d69d4e6c6b9dd1250196298e1533bed74ec3733d6";
		// NOTE: If connecting to the sandbox, please use your sandbox login credentials
		// Specify the numbers that you want to send to in a comma-separated list
		// Please ensure you include the country code (+256 for Uganda in this case)


		$nums = array($number);
        $recipients = "".implode(',', $nums);
        // And of course we want our recipients to know what we really do
        $message = "UEDCL FUEL & FLEET MGT SYSTEM : ".$message;
        // Create a new instance of our awesome gateway class
        $gateway    = new AfricasTalkingGateway($username, $apikey);
        // NOTE: If connecting to the sandbox, please add the sandbox flag to the constructor:
        /*************************************************************************************
        					 ****SANDBOX****
        		$gateway    = new AfricasTalkingGateway($username, $apiKey, "sandbox");
        		**************************************************************************************/
        // Any gateway error will be captured by our custom Exception class below, 
        // so wrap the call in a try-catch block
        try 
		{ 
		  // Thats it, hit send and we'll take care of the rest. 
		  $results = $gateway->sendMessage($recipients, $message);
					
		  foreach($results as $result) {
			// status is either "Success" or "error message"
			//echo " Number: " .$result->number;
			//echo " Status: " .$result->status;
			//echo " MessageId: " .$result->messageId;
			//echo " Cost: "   .$result->cost."\n\n";
			echo '';
		  }
		}
		catch ( AfricasTalkingGatewayException $e )
		{
		 // @echo "Encountered an error while sending: ".$e->getMessage();
		}
	}
	
	public static function n($x){}
	public static function number_to_words($num){
		$num    = ( string ) ( ( int ) $num );
	   
		if (( int ) ( $num ) && ctype_digit( $num )) {
            $words  = array( );
            $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );
            $list1  = array('','one','two','three','four','five','six','seven',
				'eight','nine','ten','eleven','twelve','thirteen','fourteen',
				'fifteen','sixteen','seventeen','eighteen','nineteen');
            $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
				'seventy','eighty','ninety','hundred');
            $list3  = array('','thousand','million','billion','trillion',
				'quadrillion','quintillion','sextillion','septillion',
				'octillion','nonillion','decillion','undecillion',
				'duodecillion','tredecillion','quattuordecillion',
				'quindecillion','sexdecillion','septendecillion',
				'octodecillion','novemdecillion','vigintillion');
            $num_length = strlen( $num );
            $levels = ( int ) ( ( $num_length + 2 ) / 3 );
            $max_length = $levels * 3;
            $num    = substr( '00'.$num , -$max_length );
            $num_levels = str_split( $num , 3 );
            foreach( $num_levels as $num_part )
			{
				$levels--;
				$hundreds   = ( int ) ( $num_part / 100 );
				$hundreds   = ( $hundreds !== 0 ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : '' ) . ' ' : '' );
				$tens       = ( int ) ( $num_part % 100 );
				$singles    = '';
			   
				if( $tens < 20 )
				{
					$tens   = ( $tens !== 0 ? ' ' . $list1[$tens] . ' ' : '' );
				}
				else
				{
					$tens   = ( int ) ( $tens / 10 );
					$tens   = ' ' . $list2[$tens] . ' ';
					$singles    = ( int ) ( $num_part % 10 );
					$singles    = ' ' . $list1[$singles] . ' ';
				}
				$words[]    = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' );
			}
            $commas = count( $words );
            if( $commas > 1 )
			{
				$commas -= 1;
			}
            $words  = implode( ', ' , $words );
            //Some Finishing Touch
            //Replacing multiples of spaces with one space
            $words  = trim( str_replace( ' ,' , ',' , trim_all( ucwords( $words ) ) ) , ', ' );
            if( $commas !== 0 )
			{
				$words  = str_replace_last( ',' , ' ' , $words );
			}
            return $words;
        } elseif (( int ) $num === 0) {
            return 'Zero';
        }
		return '';
	}
	
	public static function password_generator(){
		//////////////////////////////////////////////////////////////////////////
		//		CODE generated by code eagles - joseph musiitwa - 2015/02/21	//
		//////////////////////////////////////////////////////////////////////////

		//range() increments two parameters and creates an array, they can be a third parameter for numbers ie step to increment.
		$low_case = range('a', 'z'); 
		$cap_letters = range('A', 'Z');
		$numbers = range(0, 9);
		$non_alpha_num = array('?', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '+', '{', '}', '[', ']'); // you can add more as you want
		
		//reordering the array
		shuffle($low_case);
		shuffle($cap_letters);
		shuffle($numbers);
		shuffle($non_alpha_num);
		
		//the array collector is used to collect charaters
		//6 low case characters, 2 upper case, 2 numbers and 2 non-alphanumeric
		$collector = array( 
						$low_case[0], $low_case[1], 
						$cap_letters[0], $cap_letters[1],
						$numbers[0], $numbers[1], 
						$non_alpha_num[0], $non_alpha_num[1], 
						$low_case[2], $low_case[3],
						$low_case[4], $low_case[5],
						);
		
		shuffle($collector);//reordering the array

		//removed an error involved in feedback.inc.
		$generated_password = '';
		
		foreach ($collector as $value) {
			$generated_password .= $value;
		}
		
		return $generated_password;
	}
	
	
}

function trim_all( $str , $what = NULL , $with = ' ' )
	{				
		if	( $what === NULL )
		{
			//  Character      Decimal      Use
			//  "\0"            0           Null Character
			//  "\t"            9           Tab
			//  "\n"           10           New line
			//  "\x0B"         11           Vertical Tab
			//  "\r"           13           New Line in Mac
			//  " "            32           Space
		   
			$what   = "\\x00-\\x20";    //all white-spaces and control chars
		}
	   
		return trim( preg_replace( "/[".$what."]+/" , $with , $str ) , $what );
	}
	
	function str_replace_last( $search , $replace , $str ) {
		if( ( $pos = strrpos( $str , $search ) ) !== false ) {
			$search_length  = strlen( $search );
			$str    = substr_replace( $str , $replace , $pos , $search_length );
		}
		return $str;
	}

	function isInternetOn(){
    	$connected = @fsockopen("www.example.com", 80); 
    	if($connected){
        	$is_conn = true; 
        	fclose($connected);
    	}else{
        	$is_conn = false;
    	}
    	return $is_conn;

	}


?>