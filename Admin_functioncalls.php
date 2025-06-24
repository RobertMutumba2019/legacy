<?php 

function convertToStudlyCaps($string){
	return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
}

function convertToCamelCase($string){
	return lcfirst(convertToStudlyCaps($string));
}
 $class = (portion($portion+1)=="")? 'Dashboard':portion($portion+1);
 $method = (portion($portion+2)=="")? 'index':portion($portion+2);

$class_name = convertToStudlyCaps($class);
$method_name = convertToCamelCase($method);

//echo "$class_name and $method_name";

spl_autoload_register(function ($class_name){
	$root = '';
	$file = str_replace('\\','/', $root).'classes/'.$class_name.'.inc';
	if(is_readable($file)){

		require $file;
	}
	
});


if(class_exists($class_name)){
	$class = new $class_name;
	
	if(is_callable([$class, $method_name])){
		$class->$method_name();	

	}else{
		echo '<b>'.$method_name.'</b> METHOD DOES NOT EXIST in CLASS: '.$class;
	}


	
}else{
	echo '<b>'.$class.'</b> CLASS DOES NOT EXIST';
}

//Record::registerTrail("VISITED: $class_name/$method_name/".portion($portion+3), "LINK");

$access = new AccessRights();

?>