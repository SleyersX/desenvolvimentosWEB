<?php
if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$DOCUMENT_ROOT="/home/soporteweb/";
	require_once($DOCUMENT_ROOT."/tools/mysql.php");
} else require_once($_SERVER['DOCUMENT_ROOT']."/tools/mysql.php");

mysqli_set_charset($mysqli, "utf8");

$Fecha_Foto = (empty($_GET["FechaFoto"])?"2017-01-08":$_GET["FechaFoto"]);

if (!empty($_GET["detalle"])) {
	$Tienda=$_GET["Tienda"];
	$sql = "select Tienda,date(Fecha), Arti, Ahora, FaltasSobras, Almacen, Movimientos, Despues, Peso_Variable, Diferencia from Pend_Serv where tienda=$Tienda";
	@$responce->cols=array(
		  array ( "id"=>'Tienda', 		"label"=>'Tienda', 		"type"=>'number')
		, array ( "id"=>'Fecha', 		"label"=>'Fecha', 		"type"=>'string')
		, array ( "id"=>'Item', 		"label"=>'Arti.',		"type"=>'number')
		, array ( "id"=>'Ahora',  		"label"=>'PS.Actu.', "type"=>'number')
		, array ( "id"=>'FaltasSobras', "label"=>'Faltas-Sobras', "type"=>'number')
		, array ( "id"=>'Almacen',  	"label"=>'PS.Almacen', "type"=>'number')
		, array ( "id"=>'Movimientos',"label"=>'Mov.Tienda', "type"=>'number')
		, array ( "id"=>'Despues', 	"label"=>'Despues', 		"type"=>'number')
		, array ( "id"=>'Peso_Variable',"label"=>'PesoVar', "type"=>'number')
		, array ( "id"=>'Diferencia', "label"=>'Difer.', 	"type"=>'number')
	);

}
else {
	$sql = "
	SELECT a.Tienda, DATE(a.Fecha), count(a.Tienda)
	FROM Pend_Serv a LEFT JOIN Inic_Pend_Serv b ON a.Tienda=b.Tienda
	WHERE DATE(b.Fecha) < '".$Fecha_Foto."'
	GROUP by a.Tienda, a.Fecha
	ORDER by 3 desc";
	@$responce->cols=array(
	  array ( "id"=>'Tienda', 	"label"=>'Tienda', 		"type"=>'number')
	, array ( "id"=>'Fecha', 	"label"=>'Fecha', 		"type"=>'string')
	, array ( "id"=>'Total', 	"label"=>'Total Items',	"type"=>'number')
	);
}

/*
+--------+---------------------+--------+-------+--------------+---------+-------------+---------+---------------+------------+
| Tienda | Fecha               | Arti   | Ahora | FaltasSobras | Almacen | Movimientos | Despues | Peso_Variable | Diferencia |
+--------+---------------------+--------+-------+--------------+---------+-------------+---------+---------------+------------+
|  17507 | 2017-01-05 00:00:00 | 111195 | 7.000 |        0.000 |   0.000 |       0.000 |   0.000 |             0 |      7.000 |
+--------+---------------------+--------+-------+--------------+---------+-------------+---------+---------------+------------+
*/
$data = myQUERY($sql);

if (empty($data)) {
	header('status: 400 Bad Request', true, 400);
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