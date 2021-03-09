<title>CONEXION CAPTURADORES</title>
<?php
require("../../cabecera_vistas.php");
/*
$tmp=myQUERY("Select * from conexion_capturador");
$txt="<table class='tabla2'><thead><tr><th style='text-align:center'>Tienda</th><th style='text-align:center'>Caja</th><th style='text-align:center'>Conex. HOY</th><th style='text-align:center'>Conex. TOTAL</th></tr></thead>";
foreach($tmp as $d) {
	list($tienda, $caja, $hoy, $total)=$d;
	if ($total>0)
		$txt.="<tr><td style='text-align:right'>$tienda</td><td style='text-align:right'>$caja</td><td style='text-align:right'>$hoy</td><td style='text-align:right'>$total</td></tr>";
}
$txt.="</table>";
echo "<div style='width:500; overflow:auto'>$txt</div>";
*/
$Queries["CONEX_CAPTUR"]=array ( "CONEXIONES CAPTURADORES<br>(Total > 0)",
		array("Tienda", "Caja", "Conex.HOY", "Conex.TOTAL"),
			"Select * from conexion_capturador where total>0", NULL, "ESP","");

if (isset($Queries))
	foreach ($Queries as $key => $dato) {
		Show_data2($key, $dato); echo PHP_EOL;
	}

?>