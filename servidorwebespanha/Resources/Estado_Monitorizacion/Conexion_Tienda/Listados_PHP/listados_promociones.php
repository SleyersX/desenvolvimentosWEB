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
					<div style="width:560px;"><div class="ui-jqgrid " id="gbox_jqGrid_ofertas" dir="ltr"><table id="jqGrid_ofertas" class="F_SIZE_10"></table><div id="jqGridPager_ofertas"></div></div></div>
					<div style="width:560px;"><div class="ui-jqgrid " id="gbox_jqGrid_cupones" dir="ltr"><table id="jqGrid_cupones" class="F_SIZE_10"></table><div id="jqGridPager_cupones"></div></div></div>
					<div style="width:560px;"><div class="ui-jqgrid " id="gbox_jqGrid_cupoclie" dir="ltr"><table id="jqGrid_cupoclie" class="F_SIZE_10"></table><div id="jqGridPager_cupoclie"></div></div></div>
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
						<td>
					<tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</body>

<script>
	var IP_Tienda="<?php echo $_GET['IP_Tienda']; ?>";
	var Es_VELA="<?php echo '0'; ?>";

// ---------------------------------------------------------------------------------------------------------------------------------------------------
	var jqGridtmp=$("#jqGrid_ofertas");
	jqGridtmp.jqGrid({
		caption: "LISTADO GENERAL DE OFERTAS",
		url: "./Listados_PHP/ajax_listados/listado_ofertas.php?opcion_ofer=get_list_ofer&IP_Tienda="+IP_Tienda,
		colModel:
		[
			{ index: 'Oferta', name: 'Oferta', label:'C.Oferta', key: true, width: 80 },
			{ index: 'Descripcion', name: 'Descripcion', label:'Descripcion', width: 200 },
			{ index: 'F_Inicio', name: 'F_Inicio', label:'Fecha Inicio', width: 100 },
			{ index: 'F_Fin', name: 'F_Fin', label:'Fecha Fin', width: 100 },
			{ index: 'Activo', name: 'Activo', label:'Activo', width: 70 },
		],
		sortname:"Oferta",
		<?php echo $options_comunes; ?>,
		onSelectRow: function(id,response_data){
     	   $('#info_adicional').html("<img src='/img/Loading-data.gif'/>").load("./Listados_PHP/ajax_listados/listado_ofertas.php?opcion_ofer=get_info&IP_Tienda="+IP_Tienda+"&Oferta="+id);
		},
	});
	jqGridtmp.jqGrid('filterToolbar',{ stringResult: true });

// ---------------------------------------------------------------------------------------------------------------------------------------------------
	var jqGridtmp=$("#jqGrid_cupones");
	jqGridtmp.jqGrid({
		caption: "LISTADO GENERAL DE CUPONES",
		url: "./Listados_PHP/ajax_listados/listado_cupones.php?opcion_cupo=get_list_cupo&IP_Tienda="+IP_Tienda,
		colModel:
		[
			{ index: 'Cupon', 	name: 'Cupon',    label:'CUPON', key: true, width: 60 },
			{ index: 'Descripcion', name: 'Descripcion', label:'Descripcion', width: 150 },
			{ index: 'F_Inicio',  name: 'F_Inicio',  label:'F.Inicio', width: 100 },
			{ index: 'F_Fin',  name: 'F_Fin',  label:'F.Fin', width: 100 },
			{ index: 'Activo',  name: 'Activo',  label:'Activo', width: 50 },
		],
		sortname:"Cupon",
		<?php echo $options_comunes; ?>,
		onSelectRow: function(id){
			$('#info_adicional').html("<img src='/img/Loading-data.gif'/>").load("./Listados_PHP/ajax_listados/listado_cupones.php?opcion_cupo=get_cupo&IP_Tienda="+IP_Tienda+"&CUPON="+id);
		},
	});
	jqGridtmp.jqGrid('filterToolbar',{ stringResult: true });
// ---------------------------------------------------------------------------------------------------------------------------------------------------
	var jqGridtmp=$("#jqGrid_cupoclie");
	jqGridtmp.jqGrid({
		caption: "LISTADO ASIGNACION CUPON-CLIENTE",
		url: "./Listados_PHP/ajax_listados/listado_cupon_cliente.php?opcion_cupo=get_list_cupo&IP_Tienda="+IP_Tienda,
		colModel:
		[
			{ index: 'Cupon', 	name: 'Cupon',    label:'CUPON', key: true, width: 60 },
			{ index: 'Cliente', name: 'Descripcion', label:'Descripcion', width: 150 },
			{ index: 'TC',  name: 'TC',  label:'Tipo Cupon', width: 100 },
			{ index: 'PP',  name: 'PP',  label:'Prioridad Impresion', width: 100 },
			{ index: 'IM',  name: 'IM',  label:'Impreso', width: 50 },
		],
		sortname:"Cupon",
		<?php echo $options_comunes; ?>,
		onSelectRow: function(id){
			console.log(id);
			$('#info_adicional').html("<img src='/img/Loading-data.gif'/>").load("./Listados_PHP/ajax_listados/listado_cupones.php?opcion_cupo=get_cupo&IP_Tienda="+IP_Tienda+"&CUPON="+id);
		},
	});

	jqGridtmp.jqGrid('filterToolbar',{ stringResult: true });

	$(".ui-jqgrid-titlebar").click(function() {
		$("#jqGrid_*").jqGrid('setGridState','hidden');
		$(".ui-jqgrid-titlebar-close", this).click();
	});
</script>