<?php 
Class Exporter{
	public $exportType = "";
	public $database_values ="";
	public $column_heading = array();
	//public $types = array('Select Format', 'WORD', 'EXCEL', 'CSV', 'PDF');
	public $types = array('Select Format', 'WORD', 'EXCEL');
	public function setsColumnHeadings($columns=array()){
		$this->column_heading = $columns;
	}
	public function toShow($values){
		$this->database_values = $values;
	}
	public function getDisplay($values, $form_name = "name" ){
		$this->database_values = $values;
		$output = "";
		$output .= '<form action="'.return_url().'classes/exporter.php" method="post">';
		$output .= 'Export Data: <select name="export_type" id="ExporterForm" required>';
		for($i=0; $i<count($this->types); $i++){
			if($i == 0)
				$output .= '<option value="">'.$this->types[$i].'</option>';
			else
				$output .= '<option value="'.$this->types[$i].'">'.$this->types[$i].'</option>';
		}
		$output .= '</select>';
		$output .= '<input type="hidden" name="db_values" value="'.htmlentities($values).'">';
		$output .= '<input type="hidden" name="formName" value="'.$form_name.'"/>';
		$output .= ' &nbsp; &nbsp; <input type="submit" value="Download" name="'.$form_name.'"/>';
		$output .= '</form>';
		return $output;
	}

	public function setExportHeader($type, $name){

		if($type == $this->types[1]){
			header("Content-Type:application/msword");
  			header("Expires: 0");
  			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  			header("content-disposition: attachment;filename=$name.doc");

		}else if($type == $this->types[2]){		

			header("Content-Type:application/vnd.ms-excel");
  			header("Expires: 0");
  			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  			header("content-disposition: attachment;filename=$name.xls");

		}else if($type == "csv"){
			header("Content-Type: text/csv");
			header("Content-Disposition: attachment; filename=UploadReadings.csv");
			header("Pragma: no-cache"); 
			header("Expires: 0");
			$file_ending = "csv";
				
			$file = fopen('php://output','w');
		}else if($type == "pdf"){
		
		}

	}

}

?>