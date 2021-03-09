<?php
	$dir_oper="/home/soporteweb/tools/VELA/";
	$dir_datos="datos/";

	if (file_exists($dir_oper."refresco.hora")) {
		echo "Ultima actualizacion: ",file_get_contents($dir_oper."refresco.hora");
		echo "<button style='float:right' id='b_genera_informe'>Generar informe</button>";
	}
?>