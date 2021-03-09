<?php
function outputCSV($data) {
	$outputBuffer = fopen("php://output", 'w');
	foreach($data as $val) {
		fputcsv($outputBuffer, $val);
	}
	fclose($outputBuffer);
}


if (!empty($_GET['CSV'])) {
// 	require_once("/home/soporteweb/Resources/tools/mysql.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
	$tmpQuery=file_get_contents($_GET['QUERY']);
	$Res=myQUERY($tmpQuery);
	$filename = $_GET['CSV'];
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename={$filename}.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	outputCSV($Res);
}
?>