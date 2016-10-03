<?php
	
	session_start();
	
	require_once "aplicacion/config.php";
	
	$controller = (isset($_GET["controller"]))? $_GET["controller"]: DEFAULT_CONTROLLER;
	
	require_once "controllers/$controller.php";	

?>
