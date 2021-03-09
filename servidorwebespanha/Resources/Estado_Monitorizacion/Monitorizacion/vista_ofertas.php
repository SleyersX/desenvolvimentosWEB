<title>OFERTAS</title>
<?php
require("./cabecera_vistas.php");

function ROJO($Texto) {  return "CONCAT('<font color=red><b>',$Texto,'</b></font>')"; }
function VERDE($Texto) { return "CONCAT('<font color=darkgreen><b>',$Texto,'</b></font>')"; }

$Vers_Conex="CONCAT('(',$Conexion,') ',IF($Table.Version IS NULL, 'Nunca accedido', $Table.Version))";

$Queries["RESERVAS"]=array ( "RESERVAS DE OFERTAS",
	array(
		"Tienda", "Reservas","Recogidas"),
		"select Tienda, SUM(Reservas), SUM(Recogidas) from Reservas_Ofertas group by tienda having SUM(Reservas)+SUM(Recogidas)>0; select '<b>TOTAL</b>',SUM(Reservas),SUM(Recogidas) from Reservas_Ofertas", 
		NULL,
		"ESP",
		"Listado de reservas/recogidas de ofertas.");

if (isset($Queries))
	foreach ($Queries as $key => $dato) {
		Show_data2($key, $dato); echo PHP_EOL;
	}
?>