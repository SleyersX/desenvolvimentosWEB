<?php
if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$DOCUMENT_ROOT="/home/soporteweb/";
	require_once($DOCUMENT_ROOT."/tools/mysql.php");
} else require_once($_SERVER['DOCUMENT_ROOT']."/tools/mysql.php");

mysqli_set_charset($mysqli, "utf8");

switch ($_GET['info']) {
	case "info_prepara_vela":
		$query="select a.tienda, a.caja, a.es_vela, a.prepara_paso, a.fecha_paso, b.centro, b.tipo, b.subtipo, c.version from Paso_Vela a join tmpTiendas b on a.tienda=b.numerotienda join ChecksESP c on a.tienda=c.tienda and c.caja=a.caja and pais<>'XXX' where b.centro<>'SEDE'";
		$data = myQUERY($query);
		@$responce->cols = array (
			  array ( "id"=>'Tienda', 	"label"=>'Tienda', 	"type"=>'number')
			, array ( "id"=>'Caja',		"label"=>'Caja', 		"type"=>'number')
			, array ( "id"=>'VELA',		"label"=>'VELA', 		"type"=>'number')
			, array ( "id"=>'PP',		"label"=>'P_Paso', 	"type"=>'number')
			, array ( "id"=>'Fecha_Paso',	"label"=>'Fecha_Paso', 		"type"=>'string')
			, array ( "id"=>'Centro',	"label"=>'Centro', 		"type"=>'string')	
			, array ( "id"=>'Tipo',	"label"=>'Tipo', 		"type"=>'string')
			, array ( "id"=>'Subtipo',	"label"=>'Subtipo', 		"type"=>'string')
			, array ( "id"=>'Version',	"label"=>'Version', 		"type"=>'string')
		);

		break;
}

$responce->records = count($data);
foreach($data as $k => $d) {
	$temp = array();
	foreach($d as $d1) $temp[] = array('v' => $d1);
	$responce->rows[]=array('c' => $temp);
}

$json = json_encode($responce, JSON_NUMERIC_CHECK);

exit($json);
?>