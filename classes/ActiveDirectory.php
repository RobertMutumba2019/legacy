<?php 
//error_reporting(null);
//include("../classes/init.inc"); 
class ActiveDirectory extends BeforeAndAfter{
	
	public function getLinks(){
		
		$links = array(
			array()
		);
		
		return $links;
	}

    public function login($username, $password){

        $db = new Db();
        $password = md5($password);
        $sql = "SELECT * FROM sysuser WHERE (user_email = '$username' OR user_name = '$username') AND user_password = '$password'";
        $select = $db->select($sql);

        $errors = array();

        if(empty($db->num_rows())){
            $errors["message"] = "Could not Login, Contact Admin";
            $errors["status"] = False;
        }else{  
            extract($select[0]);

            $errors["status"] = True;
            $errors["message"] = array(
                "name"=>'', //$info[$x]['cn'][0],
                "email"=>$user_email, ///'josemusiitwa@gmail.com',//$info[$x]['mail'][0],
                "extention"=>$user_telephone,//$info[$x]['telephonenumber'][0],
                "user_role"=>'',//$member
                "user_id"=>$user_id,
            );
        }

        return $errors;
    }

    public function login22($username, $password){   
        $username = explode('@', $username);
        $username = $username[0]; 
        $errors = array();
        $cnx = ldap_connect(AD_DNS_NAME) or die("Could not connect to LDAP");
        ldap_set_option($cnx, LDAP_OPT_PROTOCOL_VERSION, 3);    //Set the LDAP Protocol used by your AD service
        ldap_set_option($cnx, LDAP_OPT_REFERRALS, 0);       //This was necessary for my AD to do anything
        if(!ldap_bind($cnx,$username.AD_DOMAIN,$password)){ 
            $errors["message"] = "Could not Login, Contact Admin";
            $errors["status"] = False;
        }else{
            $errors["status"] = True;

            $SearchFor=$username;
            $SearchField="samaccountname";          
            $LDAPFieldsToFind = array("*");  
            $filter="($SearchField=$SearchFor*)";   //Wildcard is * Remove it if you want an exact match
            $sr=ldap_search($cnx, AD_DN, $filter, $LDAPFieldsToFind);
            $info = ldap_get_entries($cnx, $sr);
           
            for ($x=0; $x<$info["count"]; $x++) {
                // echo '<pre>';
                // print_r($info[$x]);
                // echo '</pre>';

                //echo "Name: " .$info[$x]['cn'][0]."<br/>";
                // echo "Windows Login: " .$info[$x]['samaccountname'][0]."<br/>";
                // echo "Extention: " .$info[$x]['telephonenumber'][0]."<br/>";
                // echo "Email: " .$info[$x]['mail'][0]."<br/>";
                // echo "Office: " .$info[$x]['physicaldeliveryofficename'][0]."<br/>";
                // echo "Main System UID: " .$info[$x]['description'][0]."<br/>";
                // echo "Job Title: " .$info[$x]['title'][0]."<br/>";
                // echo "Department: " .$info[$x]['department'][0]."<br/>";
                // $info[$x]['manager'][0] = explode(",",$info[$x]['manager'][0]);
                // $info[$x]['manager'][0] = str_replace('CN=','',$info[$x]['manager'][0][0]);
                // echo "Line Manager: " .$info[$x]['manager'][0]."<br/>";
                // echo "Company: " .$info[$x]['company'][0]."<br/>";

                // $db = new Db();
                // $select = $db->select("SELECT * FROM user_role");
                // print_r($select[0]);
                
                // if($info[$x]['memberof']['count'] != 0){
                //     foreach($info[$x]['memberof'] as $key){
                //         $key = explode(",",$key);
                //         $key = str_replace('CN=','',$key[0]);
                //         //echo "Member of: " .$key."<br/>";
                //         $member = $this->rgf("user_role", $key, "ur_name", "ur_id");
                //         if($member){
                //             break;
                //         }
                //     }
                // }
                //echo "\n\n";
                // if(empty($member)){
                //     $member = 0;
                // }
                //echo "Member:".$member;

                $errors["message"] = array(
                    "name"=>$info[$x]['cn'][0],
                    "email"=>$info[$x]['mail'][0],
                    "extention"=>$info[$x]['telephonenumber'][0],
                );
            }
            //echo"</pre>";
        }

        return $errors;
    }
    
    public function login2($username, $password){   
        $username = explode('@', $username);
        $username = $username[0]; 
        $errors = array();
        $cnx = ldap_connect(AD_DNS_NAME) or die("Could not connect to LDAP");
        ldap_set_option($cnx, LDAP_OPT_PROTOCOL_VERSION, 3);    //Set the LDAP Protocol used by your AD service
        ldap_set_option($cnx, LDAP_OPT_REFERRALS, 0);       //This was necessary for my AD to do anything
        if(!ldap_bind($cnx,$username.AD_DOMAIN,$password)){ 
            $errors["message"] = "Could not Login, Contact Admin";
            $errors["status"] = False;
        }else{
            $errors["status"] = True;

            $SearchFor=$username;
            $SearchField="samaccountname";          
            $LDAPFieldsToFind = array("*");  
            $filter="($SearchField=$SearchFor*)"; 
           // $filter="($SearchField=*)";   //Wildcard is * Remove it if you want an exact match
            $sr=ldap_search($cnx, AD_DN, $filter, $LDAPFieldsToFind);
            $info = ldap_get_entries($cnx, $sr);
           
            for ($x=0; $x<$info["count"]; $x++) {
                echo '<pre>';
                print_r($info[$x]);
                echo '</pre>';

                //echo "Name: " .$info[$x]['cn'][0]."<br/>";
                // echo "Windows Login: " .$info[$x]['samaccountname'][0]."<br/>";
                // echo "Extention: " .$info[$x]['telephonenumber'][0]."<br/>";
                // echo "Email: " .$info[$x]['mail'][0]."<br/>";
                // echo "Office: " .$info[$x]['physicaldeliveryofficename'][0]."<br/>";
                // echo "Main System UID: " .$info[$x]['description'][0]."<br/>";
                // echo "Job Title: " .$info[$x]['title'][0]."<br/>";
                // echo "Department: " .$info[$x]['department'][0]."<br/>";
                // $info[$x]['manager'][0] = explode(",",$info[$x]['manager'][0]);
                // $info[$x]['manager'][0] = str_replace('CN=','',$info[$x]['manager'][0][0]);
                // echo "Line Manager: " .$info[$x]['manager'][0]."<br/>";
                // echo "Company: " .$info[$x]['company'][0]."<br/>";

                // $db = new Db();
                // $select = $db->select("SELECT * FROM user_role");
                // print_r($select[0]);
                
                // if($info[$x]['memberof']['count'] != 0){
                //     foreach($info[$x]['memberof'] as $key){
                //         $key = explode(",",$key);
                //         $key = str_replace('CN=','',$key[0]);
                //         //echo "Member of: " .$key."<br/>";
                //         $member = $this->rgf("user_role", $key, "ur_name", "ur_id");
                //         if($member){
                //             break;
                //         }
                //     }
                // }
                //echo "\n\n";
                // if(empty($member)){
                //     $member = 0;
                // }
                //echo "Member:".$member;

                $errors["message"] = array(
                    "name"=>$info[$x]['cn'][0],
                    "email"=>$info[$x]['mail'][0],
                    "extention"=>$info[$x]['telephonenumber'][0],
                );
            }
            //echo"</pre>";
        }

        return $errors;
    }
    
	
}

