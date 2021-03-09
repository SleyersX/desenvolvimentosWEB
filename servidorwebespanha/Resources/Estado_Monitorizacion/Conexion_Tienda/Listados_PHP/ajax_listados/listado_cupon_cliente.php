<?php

if (!empty($_GET["opcion_cupo"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();

	foreach($_GET as $k => $d) $$k=$d;

	require_once("../library/conexion_mysql_tienda.php");
		
	switch($opcion_cupo) {
		case "get_cupo":
			break;
			
		case "get_list_cupo":
			require_once("../library/json_get_list_cupo_clie.php");
			break;
	}
	@mysqli_close($mysqli_tienda);
	exit;
}

?>