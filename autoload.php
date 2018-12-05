<?php

spl_autoload_register(function($class_name){
	$ds = DIRECTORY_SEPARATOR;

	//WeWork
	if(preg_match('/^WeWork/', $class_name)){
		$path = str_replace('WeWork', 'WeWork\src', $path);
		$path = str_replace('\\', $ds, trim($class_name, '\\'));
		$file = str_replace('WeWork','',__DIR__).$path.'.php';
		if(is_file($file)){
			include_once $file;
		}
	}
});