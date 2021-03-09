<?php
$SQL_BASE="(SELECT * FROM Incidencias) as temp";

require_once("/home/soporteweb/tools/mysql.php");
foreach($_GET as $k => $d) $$k=$d;
$csv =   (empty($csv)?0:1); // get the requested page
$page =  (empty($page)?2:$page); // get the requested page
$limit = (empty($rows)?20:$rows); // get how many rows we want to have into the grid
$sidx =  (empty($sidx)?"Item_id":$sidx); // get index row - i.e. user click to sort
$sord =  (empty($sord)?'asc':$sord); // get the direction

function is_valid_callback($subject)
{
	$identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
	$reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case', 'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 'static', 'null', 'true', 'false');
	return preg_match($identifier_syntax, $subject) && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
}

mysqli_set_charset($mysqli, "utf8");

$filterResultsJSON = @json_decode($_REQUEST['filters']);
$filterArray = @get_object_vars($filterResultsJSON);

// Begin the select statement by selecting cols from tbl
$where="";
$counter = 0;
// Loop through the $filterArray until we process each 'rule' array inside
while($counter < count($filterArray['rules']))
{
	// Convert the each 'rules' object into a workable Array
	$filterRules = get_object_vars($filterArray['rules'][$counter]);

	// If this is the first pass, start with the WHERE clause
	if($counter == 0){
		$where .= ' WHERE ' . $filterRules['field'] . ' LIKE "%' . $filterRules['data'] . '%"';
	}
	// If this is the second or > pass, use AND
	else {
		$where .= ' AND ' . $filterRules['field'] . ' LIKE "%' . $filterRules['data'] . '%"';
	}
	$counter++;
}

if ($csv) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
	$sql = "SELECT ITEM, DESCRIPCION, Tipo, Tipo_PVP, BEGIN_DATE, END_DATE, PVP FROM ".$SQL_BASE." $where";
	$data = myQUERY_Tienda($mysqli_tienda, $sql,true);
	download_send_headers("listado_precios.csv");
	echo array2csv($data);
	die();
}

$result = myQUERY("SELECT COUNT(*) from ".$SQL_BASE." $where");
$count = $result[0][0];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
// echo "$page - $total_pages - $limit";
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if ($start<1) $start=1;

$sql = "SELECT ID,TITULO,FECHGRAB,FECHRESO,CODIRESO,ELEMPROD,TIPOPROBL,DIANIVEMAX,PRIORIDAD,DIANIVEACTU,ASIGNADO,ESTADO,VERSINST,DEFECTO,SERVICIO FROM ".$SQL_BASE."
	$where
	ORDER BY $sidx $sord
	limit $limit offset $start";

$data = myQUERY($sql);
	
if (empty($data)) {
	die("<h1>ERROR: No records found!!</h1>");
}

@$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$responce->sidx = $sidx;
$responce->sord = $sord;

foreach($data as $k => $d) {
	$responce->rows[]=$d;
}


$json = json_encode($responce);
# JSON if no callback
if(!isset($_GET['callback'])) 
	exit($json);
# JSONP if valid callback
if(is_valid_callback($_GET['callback']))
  	exit("{$_GET['callback']}($json)");

# Otherwise, bad request
header('status: 400 Bad Request', true, 400);
exit;

?>