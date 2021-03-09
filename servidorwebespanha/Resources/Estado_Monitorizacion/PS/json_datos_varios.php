<?php
if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$DOCUMENT_ROOT="/home/soporteweb/";
	require_once($DOCUMENT_ROOT."/tools/mysql.php");
} else require_once($_SERVER['DOCUMENT_ROOT']."/tools/mysql.php");

mysqli_set_charset($mysqli, "utf8");

if (!empty($_GET["Totales"])) {
	exec('cd /home/pendserv/trabajo && sudo sh saca_datos.sh');

	$data = myQUERY("select * from tmp_Resultados_Foto");

/*	$c_zero=file_get_contents("/home/pendserv/trabajo/files_zero.dat");
	$c_non_zero=file_get_contents("/home/pendserv/trabajo/files_non_zero.dat");
	$c_resultados=file_get_contents("/home/pendserv/trabajo/files_regularizados.dat");
	if ($c_resultados>$_non_zero) $c_resultados=$c_non_zero;
	$c_resultados_vacios=file_get_contents("/home/pendserv/trabajo/files_regularizados_vacios.dat");
*/
	@$responce->records = 5;
	$responce->cols = array (
			  array ( "id"=>'Titulo', 	"label"=>'Titulo', 	"type"=>'string')
			, array ( "id"=>'Valor',	"label"=>'Valor', 	"type"=>'number')
	);
	foreach($data as $k => $d) {
		$temp = array(); $temp[] = array('v' => $d[0]); $temp[] = array('v' => $d[1]);
		$responce->rows[]=array('c' => $temp);
	}
}

if (!empty($_GET["clas_x_tipo"])) {
	$data = myQUERY("select tipo,count(*) from tmpTiendas where numerotienda in (select distinct(tienda) from tmp_regularizacion) and centro<>'SEDE' group by tipo");

	@$responce->records = count($data);
	$responce->cols = array (
			  array ( "id"=>'Titulo', 	"label"=>'Titulo', 	"type"=>'string')
			, array ( "id"=>'Valor',	"label"=>'Valor', 	"type"=>'number') );
	foreach($data as $k => $d) {
		$temp = array(); $temp[] = array('v' => $d[0]); $temp[] = array('v' => $d[1]);
		$responce->rows[]=array('c' => $temp);
	}	
}

if (!empty($_GET["clas_x_tipo_item"])) {
	$data = myQUERY("select tipo,count(*) from tmpTiendas left join tmp_regularizacion on numerotienda=tienda where centro<>'SEDE' group by tipo");

	@$responce->records = count($data);
	$responce->cols = array (
			  array ( "id"=>'Titulo', 	"label"=>'Titulo', 	"type"=>'string')
			, array ( "id"=>'Valor',	"label"=>'Valor', 	"type"=>'number') );
	foreach($data as $k => $d) {
		$temp = array(); $temp[] = array('v' => $d[0]); $temp[] = array('v' => $d[1]);
		$responce->rows[]=array('c' => $temp);
	}	
}

if (empty($responce)) {
	header('status: 400 Bad Request', true, 400);
}

$json = json_encode($responce, JSON_NUMERIC_CHECK);

exit($json);
?>