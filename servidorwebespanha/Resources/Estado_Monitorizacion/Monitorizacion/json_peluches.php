<?php
print_r($_SERVER);

if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$DOCUMENT_ROOT="/home/soporteweb/";
	require_once($DOCUMENT_ROOT."/tools/mysql.php");
} else require_once($_SERVER['DOCUMENT_ROOT']."/tools/mysql.php");

mysqli_set_charset($mysqli, "utf8");

$data = myQUERY("	select
		  pp.Tienda as Tienda
		, SUM(pp.Entregados) as suma_entregados
		, SUM(pp.No_Entregados)  as suma_no_entregados
		, ROUND(SUM(pp.Entregados)/(SUM(pp.Entregados)+SUM(pp.No_Entregados))*100,1)
		, ROUND(SUM(pp.No_Entregados)/(SUM(pp.Entregados)+SUM(pp.No_Entregados))*100,1)
		, IFNULL((select SUM(pe.cerd+pe.ovej+pe.vaca+pe.burr+pe.caba) from PeluchesEntregados pe where pe.tienda=pp.tienda and pe.fecha=pp.fecha),0) as suma_peluches
		, IFNULL((select SUM(peg.cerd+peg.ovej+peg.vaca+peg.burr+peg.caba) from PeluchesEntregadosGratis peg where peg.tienda=pp.tienda and peg.fecha=pp.fecha),0) as suma_peluches_gratis
		, date(pp.Fecha)
	from PuntosPeluche pp
	group by pp.tienda,pp.Fecha
	having
		   (suma_entregados+suma_no_entregados)>0 or suma_peluches>0 or suma_peluches_gratis>0
");

if (empty($data)) {
	die("<h1>ERROR: No records found!!</h1>");
}

foreach($data as $k => $d) { $responce->rows[]=$d; }

// file_put_contents("/tmp/error1.log",$responce,FILE_APPEND);

$json = json_encode($responce, JSON_NUMERIC_CHECK);
// file_put_contents("/tmp/error1.log",var_export($json,true).PHP_EOL,FILE_APPEND);
# JSON if no callback
if(!isset($_GET['callback'])) 
	exit($json);
# JSONP if valid callback
if(is_valid_callback($_GET['callback']))
	exit("{$_GET['callback']}($json)");

# Otherwise, bad request
header('status: 400 Bad Request', true, 400);
// file_put_contents("/tmp/error1.log","ERROR: 400 bad request",FILE_APPEND);
?>