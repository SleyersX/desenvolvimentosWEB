<?php

$SQL_BASE="( SELECT
		a.item_id as ITEM
		, c.DESCRIPTION 'DESCRIPCION'
		, b.description 'Tipo'
		, (if(a.price_key_id=1,'PVP1 TARIFA',IF(a.price_key_id=2,'PVP2 SIN TARJ.','PVP3 CON TARJ.'))) as Tipo_PVP
		, IFNULL(a.begin_date,'') 'BEGIN_DATE'
		, IFNULL(a.end_date,'') 'END_DATE'
		, a.price_amount 'PVP'
				FROM PRICE a
					JOIN PRICE_TYPE b on a.PRICE_TYPE_ID=b.PRICE_TYPE_ID
					JOIN ITEM c ON a.ITEM_ID=c.ITEM_ID
		) as temp";


if (!empty($_GET["opcion_ventas"])) {
	header('content-type: application/json; charset=utf-8');
	require_once("/home/soporteweb/tools/mysql.php");
	foreach($_GET as $k => $d) $$k=$d;
	$csv =   (empty($csv)?0:1); // get the requested page
	$page =  (empty($page)?2:$page); // get the requested page
	$limit = (empty($rows)?20:$rows); // get how many rows we want to have into the grid
	$sidx =  (empty($sidx)?"Item_id":$sidx); // get index row - i.e. user click to sort
	$sord =  (empty($sord)?'asc':$sord); // get the direction

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

	$mysqli_tienda= open_mysql_tienda($tienda,$caja);

//	$mysqli_tienda = new mysqli($IP_Tienda, "root", "", "n2a");

	// Gets the 'filters' object from JSON
	$filterResultsJSON = @json_decode($_REQUEST['filters']);

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

	if ($csv) {
		require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
		$sql = "SELECT ITEM, DESCRIPCION, Tipo, Tipo_PVP, BEGIN_DATE, END_DATE, PVP FROM ".$SQL_BASE." $where";
		$data = myQUERY_Tienda($mysqli_tienda, $sql,true);
		download_send_headers("listado_precios.csv");
		echo array2csv($data);
		die();
	}

	$result = myQUERY_Tienda($mysqli_tienda,"SELECT COUNT(*) from ".$SQL_BASE." $where");
	$count = $result[0][0];

	if( $count >0 ) {
		$total_pages = ceil($count/$limit);
	} else {
		$total_pages = 0;
	}
	if ($page > $total_pages) $page=$total_pages;
	// echo "$page - $total_pages - $limit";
	$start = $limit*$page - $limit; // do not put $limit*($page - 1)

	if ($start<0) $start=0;

//	$sql = "select Tienda, Fecha,Capturador, DatoAdic from Capturadores $where ORDER BY $sidx $sord LIMIT $start,$limit";
	$sql = "SELECT ITEM, DESCRIPCION, Tipo, Tipo_PVP, BEGIN_DATE, END_DATE, PVP FROM ".$SQL_BASE."
		$where
		ORDER BY $sidx $sord
		limit $limit offset $start";

//	file_put_contents("/tmp/error1.log","Parametros: ".$sql);
	// echo "$sql";
	$data = myQUERY_Tienda($mysqli_tienda,$sql);
	close_mysql_tienda($mysqli_tienda);
	
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
}

$l_tipos=":[All];TARIFA:TARIFA;EXCEPCION:EXCEPCION";
$l_tipos_pvp=":[All];PVP1 TARIFA:PVP1 TARIFA;PVP2 SIN TARJ:PVP2 SIN TARJ;PVP2 CON TARJ:PVP2 CON TARJ";

?>
<style>
	#jqGridPager * { color:white; }
	.Tienda_Off { background:lightgray; color:auto; }
	#ayuda { cursor:pointer;}
	#ayuda:hover { font-style: italic; }
</style>

<div>
	<div class="ui-jqgrid " id="gbox_jqGrid" dir="ltr">
		<table id="jqGrid" class="F_SIZE_12"></table>
		<div id="jqGridPager"></div>
	</div>
</div>

<script>
	$.jgrid.defaults.responsive = true;
	var url_local="Listados_PHP/<?php echo basename(__FILE__); ?>";
	var IP_Tienda="<?php echo $con_tda->GetIP(); ?>";
	var tienda="<?php echo $con_tda->tienda; ?>";
	var caja="<?php echo $con_tda->caja; ?>";
	Desbloqueo();
	var url=url_local+"?opcion_ventas=get_precios_json&tienda="+tienda+"&caja="+caja;

$(document).ready(function () {
		$("#jqGrid").jqGrid({
			caption: '- LISTADO TOTAL DE PRECIOS -',
			url: url,
			mtype: "GET",
			datatype: "json",
			colModel:
			[
				{ index: 'ITEM', 			name: 'ITEM',     label:'Articulo', key: true, width: 50 },
				{ index: 'DESCRIPCION', name: 'DESCRIPCION', label:'Descripcion de articulo', key: true, width: 100 },
				{ index: 'TIPO', 			name: 'TIPO',          label:'Tipo', 		 width: 50, stype: 'select', searchoptions: { value: '<?php echo $l_tipos;?>' } },
				{ index: 'TIPO_PVP', 	name: 'TIPO_PVP',      label:'Tipo PVP', width: 50, stype: 'select', searchoptions: { value: '<?php echo $l_tipos_pvp;?>' } },
				{ index: 'BEGIN_DATE', 	name: 'BEGIN_DATE',  label:'F. Inicial', width: 50 },
				{ index: 'END_DATE', 	name: 'END_DATE',    label:'F. Fin', width: 50 },
				{ index: 'PVP', 			name: 'PVP', label:'PVP', width: 50 }
			],
			sortname:"ITEM",
			gridview: true,
			viewrecords: true, emptyrecords: "No hay datos",
			page: 1,
			height:650,
			width:1000,
//			autowidth: true,
			rowNum: 50,
			scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
			pager: "#jqGridPager",
			search: true,
			refresh: true, sortable: true, shrinkToFit: true,
			loadui: 'enable',

		});

		$('#jqGrid').jqGrid('filterToolbar',{
                stringResult: true
                // instuct the grid toolbar to show the search options
//                 searchOperators: true
            });

		$('#jqGrid').navGrid("#jqGridPager", {
			search: true, edit:false, add:false, del:false, refresh: true
		})
		.navSeparatorAdd("#jqGridPager",{})
		// add custom button to export the data to excel
		.navButtonAdd('#jqGridPager',{
			caption:"Excel",
			title:"Permite salvar el listado total en formato CSV para ser cargado por hojas de calculo",
			buttonicon:"ui-icon-document",
			onClickButton : function () { 
				window.open(url+"&csv=1", '');
			}
		})
		.navSeparatorAdd("#jqGridPager",{});
});

</script>