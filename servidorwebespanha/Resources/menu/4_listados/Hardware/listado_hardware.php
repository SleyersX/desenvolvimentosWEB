<title>LISTADO HARDWARE</title>
<?php
	require($_SERVER['DOCUMENT_ROOT']."/config.php");
	// PREPARAMOS LA VISTA PARA LA TABLA DINAMICA.

$Tiendas=Prepara_Lista_Select("Tienda","tmpHardware");
$Cajas=Prepara_Lista_Select("Caja","tmpHardware");
$Centros=Prepara_Lista_Select("Centro","tmpHardware");
$COMS1=Prepara_Lista_Select("COM_1","tmpHardware");
$COMS2=Prepara_Lista_Select("COM_2","tmpHardware");
$COMS3=Prepara_Lista_Select("COM_3","tmpHardware");
$COMS4=Prepara_Lista_Select("COM_4","tmpHardware");
$COMS5=Prepara_Lista_Select("COM_5","tmpHardware");
$COMS6=Prepara_Lista_Select("COM_6","tmpHardware");
$COMS7=Prepara_Lista_Select("COM_7","tmpHardware");
$COMS8=Prepara_Lista_Select("COM_8","tmpHardware");

$BIOSES=Prepara_Lista_Select("BIOS","tmpHardware");
$CMOSES=Prepara_Lista_Select("CMOS","tmpHardware");
$TPVES=Prepara_Lista_Select("TPV","tmpHardware");
$CPUS=Prepara_Lista_Select("CPU","tmpHardware");
$HUBS=Prepara_Lista_Select("HUB","tmpHardware");
$RAIDES=Prepara_Lista_Select("RAID","tmpHardware");

$EMVSWES=Prepara_Lista_Select("EMVSW","tmpHardware");
$TECLADOS=Prepara_Lista_Select("TECLADO","tmpHardware");
if ($PAIS_SERVER=="BRA") $IMPRESORAS=Prepara_Lista_Select("IMPRESORA","tmpHardware");
if ($PAIS_SERVER=="ARG") $IMPRESORAS=Prepara_Lista_Select("PV","Info_PV");

// $Tipos_Subtipos=Prepara_Lista_Select("tipo_subtipo","tmpHardware");
// $Modelos=Prepara_Lista_Select("Modelo","tmpHardware");
// $Versiones=Prepara_Lista_Select("Version","tmpHardware");
// $RAMs=Prepara_Lista_Select("RAM","tmpHardware");
// $BIOSs=Prepara_Lista_Select("BIOS","tmpHardware");
// $HUBs=Prepara_Lista_Select("HUB","tmpHardware");
// $PINPADs=Prepara_Lista_Select("PINPAD","tmpHardware");
// $RAIDs=Prepara_Lista_Select("RAID","tmpHardware");

?>
<style>
	#jqGridPager * { color:white; }
	.Tienda_Off { background:lightgray; color:auto; }
</style>

<div>
	<div class="ui-jqgrid " id="gbox_jqGrid" dir="ltr">
		<table id="jqGrid" class="F_SIZE_12"></table>
		<div id="jqGridPager"></div>
	</div>
</div>
<script type="text/javascript"> 
	$.jgrid.defaults.responsive = true;
	var local_dir="<?php echo get_url_from_local(dirname(__FILE__)); ?>";

	$(document).ready(function () {
		$("#jqGrid").jqGrid({
			caption: '- LISTADO TOTAL DE HARDWARE -',
			url: local_dir+'/json_hardware.php?callback=?&qwery=orders',
			mtype: "GET",
			datatype: "jsonp",
			colModel: [
				{ index: 'Tienda', 		name: 'Tienda', 	label:'Tienda', key: true, width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $Tiendas; ?>" } },
				{ index: 'Caja', name: 'Caja', label:'Caja', width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $Cajas; ?>" } },
				{ index: 'Centro', 		name: 'Centro', 	label:'Centro', width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $Centros; ?>" } },
				{ index: 'Fecha', 		name: 'Fecha', 	label:'Fecha', width: 100 },

				{ index: 'COM_1', 	name: 'COM_1', label:'COM1', 	width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $COMS1; ?>" } },
				{ index: 'COM_2', 	name: 'COM_2', label:'COM2', 	width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $COMS2; ?>" } },
				{ index: 'COM_3', 	name: 'COM_3', label:'COM3', 	width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $COMS3; ?>" } },
				{ index: 'COM_4', 	name: 'COM_4', label:'COM4', 	width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $COMS4; ?>" } },
				{ index: 'COM_5', 	name: 'COM_5', label:'COM5', 	width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $COMS5; ?>" } },
				{ index: 'COM_6', 	name: 'COM_6', label:'COM6', 	width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $COMS6; ?>" } },
				{ index: 'COM_7', 	name: 'COM_7', label:'COM7', 	width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $COMS7; ?>" } },
				{ index: 'COM_8', 	name: 'COM_8', label:'COM8', 	width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $COMS8; ?>" } },

				{ index: 'BIOS', 		name: 'BIOS', 		label:'BIOS', width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $BIOSES; ?>" } },
				{ index: 'NSERIETPV', 	name: 'NSERIETPV', label:'NSERIETPV', width: 80, },
				{ index: 'CMOS', 		name: 'CMOS', 		label:'CMOS', width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $CMOSES; ?>" } },
				{ index: 'TPV', 		name: 'TPV', 		label:'TPV', width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $TPVES; ?>" } },
				{ index: 'CPU', 		name: 'CPU', 		label:'CPU', width: 150,
					stype: "select", searchoptions: { value: ":[All]<?php echo $CPUS; ?>" } },
				{ index: 'HUB', 		name: 'HUB', 		label:'HUB', width: 50,
					stype: "select", searchoptions: { value: ":[All]<?php echo $HUBS; ?>" } },
				{ index: 'RAID', 		name: 'RAID', 		label:'RAID', width: 80,
					stype: "select", searchoptions: { value: ":[All]<?php echo $RAIDES; ?>" } },
				{ index: 'DISCOA', 		name: 'DISCOA', 	label:'DISCOA', width: 100, },
// 				{ index: 'SERDISCOA', 	name: 'SERDISCOA', 	label:'SERDISCOA', width: 20, },
				{ index: 'DISCOB', 		name: 'DISCOB', 	label:'DISCOB', width: 100, },
// 				{ index: 'SERDISCOB', 	name: 'SERDISCOB', 	label:'SERDISCOB', width: 20, },
				{ index: 'EMVSW', 		name: 'EMVSW', 	label:'EMVSW', width: 100,
					stype: "select", searchoptions: { value: ":[All]<?php echo $EMVSWES; ?>" } },
				{ index: 'EMVHW', 		name: 'EMVHW', 	label:'EMVHW', width: 100, },
				{ index: 'EXPANSORA', 	name: 'EXPANSORA', 	label:'EXPANSORA', width: 20, },
				{ index: 'MEM', 		name: 'MEM', 		label:'MEM', width: 50, },
				{ index: 'TECLADO', 	name: 'TECLADO', 	label:'TECLADO', width: 100,
					stype: "select", searchoptions: { value: ":[All]<?php echo $TECLADOS; ?>" } },
				{ index: 'USB0', 		name: 'USB0', 		label:'USB0', width: 100, },
				{ index: 'USB1', 		name: 'USB1', 		label:'USB1', width: 100, },
				{ index: 'USB2', 		name: 'USB2', 		label:'USB2', width: 100, },
				{ index: 'USB3', 		name: 'USB3', 		label:'USB3', width: 100, }
				<?php
					if ($PAIS_SERVER=="BRA")
						echo ",{ index: 'IMPRESORA', name: 'IMPRESORA', label:'IMPRESORA', width: 100, stype: \"select\", searchoptions: { value: \":[All]$IMPRESORAS\" } }";
					if ($PAIS_SERVER=="ARG"  && SoyYo() )
						echo ",{ index: 'b.PV', name: 'b.PV', label:'N.TPV.FISC.', width: 50, stype: \"select\", searchoptions: { value: \":[All]$IMPRESORAS\" } }";
				?>
			],
			sortname:"Tienda,Caja",
			gridview: true,
			viewrecords: true,
			page: 1,
			height:650,
			autowidth: true,
// 			width:1260,
			rowNum: 50,
			scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
			pager: "#jqGridPager",
			search: true,
			refresh: true,
 			sortable: true,
			shrinkToFit: false,
// 			altRows: true,
			onSelectRow: function(rowid, selected) {
				if(rowid) {
					var rdata = $('#jqGrid').jqGrid('getRowData', rowid);
					$("#tienda_elegida").html(rdata.Tienda);
					$("#centro_elegido").html(rdata.Centro);
				}
			},
			loadComplete: function () {
				var rowIds = $('#jqGrid').jqGrid('getDataIDs');
				for (i = 0; i < rowIds.length; i++) {//iterate over each row
					rowData = $('#jqGrid').jqGrid('getRowData', rowIds[i]);
					//set background style if ColValue === true\
					if (rowData['Caja'] == 1) {
						$('#jqGrid').jqGrid('setRowData', rowIds[i], false, "Tienda_Off ");
					} //if
				} //for
			}//loadComplete
		});
		jQuery("#jqGrid").jqGrid('setFrozenColumns');

		$('#jqGrid').jqGrid("destroyFrozenColumns")
			.jqGrid("setColProp", "Tienda", { frozen: true })
			.jqGrid("setFrozenColumns")
			.trigger("reloadGrid", [{ current: true}]);

		$('#jqGrid').jqGrid('filterToolbar',{
                stringResult: true
                // instuct the grid toolbar to show the search options
//                 searchOperators: true
            });

		$('#jqGrid').navGrid("#jqGridPager", {
			search: true, edit:false, add:false, del:false, refresh: true
		})
		// add custom button to export the data to excel
		.navSeparatorAdd("#jqGridPager",{})
		.navButtonAdd('#jqGridPager',{
			caption:"Excel",
			title:"Permite salvar el listado total en formato CSV para ser cargado por hojas de calculo",
			buttonicon:"ui-icon-document",
			onClickButton : function () { 
				window.open(local_dir + '/json_hardware.php?csv=1', '');
			}
		})
		.navSeparatorAdd("#jqGridPager",{})
		.navButtonAdd('#jqGridPager',{
			id:"#Boton_Info",
			caption:"CONECTAR A TIENDA<span id='tienda_elegida' style='display:none'></span><span id='centro_elegido' style='display:none'></span>",
   			buttonicon:"ui-icon-info",
			onClickButton : function () {
				tienda=$("#tienda_elegida").html(); centro=$("#centro_elegido").html();
				if(tienda>0) {
					Connect_to_Store(tienda,centro);
				} else {
					alert("Debe elegir una tienda\nYou must choose a record");
				}
			}
		});
// .navButtonAdd('#jqGridPager',
//                 {
//                     buttonicon: "ui-icon-calculator",
//                     title: "Column chooser",
//                     caption: "Columns",
//                     position: "last",
//                     onClickButton: function() {
// 						// call the column chooser method
// 						jQuery("#jqGrid").jqGrid('columnChooser');
// 					}
//                 });

	});
</script>

</body>
</html>