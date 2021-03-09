<?php

$SQL_BASE="(select
	a.item_id as ITEM
	, c.DESCRIPTION 'DESCRIPCION'
	, (if(a.price_key_id=1,'PVP1 TARIFA',IF(a.price_key_id=2,'PVP2 SIN TARJ.','PVP3 CON TARJ.'))) as Tipo_PVP
	, IFNULL(a.begin_date,'') as F_INICIO
	, IFNULL(a.end_date,'') as F_FIN
	, a.price_amount 'PVP'
	FROM PRICE a
		JOIN PRICE_TYPE b on a.PRICE_TYPE_ID=b.PRICE_TYPE_ID
		JOIN ITEM c ON a.ITEM_ID=c.ITEM_ID
	where a.price_type_id=1
	) as temp";

$csv =   (empty($csv)?0:1); // get the requested page
$page =  (empty($page)?2:$page); // get the requested page
$limit = (empty($rows)?20:$rows); // get how many rows we want to have into the grid
$sidx =  (empty($sidx)?"Item_id":$sidx); // get index row - i.e. user click to sort
$sord =  (empty($sord)?'asc':$sord); // get the direction

$filterResultsJSON = @json_decode($_REQUEST['filters']);
$filterArray = @get_object_vars($filterResultsJSON);

// Begin the select statement by selecting cols from tbl
$where="";
$counter = 0;

// Loop through the $filterArray until we process each 'rule' array inside
while($counter < count($filterArray['rules']))
{
	$filterRules = get_object_vars($filterArray['rules'][$counter]);
	if($counter == 0){
		$where .= ' WHERE ' . $filterRules['field'] . " LIKE '" . $filterRules['data'] ."'";
	}
	else {
		$where .= ' AND ' . $filterRules['field'] . " LIKE '" . $filterRules['data'] ."'";
	}
	$counter++;
}

$result = myQUERY_Tienda($mysqli_tienda,"SELECT COUNT(*) from $SQL_BASE $where");
$count = $result[0][0];

if( $count >0 ) $total_pages = ceil($count/$limit);
else $total_pages = 0;

if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if ($start<0) $start=0;		

$sql = "SELECT ITEM, DESCRIPCION, Tipo_PVP, F_INICIO, F_FIN, PVP  FROM $SQL_BASE $where ORDER BY $sidx $sord limit $limit offset $start";

$data=myQUERY_Tienda($mysqli_tienda, $sql);

if (empty($data)) { die("<h1>ERROR: No records found!!</h1>"); }

@$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$responce->sidx = $sidx;
$responce->sord = $sord;

foreach($data as $k => $d) {
	$responce->rows[]=$d;
}
$json = json_encode($responce);

echo $json;
?>