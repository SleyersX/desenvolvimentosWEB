<title>Vista PV</title>
<?php
require("./cabecera_vistas.php");

$Queries["Vista_PV"]=array ( "Numero de TPV Fiscal",
	array("Tienda", "Caja","PV"),
	"select Tienda,Caja,PV from Info_PV",
	"",
	"ARG");


if (isset($Queries))
	foreach ($Queries as $key => $dato) {
		Show_data2($key, $dato); echo PHP_EOL;
	}
if (isset($Queries_s))
	foreach ($Queries_s as $key => $dato) {
		Show_data_sin_query($key, $dato);
	}
?>
