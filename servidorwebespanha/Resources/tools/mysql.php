<?php

function myQUERY($Query, $verbose=false) {
	if (!isset($GLOBALS['mysqli'])) die ("ERROR: No hay MySQL cargado...");
	$db=$GLOBALS['mysqli'];

	if ($db->multi_query($Query)) {
		do {
			if ($Res = $db->store_result()) {		
				while ($fila = $Res->fetch_row()) { if ($verbose) var_dump($fila); $Vista[]=$fila; }
				$Res->free();
			}
		} while (@$db->next_result());
	}
	if ($verbose) {
		printf("<pre>Query: %s\n - Errorcode: %d (%s)\n", $Query, $db->errno, $db->error);
		var_dump($db);
		echo "</pre>";
	}
//	var_dump($Vista);
	return isset($Vista)?$Vista:NULL;
}

function myQUERY_Actu($Query, $verbose=false) {
	switch($_SERVER['SERVER_ADDR'] ) {
			case "10.208.162.17": return myQUERY_remoto("10.208.162.6", $Query, $verbose);
			default: return myQUERY($Query, $verbose);  
	}
}

function myQUERY_remoto($server, $Query, $verbose=false) {
	if (empty($server)) die("ERROR: no hay servidor en la consulta MYSQL");

	$mysqli_remoto = new mysqli($server, "soporteweb", "soporteweb", "soporteremotoweb");
	if (!$mysqli_remoto) die("ERROR: no se puede establecer conexion con el server: $server");

	if ($mysqli_remoto->multi_query($Query)) {
		do {
			if ($Res = $mysqli_remoto->store_result()) {
				while ($fila = $Res->fetch_row()) { $Vista[]=$fila; }
				$Res->free();
			}
		} while (@$mysqli_remoto->next_result());
	}
	if ($verbose && $_SESSION['usuario']=="vma001es")
		printf("Query: %s\n - Errorcode: %d (%s)\n", $Query, $mysqli_remoto->errno, $mysqli_remoto->error);

	@mysql_close($mysqli_remoto);
	return isset($Vista)?$Vista:NULL;
}

function open_mysql_tienda($tienda,$caja=1) {
	global $mysqli;
	if ($mysqli) {
		$ret=myQUERY("SELECT IP FROM tmpTiendas WHERE numerotienda=".$tienda);
		list($i1,$i2,$i3,$i4) = explode(".",$ret[0][0]);
		$i4=$i4+($caja-1);
		$IP_Tienda=sprintf("%d.%d.%d.%d",$i1,$i2,$i3,$i4);
	}
	$mysqli_tienda = new mysqli($IP_Tienda, "root", "", "n2a");
	if (!$mysqli_tienda) { die("ERROR! No ha sido posible conectar a TPV $caja de la tienda $tienda: $IP_Tienda"); }
	return $mysqli_tienda;
}

function close_mysql_tienda($mysqli_tienda) {
	@mysqli_close($mysqli_tienda);
}

function myQUERY_Tienda($db, $Query, $verbose=false) {
	if ($db->multi_query($Query)) {
		do {
			if ($Res = $db->store_result()) {
				while ($fila = $Res->fetch_row()) { $Vista[]=$fila; }
				$Res->free();
			}
		} while (@$db->next_result());
	}
	if ($verbose && $_SESSION['usuario']=="vma001es")
		printf("Query: %s\n - Errorcode: %d (%s)\n", $Query, $db->errno, $db->error);
	return isset($Vista)?$Vista:NULL;
}

function CLOSE_BBDD($db=NULL) {
	if (!$db) $db=$GLOBALS['mysqli'];
	if (!$db) die ("ERROR: No hay MySQL cargado...");
	@mysqli_close($db);
}

if (!isset($mysqli)) {
	$mysqli = new mysqli("localhost", "soporteweb", "soporteweb", "soporteremotoweb");
// 	$mysqli = new mysqli("10.208.162.6", "soporteweb", "soporteweb", "soporteremotoweb");
	if (mysqli_connect_errno()) die ('Falló la conexión: '.mysqli_connect_error());
	mysqli_set_charset($mysqli, "utf8");
//	mysql_query("SET NAMES 'utf8'", $mysqli);
//	mysql_query("SET CHARACTER_SET 'utf8'", $mysqli);
}

?>
