<?php
$options_comunes="hiddengrid:true, mtype: 'GET', datatype: 'json', gridview: true, viewrecords: true, page: 1, height:500, width:2000, autowidth:true, rowNum: 22,	scroll: 1, search: false, refresh: true, sortable: true, shrinkToFit: false ";
?>

<style type="text/css" ="">
	<?php require_once("library/css_comun.css"); ?>
	.ui-jqgrid .ui-jqgrid-titlebar {
			height: 14px;
			cursor: pointer;
	}
</style>

<body>
	<div id="Info">
		<table>
			<tr>	
				<td style="vertical-align: top;">
					<div style="width:560px;"><div class="ui-jqgrid " id="gbox_jqGrid_articulos" dir="ltr"><table id="jqGrid_articulos" class="F_SIZE_10"></table><div id="jqGridPager_articulos"></div></div></div>
					<div style="width:560px;"><div class="ui-jqgrid " id="gbox_jqGrid_EAN" dir="ltr"><table id="jqGrid_EAN" class="F_SIZE_10"></table><div id="jqGridPager_EAN"></div></div></div>
				</td>
				<td style="vertical-align: top;">
					<table>
					<tr>
						<td style="vertical-align: top;">
							<div id="info_adicional" style="float:left; border:1px solid black; border-radius:3px; padding:1em; background-color:azure">
								<span style="background-color:lightgreen; font-size:12px; font-weight:bold;">&#9664 Haga click para ver sus datos.</span>
							</div>
						<td>
						<td style="vertical-align: top;">
							<div id="info_adicional_apt2" style="display:none; border:1px solid black; border-radius:3px; padding:1em; background-color:azure"></div>
							<div id="info_adicional_ean" style="display:none; border:1px solid black; border-radius:3px; padding:1em; background-color:azure"></div>
						<td>
					<tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</body>

<script>
	var url_local='<?php echo "Listados_PHP/".basename(__FILE__); ?>';
	var IP_Tienda="<?php echo $_GET['IP_Tienda']; ?>";
	var Es_VELA="<?php echo '0'; ?>";
	var Titulo="";

	function Carga_Contenido(target, url) {
		target.show();
		target.html("<img src='/img/Loading-data.gif'/>");
		target.load(url);
	}	
	
	var last_id=0;
// ---------------------------------------------------------------------------------------------------------------------------------------------------
	var jqGridtmp=$("#jqGrid_articulos");
	$("#jqGrid_articulos").jqGrid({
		caption: "LISTADO GENERAL DE ARTICULOS, APT2 Y EANES",
		url: "./Listados_PHP/ajax_listados/listado_articulos.php?opcion_arti=get_list_arti&IP_Tienda="+IP_Tienda,
		colModel:
		[
			{ index: 'ITEM_ID', 		name: 'ITEM_ID',    	label:'ITEM', key: true, width: 100 },
			{ index: 'DESCRIPTION', name: 'DESCRIPTION', label:'Descripcion', width: 280 },
		],
		sortname:"ITEM_ID",
		<?php echo $options_comunes; ?>,		
		onSelectRow: function(id)
		{
			Carga_Contenido($('#info_adicional'), "./Listados_PHP/ajax_listados/listado_articulos.php?opcion_arti=get_info&IP_Tienda="+IP_Tienda+"&item_id="+id);
			Carga_Contenido($('#info_adicional_apt2'), "./Listados_PHP/ajax_listados/apt2_articulo.php?opcion_arti=get_info&IP_Tienda="+IP_Tienda+"&item_id="+id);	
			Carga_Contenido($('#info_adicional_ean'), "./Listados_PHP/ajax_listados/listado_articulo_EAN.php?opcion_arti=get_info&IP_Tienda="+IP_Tienda+"&item_id="+id);
		},
	});
	jqGridtmp.jqGrid('filterToolbar',{ stringResult: true });
// ---------------------------------------------------------------------------------------------------------------------------------------------------

// ---------------------------------------------------------------------------------------------------------------------------------------------------
	$(".ui-jqgrid-titlebar").click(function() {
		$(".ui-jqgrid-titlebar-close", this).click();
	});

</script>