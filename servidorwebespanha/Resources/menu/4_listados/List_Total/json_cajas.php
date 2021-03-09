<?php
// header('content-type: application/json; charset=utf-8');

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$page =  (empty($_GET['page'])?2:$_GET['page']); // get the requested page
$limit = (empty($_GET['rows'])?20:$_GET['rows']); // get how many rows we want to have into the grid
$sidx =  (empty($_GET['sidx'])?"Caja":$_GET['sidx']); // get index row - i.e. user click to sort
$sord =  (empty($_GET['sord'])?'asc':$_GET['sord']); // get the direction

$qTienda = (empty($_GET['Tienda'])?'':$_GET['Tienda']);
// $sortname=(empty($_GET['sortname'])?'NumeroTienda':$_GET['sortname']);
// $sortorder=(empty($_GET['sortorder'])?'asc':$_GET['sortorder']);

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

$Tabla="Checks$PAIS_SERVER";
if (!empty($_GET['Centro']) && $_GET['Centro'] == "SEDE")
	$Tabla="ChecksXXX";

$result = myQUERY("SELECT COUNT(*) from $Tabla where tienda=$qTienda");
$count = $result[0][0];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)
$SQL="SELECT caja,Exec,MYSQL,WSD,Version,Modelo,BIOS,RAM,HDD,Temper,LAN,N_APAG from $Tabla where Tienda=$qTienda ORDER BY $sidx  $sord LIMIT $start , $limit";
// echo $SQL;
// file_put_contents("/tmp/error2.log","Parametros: ".$_GET['sidx']." / ".$SQL);
$data = myQUERY($SQL);

// file_put_contents("/tmp/error2.log",$sql.PHP_EOL,FILE_APPEND);
// file_put_contents("/tmp/error2.log","Error: ".$mysqli->errno." (".$mysqli->error.")".PHP_EOL,FILE_APPEND);

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

$json = json_encode($responce);
# JSON if no callback
// file_put_contents("/tmp/error2.log",var_export($json,true).PHP_EOL,FILE_APPEND);
if(!isset($_GET['callback'])) 
	exit($json);
# JSONP if valid callback
if(is_valid_callback($_GET['callback']))
    exit("{$_GET['callback']}($json)");

# Otherwise, bad request
header('status: 400 Bad Request', true, 400);
?>