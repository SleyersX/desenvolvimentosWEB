<?php
if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$DOCUMENT_ROOT="/home/soporteweb/";
	require_once($DOCUMENT_ROOT."/tools/mysql.php");
} else require_once($_SERVER['DOCUMENT_ROOT']."/tools/mysql.php");

mysqli_set_charset($mysqli, "utf8");

$sql = "select a.tienda,(select count(*) from tmp_foto b where b.tienda=a.tienda) 'Foto', (select count(*) from tmp_regularizacion c where c.tienda=a.tienda) 'Regularizados', (select comunicacion from tmp_foto d where d.tienda=a.tienda limit 1) 'Comunicado' from tmp_foto a group by tienda";
	@$responce->cols=array(
		  array ( "id"=>'Tienda', 		"label"=>'Tienda', 		 "type"=>'number')
		, array ( "id"=>'Item_Foto',	"label"=>'Arti. en Foto',"type"=>'number')
		, array ( "id"=>'Item_Regu',	"label"=>'Arti. Regul.', "type"=>'number')
		, array ( "id"=>'Comunica',  	"label"=>'Comunica', 	 "type"=>'string')
		, array ( "id"=>'Porcentaje', "label"=>'Porcentaje', 	 "type"=>'number')
);

$data = myQUERY($sql);

if (empty($data)) {
	header('status: 400 Bad Request', true, 400);
}

$responce->records = count($data);

foreach($data as $k => $d) {
	$temp = array();
	$temp[] = array('v' => $d[0]);
	$temp[] = array('v' => $d[1]);
	$temp[] = array('v' => $d[2]);
	$temp[] = array('v' => $d[3]);
	$temp[] = array('v' => ($d[2]/$d[1]));
	$responce->rows[]=array('c' => $temp);
}

$json = json_encode($responce, JSON_NUMERIC_CHECK);

exit($json);

?>