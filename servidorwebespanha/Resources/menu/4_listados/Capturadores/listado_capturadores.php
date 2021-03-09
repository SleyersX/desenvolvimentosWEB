<title>LISTADO CAPTURADORES</title>
<?php
	require($_SERVER['DOCUMENT_ROOT']."/config.php");
	// PREPARAMOS LA VISTA PARA LA TABLA DINAMICA.

$fTienda="{ index: 'Tienda', name: 'Tienda', label:'Tienda', key: true, width: 50, stype: 'select', searchoptions: { value: ':[All]".Prepara_Lista_Select("Tienda","Capturadores")."' } }";
$fFecha="{ index: 'Fecha', name: 'Fecha', label:'Fecha', width: 120, stype: 'select', searchoptions: { value: ':[All]".Prepara_Lista_Select("Date(Fecha)","Capturadores")."' } }";
$fCapturador="{ index: 'Capturador', name: 'Capturador', label:'Capturador', key: true, width: 150, stype: 'select', searchoptions: { value: ':[All]".Prepara_Lista_Select("Capturador","Capturadores")."' } }";
$fAdicional="{ index: 'Adicional', name: 'Adicional', label:'Adicional', key: true, width: 500 }";

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
			caption: '- LISTADO TOTAL DE CAPTURADORES -',
			url: local_dir+'/json_capturadores.php?callback=?&qwery=orders',
			mtype: "GET",
			datatype: "jsonp",
			colModel: [ <?php echo $fTienda.",".$fFecha.",".$fCapturador.",".$fAdicional; ?> ],
			sortname:"Tienda",
			gridview: true,
			viewrecords: true,
			page: 1,
			height:650,
			autowidth: true,
			rowNum: 50,
			scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
			pager: "#jqGridPager",
			search: true,
			refresh: true,
 			sortable: true,
			shrinkToFit: false,
		});

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
				window.open(local_dir + '/json_capturadores.php?csv=1', '');
			}
		})
		.navSeparatorAdd("#jqGridPager",{})
		.navButtonAdd('#jqGridPager',{
			id:"#Boton_Info",
			caption:"CONECTAR A TIENDA<span id='tienda_elegida' style='display:none'></span><span id='centro_eligido' style='display:none'></span>",
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