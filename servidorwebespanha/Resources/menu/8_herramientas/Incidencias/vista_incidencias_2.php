<?php
$SQL_BASE="(SELECT * FROM Incidencias) as temp";

if (!empty($_GET["opcion"])) {
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

//	$sql = "select Tienda, Fecha,Capturador, DatoAdic from Capturadores $where ORDER BY $sidx $sord LIMIT $start,$limit";
	$sql = "SELECT ID,TITULO,FECHGRAB,FECHRESO,CODIRESO,ELEMPROD,TIPOPROBL,DIANIVEMAX,PRIORIDAD,DIANIVEACTU,ASIGNADO,ESTADO,VERSINST,DEFECTO,SERVICIO FROM ".$SQL_BASE."
		$where
		ORDER BY $sidx $sord
		limit $limit offset $start";

//	file_put_contents("/tmp/error1.log","Parametros: ".$sql);
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
	exit;
}

$No_Carga_ssh2=true;
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Resources/styles_js/head_1.php");
?>

<style>
	#jqGridPager * { color:white; }
	.Tienda_Off { background:lightgray; color:auto; }
	#ayuda { cursor:pointer;}
	#ayuda:hover { font-style: italic; }
</style>

<div>
	<div class="ui-jqgrid " id="gbox_jqGrid" dir="ltr"><table id="jqGrid" class="F_SIZE_12"></table><div id="jqGridPager"></div></div>
</div>

<script>

$.jgrid.defaults.responsive = true;
var url_local="/Resources/Estado_Monitorizacion/Incidencias/<?php echo basename(__FILE__); ?>";
//var url=url_local+"?opcion_ventas=get_precios_json&tienda="+tienda+"&caja="+caja;
	
$(document).ready(function () {
		$("#jqGrid").jqGrid({
			caption: '- LISTADO DE INCIDENCIAS -',
			url: url_local+"?opcion=listado_total",
			mtype: "GET",
			datatype: "json",
			colModel:
			[
				{ index: 'ID', 			name: 'ID',     		label:'Inc.ID', key: true, width: 30 },
				{ index: 'TITULO', 		name: 'TITULO', 		label:'Titulo', width: 200 },
				{ index: 'FECHGRAB', 	name: 'FECHGRAB',		label:'F.Grabacion',  width: 50 },
				{ index: 'FECHRESO', 	name: 'FECHRESO',    label:'F.Resolucion', width: 50 },
				{ index: 'CODIRESO', 	name: 'CODIRESO',  	label:'Cod. Resolucion', width: 80 },
				{ index: 'ELEMPROD', 	name: 'ELEMPROD',    label:'Elem.Prod.', width: 50 },
				{ index: 'TIPOPROBL', 	name: 'TIPOPROBL', 	label:'Tipo Problema', width: 80 },
				{ index: 'DIANIVEMAX', 	name: 'DIANIVEMAX', 	label:'Max.Nivel', width: 20 },				
				{ index: 'PRIORIDAD', 	name: 'PRIORIDAD', 	label:'Prioridad', width: 20 },
				{ index: 'DIANIVEACTU',	name: 'DIANIVEACTU',	label:'Niv.Actual', width: 20 },
				{ index: 'ASIGNADO', 	name: 'ADIGNADO', 	label:'Asignado a', width: 50 },
				{ index: 'ESTADO', 		name: 'ESTADO',	 	label:'Estado', width: 50 },
				{ index: 'VERSINST', 	name: 'ESTADO', 		label:'Vers.Inst.', width: 50 },
				{ index: 'DEFECTO', 		name: 'DEFECTO', 		label:'Defecto', width: 50 },
				{ index: 'SERVICIO', 	name: 'SERVICIO', 	label:'Servicio', width: 50 }
			],
			sortname:"ID",
			gridview: true,
			viewrecords: true,
			page: 1,
			height:650,
			autowidth:true,
//			autowidth: true,
			rowNum: 50,
			scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
			pager: "#jqGridPager",
			search: true,
			refresh: true, sortable: true, shrinkToFit: true,
			loadui: 'enable'
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