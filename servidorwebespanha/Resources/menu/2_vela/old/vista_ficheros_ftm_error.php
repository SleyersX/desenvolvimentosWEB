<title>ERRORES FTM</title>
<?php
require("../Monitorizacion/cabecera_vistas.php");

// if (SoyYo()) { echo '<pre>'; print_r($_SESSION); print_r($_COOKIE); print_r($_SERVER); echo '</pre>'; }

$Queries["FTM_Error1"]=array (
	"Ficheros de ventas no enviados",
	array("Tienda", "Fichero","Fecha","Size"), "select Tienda,Fichero,Fecha,Size from FTM_Error WHERE Clave=1", "select Tienda,Fichero,Fecha,Size from FTM_Error WHERE Clave=1", "ESP");

$Queries["FTM_Error2"]=array (
	"Ficheros de ventas en error",
	array("Tienda", "Fichero","Fecha","Size"), "select Tienda,Fichero,Fecha,Size from FTM_Error WHERE Clave=2", "select Tienda,Fichero,Fecha,Size from FTM_Error WHERE Clave=2", "ESP");

$Queries["FTM_Error3"]=array (
	"Ficheros de pedidos en error",
	array("Tienda", "Fichero","Fecha","Size"), "select Tienda,Fichero,Fecha,Size from FTM_Error WHERE Clave=3", "select Tienda,Fichero,Fecha,Size from FTM_Error WHERE Clave=3", "ESP");


if (isset($Queries))
	foreach ($Queries as $key => $dato) {
		Show_data2($key, $dato); echo PHP_EOL;
	}
if (isset($Queries_s))
	foreach ($Queries_s as $key => $dato) {
		Show_data_sin_query($key, $dato);
	}
?>
