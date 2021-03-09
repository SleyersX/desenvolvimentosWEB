<title>LISTADO TOTAL</title>
<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
	// PREPARAMOS LA VISTA PARA LA TABLA DINAMICA.
//  	myQUERY("
// 	drop view if exists tmpTiendas;
// 	create view tmpTiendas as select a.numerotienda, b.conexion, if(b.version is null,'NO DISPON.',b.version) as version, a.centro, a.tipo, a.subtipo, a.direccion, a.poblacion, a.provincia, a.telefono, a.IP, a.tipoConexion,a.tipoEtiquetadora,a.frescos,
// 	(select count(*) from Elementos c where c.Elemento like 'balanza%' and c.conexion=1 and a.numerotienda=c.tienda) 'Balanzas',
// 	(select count(*) from Elementos c where c.Elemento like 'pc%' and c.conexion=1 and a.numerotienda=c.tienda) 'PCs',
// 	(select count(*) from Elementos c where c.Elemento like 'impres%' and c.conexion=1 and a.numerotienda=c.tienda) 'Impresoras'
// 	from tiendas a
// 	LEFT JOIN $Table b ON a.numerotienda=b.tienda and caja=1");

$tmp=myQUERY("select distinct(Centro) from tmpTiendas order by 1");
	foreach($tmp as $k => $d) { @$Centros.=";".$d[0].":".$d[0]; }
$tmp=myQUERY("select distinct(Tipo)   from tmpTiendas order by 1");
	foreach($tmp as $k => $d) { @$Tipos.=";".$d[0].":".$d[0]; }
$tmp=myQUERY("select distinct(SubTipo) from tmpTiendas order by 1");
	foreach($tmp as $k => $d) { @$Subtipos.=";".$d[0].":".$d[0]; }
$tmp=myQUERY("select distinct(tipoEtiquetadora) from tmpTiendas order by 1");
	foreach($tmp as $k => $d) { @$Impresora.=";".$d[0].":".$d[0]; }
$tmp=myQUERY("select distinct(Version) from tmpTiendas order by 1");
	foreach($tmp as $k => $d) { @$Versiones.=";".$d[0].":".$d[0]; }
$tmp=myQUERY("select distinct(frescos) from tmpTiendas order by 1");
	foreach($tmp as $k => $d) { @$Frescos.=";".$d[0].":".$d[0]; }
$tmp=myQUERY("select distinct(NTPVS) from $Table order by 1");
	foreach($tmp as $k => $d) { @$NTPVS.=";".$d[0].":".$d[0]; }
$tmp=myQUERY("select distinct(VELA) from tmpTiendas order by 1");
	foreach($tmp as $k => $d) { @$VELA.=";".$d[0].":".$d[0]; }

$local_dir=get_url_from_local(dirname(__FILE__));

?>
<div>
	<style>
		#jqGridPager * { color:white; }
		.Tienda_Off { background:lightgray; color:red; }
		.es_vela { background:#E0E0F8; color:black; font-weight: bold;}
	</style>
	<div class="ui-jqgrid" id="gbox_jqGrid" dir="ltr">
		<table id="jqGrid" class="F_SIZE_12"></table>
		<div id="jqGridPager"></div>
	</div>
	<div id="detailsPlaceholder" style="margin-top:5px;">
		<table id="jqGridDetails" class="F_SIZE_12"></table>
		<div id="jqGridDetailsPager"></div>
	</div>
	<div id="dialog_actualizar" title="Actualizando datos" style="display:none" >
		<p><img src="/img/wait.gif"/></p>
		<p><span id="Mensaje_exec2"></span></p>
	</div>
<!-- 	<span id="tienda_elegida" style="display:none; float:left; top:0;"></span> -->
</div>
<script type="text/javascript"> 
	$("#dialog_actualizar").dialog({
		autoOpen: false, modal: true, width: 'auto', height: 'auto', resizable: false,
		open: function(event, ui) {
			var tienda=$("#tienda_elegida").html();
			var centro=$("#centro_elegido").html();
			var pais='<?php echo $Pais; ?>';
			if (centro == "SEDE") var pais='XXX';
			var parametros = { "OPCION":"UPDATE_DATA", "Tienda":tienda, "Pais":pais }
			Ejecuta_AJAX("Mensaje_exec2", parametros );
			$(this).close;
		}
	});

	$.jgrid.defaults={
		responsive:true,
		recordtext: "Vista {0} - {1} de {2}",
		emptyrecords: "No se han encontrado registros...",
		loadtext: "Cargando datos...",
		pgtext : "Pag. {0} de {1}",
		page: 1, viewrecords: true, gridview: true,
		scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
	};
	var local_dir="<?php echo $local_dir; ?>";
	var w_descarga;
	console.log(local_dir);
	function Connect_to_Store(tienda,centro) {
		window.open(DIR_RAIZ+"/Estado_Monitorizacion/Conexion_Tienda/pre_conectar.php?Tienda="+tienda+"&Centro="+centro);
		return;
	}
	function imageFormat( cellvalue, options, rowObject ){
		if (cellvalue != null) return '<img src="/img/user-online.png" height="12" width="12"/>';
		else return '<img src="/img/process-stop.png" height="12" width="12"/>';
	}
	$(document).ready(function () {
		$("#jqGrid").jqGrid({
			caption: 'LISTADO TOTAL DE TIENDAS',
			url: local_dir+'/json_tiendas.php?callback=?&qwery=orders',
			mtype: "GET",
			datatype: "jsonp",
			colModel: [

{ index: 'NumeroTienda', name: 'NumeroTienda', label:'Tienda', key: true, width: 60, frozen:true },
{ index: 'conexion', name:'',label:'', width:15, formatter:imageFormat, stype:"none"},
{ index: 'Version', name: 'Version', label:'Version', stype: "select", searchoptions: { value: ":[All]<?php echo $Versiones; ?>" },
 width: 75 },
{ index: 'VELA', name: 'VELA', label:'VELA', stype: "select", searchoptions: { value: ":[All]<?php echo $VELA; ?>" }, width: 30 },
{ index: 'Centro', name: 'Centro', label:'Centro', stype: "select", searchoptions: { value: ":[All]<?php echo $Centros; ?>" }, width: 100 },
{ index: 'Tipo',      name: 'Tipo', label:'Tipo', stype: "select", searchoptions: { value: ":[All]<?php echo $Tipos; ?>" }, width: 50 },
{ index: 'Subtipo',   name: 'Subtipo', label:'Subtipo', stype: "select", searchoptions: { value: ":[All]<?php echo $Subtipos; ?>" }, width: 50 },
{ index: 'Direccion', name: 'Direccion', label:'Direccion', width: 150 },
{ index: 'Poblacion', name: 'Poblacion', label:'Poblacion', width: 80 },
{ index: 'CP', name: 'CP', label:'C.P.', width: 50 },
{ index: 'Provincia', name: 'Provincia', label:'Provincia', width: 80 },
{ index: 'Telefono',  name: 'Telefono', label:'Telefono',   width: 100 },
{ index: 'IP', name: 'IP', label:'Direccion IP', width: 100 },
{ index: 'NTPVS', name: 'NTPVS', label:'N.TPVS', stype: "select", searchoptions: { value: ":[All]<?php echo $NTPVS; ?>" }, width: 20 },
{ index: 'tipoEtiquetadora', name: 'tipoEtiquetadora', label:'Impresora', stype: "select", searchoptions: { value: ":[All]<?php echo $Impresora; ?>" }, width: 50 },
{ index: 'frescos', name: 'frescos', label:'PC', stype: "select", searchoptions: { value: ":[All]<?php echo $Frescos; ?>" }, width: 50 },
{ index: 'Balanzas',  name: 'Balanzas', label:'Balanzas', width: 50 },
{ index: 'PCs',  name: 'PCs', label:'PCs', width: 50 },
{ index: 'Impresoras',  name: 'Impresoras', label:'Impresoras', width: 50 },
			],
			sortname:"NumeroTienda",
// 			multiSort: true,
			height:430, autowidth: true,
			rowNum: 50,
			pager: "#jqGridPager",
			search: true,	refresh: true,
			onSelectRow: function(rowid, selected) {
				if(rowid) {
					var rdata = $('#jqGrid').jqGrid('getRowData', rowid);
					jQuery("#jqGridDetails").jqGrid('setCaption', 'Detalle de la tienda: <span id="tienda_elegida">'+rdata.NumeroTienda+'</span><span id="centro_elegido" style="display:none">'+rdata.Centro+'</span>');
					jQuery("#jqGridDetails").jqGrid('setGridParam',{url:local_dir+"/json_cajas.php?callback=?&qwery=orders&Tienda="+rdata.NumeroTienda+"&Centro="+rdata.Centro, datatype:"jsonp"});
					jQuery("#jqGridDetails").trigger("reloadGrid");
					$("#tienda_elegida").html(rdata.NumeroTienda);
// 					$("#Boton_Info").pulse({opacity: 0.8}, {duration : 100, pulses : 5});
				}
			},
			shrinktofit:true,
			onSortCol : clearSelection,
			loadComplete: function () {
				var rowIds = $('#jqGrid').jqGrid('getDataIDs');
				for (i = 0; i < rowIds.length; i++) {//iterate over each row
					rowData = $('#jqGrid').jqGrid('getRowData', rowIds[i]);
					//set background style if ColValue === true\
					if (rowData['VELA'] == "SI") {
						$('#jqGrid').jqGrid('setRowData', rowIds[i], false, "es_vela ");
					} //if
					if (rowData['Version'] == "NO DISPON." || rowData['Version'] == "IP.INCORR.") {
						$('#jqGrid').jqGrid('setRowData', rowIds[i], false, "Tienda_Off ");
					} //if
				} //for
			}//loadComplete
		});

		$('#jqGrid').jqGrid('filterToolbar',{
                stringResult: true
                // instuct the grid toolbar to show the search options
//                 searchOperators: true
            });

		$('#jqGrid').navGrid("#jqGridPager", {
			search: false, edit:false, add:false, del:false, refresh: true
		})
		// add custom button to export the data to excel
		.navSeparatorAdd("#jqGridPager",{})
		.navButtonAdd('#jqGridPager',{
			caption:"Excel",
			title:"Permite salvar el listado total en formato CSV para ser cargado por hojas de calculo",
			buttonicon:"ui-icon-document",
			onClickButton : function () { 
				window.open(local_dir + '/json_tiendas.php?csv=1', '');
			}
		})
		.navSeparatorAdd("#jqGridPager",{})
		.navButtonAdd('#jqGridPager',{
			id:"#Boton_Info",
			caption:"CONECTAR TIENDA",
			title:"Permite acceder a la pagina para conectar a la tienda",
   			buttonicon:"ui-icon-info",
			onClickButton : function () {
				tienda=$("#tienda_elegida").html();
				centro=$("#centro_elegido").html();
				if(tienda>0) {
					Connect_to_Store(tienda,centro);
				} else {
					alert("Debe elegir una tienda\nYou must choose a record");
				}
			}
		});

		function ON_OFF( cellvalue, options, rowObject ){
			if (cellvalue == 0) return '<b style="color:red">NO</b>';
			return '<span style="color:BLUE">SI</span>';
		}
		function MAYOR_QUE_CERO( cellvalue, options, rowObject ){
			if (cellvalue > 0) return '<b style="color:red">'+cellvalue+'</b>';
			return '<span style="color:BLUE">'+cellvalue+'</span>';
		}

		$("#jqGridDetails").jqGrid({
			url: local_dir+'/json_cajas.php',
			mtype: "GET",
			datatype: "json",
			colModel: [
				{ label: 'Caja', name: 'Caja', key: true, width: 50 },
				{ label: 'APP', name: 'APP', width: 50, formatter:ON_OFF },
				{ label: 'MYSQL', name: 'MYSQL', width: 50, formatter:ON_OFF  },
				{ label: 'WSD', name: 'WSD', width: 50, formatter:ON_OFF  },
				{ label: 'Version', name: 'Version', width: 50 },
				{ label: 'Modelo', name: 'Modelo', width: 50 },
				{ label: 'BIOS', name: 'BIOS', width: 50 },
				{ label: 'RAM', name: 'RAM', width: 50 },
				{ label: 'HDD', name: 'HDD', width: 100 },
				{ label: 'Temper.', name: 'Temper.', width: 50 },
				{ label: 'Err.LAN', name: 'Err.LAN', width: 50, formatter:MAYOR_QUE_CERO },
				{ label: 'N.Apag.', name: 'N.Apag.', width: 50, formatter:MAYOR_QUE_CERO }
			],
			autowidth: true,
			rowNum: 10,
			height: '150',
			caption: 'Detalle de la tienda: <span id="tienda_elegida"></span>',
			pager: "#jqGridDetailsPager"
		});

		function clearSelection() {
			jQuery("#jqGridDetails").jqGrid('setGridParam',{url: "", datatype: 'json'}); // the last setting is for demo purpose only
			jQuery("#jqGridDetails").jqGrid('setCaption', 'Detail Grid:: none');
			jQuery("#jqGridDetails").trigger("reloadGrid");
		}
	});

</script>

<!--</body>
</html>-->
