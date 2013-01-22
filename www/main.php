<?php
require_once(dirname(__FILE__).'/../mfw/initialize.php');
require_once APP_ROOT.'/mainmodules/MainModules.php';

try{
	mfwServerEnv::setEnv('metahub_vm');

	list($headers,$content) = MainModules::execute();
	foreach($headers as $h){
		header($h);
	}
	echo $content;
}
catch(Exception $e){
	header("HTTP/1.1 500 Internal Server Error");
	echo "<h1>500 Internal Server Error</h1>\n";
	echo $e->getMessage();
}

