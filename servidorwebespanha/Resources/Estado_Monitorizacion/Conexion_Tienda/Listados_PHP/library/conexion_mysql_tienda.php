<?php
	require_once("/home/soporteweb/tools/mysql.php");
	error_reporting(E_ERROR);
	if (empty($IP_Tienda))
		die("ERROR: No hay IP de tienda para conectar");

	$mysqli_tienda = new mysqli($IP_Tienda, "root", "", "n2a");
	
//	if (!empty($mysqli_tienda["connect_error"]))
//		die();
 	
	mysqli_set_charset($mysqli_tienda, "utf8");

//	if (strtoupper($_SESSION["usuario"])=="VMA001ES")
//		var_dump($mysqli_tienda);
?>