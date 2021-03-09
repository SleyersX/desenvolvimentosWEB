<?php
header('content-type: application/json; charset=utf-8');
// foreach($_REQUEST as $k => $d) { @$Echo.="[R:$k]:$d\n"; }
// foreach($_POST as $k => $d) { @$Echo.="[P:$k]:$d\n"; }
// file_put_contents("/tmp/error1.log",@$Echo);
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$page =  (empty($_GET['page'])?2:$_GET['page']); // get the requested page
$limit = (empty($_GET['rows'])?20:$_GET['rows']); // get how many rows we want to have into the grid
$sidx =  (empty($_GET['sidx'])?"Numerotienda":$_GET['sidx']); // get index row - i.e. user click to sort
$sord =  (empty($_GET['sord'])?'asc':$_GET['sord']); // get the direction
$filters = (empty($_GET['filters'])?'':$_GET['filters']); // get the direction

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
		$where .= ' WHERE ' . $filterRules['field'] . ' LIKE "%' . $filterRules['data'] . '%"';
	}
	// If this is the second or > pass, use AND
	else {
	$where .= ' AND ' . $filterRules['field'] . ' LIKE "%' . $filterRules['data'] . '%"';
	}
	$counter++;
}

$result = myQUERY("SELECT COUNT(*) from HistoricoESP $where");
$count = $result[0][0];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)


$sql = "select * from HistoricoESP $where ORDER BY $sidx $sord LIMIT $start,$limit";

// echo $SQL;
// file_put_contents("/tmp/error1.log","Parametros: ".$_GET['sidx'].$_GET['sord']." / ".$SQL);
$data = myQUERY($sql);

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

foreach($data as $k => $d) {
    $responce->rows[]=$d;
}

$json = json_encode($responce, JSON_NUMERIC_CHECK);
# JSON if no callback
if(!isset($_GET['callback'])) 
	exit($json);
# JSONP if valid callback
if(is_valid_callback($_GET['callback']))
    exit("{$_GET['callback']}($json)");

# Otherwise, bad request
header('status: 400 Bad Request', true, 400);
?>