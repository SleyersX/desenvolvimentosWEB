<?php
header('content-type: application/json; charset=utf-8');

if (empty($_SERVER['DOCUMENT_ROOT']))
	require_once("/home/soporteweb/tools/mysql.php");
else
	require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$csv =   (empty($_GET['csv'])?0:1); // get the requested page
$page =  (empty($_GET['page'])?2:$_GET['page']); // get the requested page
$limit = (empty($_GET['rows'])?20:$_GET['rows']); // get how many rows we want to have into the grid
$sidx =  (empty($_GET['sidx'])?"Tienda":$_GET['sidx']); // get index row - i.e. user click to sort
$sord =  (empty($_GET['sord'])?'asc':$_GET['sord']); // get the direction

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

if ($Pais == "ARG" && SoyYo())
	$SQL_BASE="(select a.*, b.PV from tmpHardware a JOIN Info_PV b ON a.tienda=b.tienda AND a.caja=b.caja where centro <> 'SEDE') as tmp";
else 
	$SQL_BASE="(select * from tmpHardware where centro <> 'SEDE' ) as tmp";

if ($csv) {
	$cabeceras=array();
	if ($Pais == "ARG" && SoyYo())
			$tmp = myQUERY("select column_name from information_schema.columns WHERE  table_name in ('tmpHardware') union (select 'PV');");
		else
			$tmp = myQUERY("select column_name from information_schema.columns WHERE  table_name='tmpHardware'; ");
	foreach($tmp as $d) $cabeceras[0][]=$d[0];
	$sql = "select * from $SQL_BASE";
	$tmp = myQUERY($sql);
	$data=array_merge($cabeceras,$tmp);
	download_send_headers("listado_hw.csv");
	echo array2csv($data);
	die();
}

$where="";
$str=@$_REQUEST['filters'];
if (!empty($str)) {
        $str=str_replace('\\', '', $str);
        // Gets the 'filters' object from JSON
        $filterResultsJSON = json_decode($str);

        // Converts the 'filters' object into a workable Array
        $filterArray = get_object_vars($filterResultsJSON);

        // Begin the select statement by selecting cols from tbl
        $counter = 0;
        // Loop through the $filterArray until we process each 'rule' array inside
        while($counter < count($filterArray['rules']))
        {
                // Convert the each 'rules' object into a workable Array
                $filterRules = get_object_vars($filterArray['rules'][$counter]);
                // If this is the first pass, start with the WHERE clause
                if($counter == 0){
                        $where .= ' WHERE ' . $filterRules['field'] . ' = "' . $filterRules['data'] . '"';
                }
                // If this is the second or > pass, use AND
                else {
                $where .= ' AND ' . $filterRules['field'] . ' = "' . $filterRules['data'] . '"';
                }
                $counter++;
        }
}

$result = myQUERY("SELECT COUNT(*) from $SQL_BASE $where");
$count = $result[0][0];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
// echo "$page - $total_pages - $limit";
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

$sql = "select * from $SQL_BASE $where ORDER BY $sidx $sord LIMIT $start,$limit";
file_put_contents("/tmp/error1.log","Parametros: ".$sql);
// echo "$sql";
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
?>