<?php 
Class Image{
	
	private $allowed_formats = array(
		"image/jpeg", 
		"image/jpg", 
		"image/png", 
		"image/gif",
		//"audio/mpeg",
		//"video/mp4",
		//"audio/mp3",
		//"application/vnd.openxmlformats-officedocument.wordprocessingml.document", 
		//"application/vnd.ms-powerpoint", 
		//"application/zip", 
		"application/pdf", 
		//"application/msword"
		);
	private $error="";
	private $image_link="";
	private $image_empty = true;
	
	public function upload($file,$folder_name="uploads",$path=""){
		//echo $_FILES[$file]["type"];
		//exit();
		$upload_errors = array(
			'There is no error, the file uploaded with success.', 
			'The uploaded file exceeds the <b>2MBs</b> upload_max_filesize.', 
			'The uploaded file exceeds the MAX_FILE_SIZE <b>(2MBs)</b> for the form', 
			'The uploaded file was only partially uploaded.', 
			'No file was uploaded. Please attach a file', 
			'Missing a temporary folder.', 
			'Failed to write file to disk', 
			'File extension no allowed' );

		$size=$_FILES[$file]['size'];
		$required_size = 1024*1024*2; //2097152 => 2MBs 

		if($size >= $required_size){
			$this->error = $upload_errors[1];
		}elseif ($_FILES[$file]["error"] > 0){//errors
			$this->error = "" . $upload_errors[$_FILES[$file]["error"]] . "";					
		}elseif($_FILES[$file]["name"] != ""){			
			$this->image_empty = false;
			if (in_array($_FILES[$file]["type"], $this->allowed_formats))
				//if(1)
			{
				if ($_FILES[$file]["error"] > 0) {
                    //errors
                    $this->error = "" . $upload_errors[$_FILES[$file]["error"]] . "";
                } elseif (file_exists($path.$folder_name.'/' . $_FILES[$file]["name"])) {
                    //checking if the image is already uploaded by another user
                    $this->error = ''.$_FILES[$file]["name"] . " already exists.</br> ";
                } else{
						$uploaded_file = strtolower($path.$folder_name.'/'.time().'_'.basename(str_replace(' ', '_', $_FILES[$file]['name'])));

						if(move_uploaded_file($_FILES[$file]['tmp_name'],$uploaded_file)){
							//paths to resize and to store the resized image
							$this->image_link = strtolower($folder_name.'/'.time().'_'.basename(str_replace(' ', '_', $_FILES[$file]['name'])));
							$this->error = 'Uploaded';
						}else{
							$this->error = 'Not uploaded';
						}
					}
			}else{
				$this->error = '<br/>This File Format not allowed. <b>Only Images are accepted</b>';
			}
		}else{			
			$this->error = $upload_errors[$_FILES[$file]["error"]];
		}

		if($this->error !== '' && $this->error !== '0'){
			$image_path = '../'.$this->image_link;
			if (file_exists($image_path)) {
				chmod($image_path, 0644);
			    unlink($image_path);
			    //echo 'Deleted old image';
			}else{
				//echo 'Image Not deleted';
			}
		}
	}
		
	public function imageLink(){
		return $this->image_link;
	}
	
	public function unLink($url=""){
		return unlink($url);
	}
	
	public function isError(){
		return true;//$this->is_error;
	}
	
	public function errorMessage(){
		return $this->error;
	}

	public function isEmpty(){
		return $this->image_empty;
	}
	
	public function name($image_name)
    {
    }
}

