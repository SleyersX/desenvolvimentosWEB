<?php
// header('content-type: application/json; charset=utf-8');
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$csv =   (empty($_GET['csv'])?0:1); // get the requested page
$page =  (empty($_GET['page'])?2:$_GET['page']); // get the requested page
$limit = (empty($_GET['rows'])?20:$_GET['rows']); // get how many rows we want to have into the grid
$sidx =  (empty($_GET['sidx'])?"Numerotienda":$_GET['sidx']); // get index row - i.e. user click to sort
$sord =  (empty($_GET['sord'])?'asc':$_GET['sord']); // get the direction
if ($PAIS_SERVER=="ESP")
	$filters = (empty($_GET['filters'])?'':$_GET['filters']); // get the direction
else
	$filters = (empty($_GET['filters'])?'':str_replace("\\","",$_GET['filters'])); // get the direction

function is_valid_callback($subject)
{
    $identifier_syntax
      = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

    $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
      'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 
      'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 
      'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 
      'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 
      'private', 'public', 'yield', 'interface', 'package', 'protected', 
      'static', 'null', 'true', 'false');

    return preg_match($identifier_syntax, $subject)
        && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
}

mysqli_set_charset($mysqli, "utf8");

$Base="numerotienda, conexion, version, VELA, centro, tipo, subtipo, direccion, poblacion, CodiPost, provincia, telefono, IP, NTPVS, tipoEtiquetadora,frescos, Balanzas, PCs, Impresoras FROM tmpTiendas_Total";

if ($csv) {
	$data = myQUERY("select $Base");
	// CABECERAS
	array_unshift($data,array("numerotienda", "conexion", "version", "VELA", "centro", "tipo", "subtipo", "direccion", "poblacion", "C.P.", "provincia", "telefono", "IP", "NTPVs", "tipoEtiquetadora", "frescos", "Balanzas", "PCs", "Impresoras"));
	// disable caching
	download_send_headers("listado_total.csv");
	echo array2csv($data);
	die();
}

// Gets the 'filters' object from JSON
$filterResultsJSON = @json_decode($filters);

// Converts the 'filters' object into a workable Array
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
		$where .= ' WHERE ' . $filterRules['field'] . ' LIKE \'%' . $filterRules['data'] . '%\'';
	}
	// If this is the second or > pass, use AND
	else {
	$where .= ' AND ' . $filterRules['field'] . ' LIKE \'%' . $filterRules['data'] . '%\'';
	}
	$counter++;
}

$result = myQUERY("SELECT COUNT(*) from tmpTiendas_Total $where");
$count = $result[0][0];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

$sql = "select $Base $where ORDER BY  $sidx $sord LIMIT $start,$limit";
// select a.numerotienda,a.ip,SUM(case when b.Elemento like 'b%' and a.numerotienda=b.tienda and b.conexion=1 then 1 else 0 end) as Balanzas, SUM(case when b.Elemento like 'i%' and a.numerotienda=b.tienda and b.conexion=1 then 1 else 0 end) as PCs, SUM(case when b.Elemento like 'pc%' and a.numerotienda=b.tienda and b.conexion=1 then 1 else 0 end) as Impresoras from tmpTiendas a left join Elementos b on a.numerotienda=b.Tienda group by a.numerotienda
$data = myQUERY($sql);

// file_put_contents("/tmp/error1.log",$sql.PHP_EOL,FILE_APPEND);
// file_put_contents("/tmp/error1.log","Error: ".$mysqli->errno." (".$mysqli->error.")".PHP_EOL,FILE_APPEND);

if (empty($data)) {
	die("<h1>ERROR: No records found!!</h1>");
}

@$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
// $responce->sortorder = $sortorder;
// $responce->sortname = $sortname;
$responce->sidx = $sidx;
$responce->sord = $sord;

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