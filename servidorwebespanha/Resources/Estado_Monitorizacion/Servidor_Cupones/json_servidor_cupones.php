<?php

if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$DOCUMENT_ROOT="/home/soporteweb/";
	require_once($DOCUMENT_ROOT."/tools/mysql.php");
} else require_once($_SERVER['DOCUMENT_ROOT']."/tools/mysql.php");

mysqli_set_charset($mysqli, "utf8");

//$cmd_sql = "mysql soporteremotoweb -N -e \"select sc1.ID 'Tramo' ,sc1.Oper 'SC1_Oper', sc1.Web 'SC1_Web' ,sc2.Oper 'SC2_Oper', sc2.Web 'SC2_Web' ,sc1.Oper+sc2.Oper 'SC1_Total', sc1.Web+sc2.Web 'SC2_Total' from serv_cupo1 sc1 inner join serv_cupo2 sc2 on sc1.id = sc2.id where DATE(sc1.ID) = DATE(NOW()) and time(sc1.ID) >= '00:10:00' and time(sc1.ID) <= '23:50:00'\"";
//$cmd = 'ssh soporte$pais "$cmp"';

//shell_exec($cmd);

$sql = "(select *,'ESP' from serv_cupo_hoy_ESP)
	UNION (select *,'POR' from serv_cupo_hoy_POR)
	UNION (select *,'ARG' from serv_cupo_hoy_ARG)
	UNION (select *,'BRA' from serv_cupo_hoy_BRA)
	";

$data = myQUERY($sql);

// file_put_contents("/tmp/error1.log",$sql.PHP_EOL,FILE_APPEND);
// file_put_contents("/tmp/error1.log","Error: ".$mysqli->errno." (".$mysqli->error.")".PHP_EOL,FILE_APPEND);

if (empty($data)) {
	header('status: 400 Bad Request', true, 400);
}

@$responce->records = count($data);

$responce->cols=array(
	array ( "id"=>'Tramo', "label"=>'Tramo', "type"=>'string'),
	array ( "id"=>'Oper1', "label"=>'SC1_Oper', "type"=>'number'),
	array ( "id"=>'Web1',  "label"=>'SC1_Web', "type"=>'number'),
	array ( "id"=>'Oper2', "label"=>'SC2_Oper', "type"=>'number'),
	array ( "id"=>'Web2',  "label"=>'SC2_Web', "type"=>'number'),
	array ( "id"=>'Total1', "label"=>'SC1_Total', "type"=>'number'),
	array ( "id"=>'Total2', "label"=>'SC2_Total', "type"=>'number'),
	array ( "id"=>'Pais', "label"=>'Pais', "type"=>'string')
);

foreach($data as $k => $d) {
	$temp = array();
	$temp[] = array('v' => $d[0]);
	$temp[] = array('v' => (int) $d[1]);
	$temp[] = array('v' => (int) $d[2]);
	$temp[] = array('v' => (int) $d[3]);
	$temp[] = array('v' => (int) $d[4]);
	$temp[] = array('v' => (int) $d[5]);
	$temp[] = array('v' => (int) $d[6]);
	$temp[] = array('v' => $d[7]);
	$responce->rows[]=array('c' => $temp);
}

$json = json_encode($responce, JSON_NUMERIC_CHECK);

exit($json);

?>