<?php

$array_sondas=array(
	"check_pendientes_servir_altas" => array(
		"fichero" => $dir_oper.$dir_datos."/pendientes_servir_altas.dat",
		"texto_titulo" => "PENDIENTES SERV. ALTAS",
		"ayuda" => "Pendientes de servir muy altas (desbordamiento > 5,3)",
		"src" => $DIR_VELA."/procesos_etl.php",
		"detalles" => "#",
		"refresco" => 1)
);

?>