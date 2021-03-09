<title>PENDIENTES DE SERVIR - FOTO</title>
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }

$c_zero=file_get_contents("/home/pendserv/trabajo/files_zero.dat");
$c_non_zero=file_get_contents("/home/pendserv/trabajo/files_non_zero.dat");
$c_resultados=file_get_contents("/home/pendserv/trabajo/files_regularizados.dat");

$link_foto = "/tmp/trabajo/foto.csv";
$link_resultados = "/tmp/trabajo/resultado.csv";

?>
<style>
	.rdiv {
		/*float:left; position:relative;*/
		border-radius:4px;
		border:1px solid red;
		background-color:white;
		margin:1px;	
	}
	#status_foto { font-weight: bold;}
</style>

<div class='rdiv'>
	<table>
		<tr>
			<td colspan="3" class="rdiv">
				<b>FECHA FOTO: 08/01/2017</b>
				 - 
				<span id="status_foto">Status: <b style="color:blue">ACTIVADO</b></span>
				 - 
				<span id="refresco"></span>
			</td>
		</tr>

		<tr>
			<td class="rdiv" id="t_desglose_ficheros"><div id='desglose_ficheros' style='width:300; height:240'></div></td>
			<td class="rdiv" id="t_procesados"><div id='procesados' style='width:300; height:240'></div></td>
			<td rowspan="2" class="rdiv" id="t_tiendas"><div id='tiendas' style='width:450; height:400;'><img src='/img/cargando.gif'/></div></td>
		</tr>
		<tr>
			<td colspan="2" class="rdiv">
				<div id="t_graficos3" class=""></div>
				<table>
					<tr>
						<td><div id='clas_x_tipo' style='width:200; height:150; position:relative;'></div></td>
						<td><div id='clas_x_tipo_item' style='width:200; height:150; position:relative;'></div></td>
						<td>
							<div style="font-size:10px; padding:1em;">
								<a href="<?php echo $link_foto; ?>" target="_blank">Descargar foto en .CSV</a>
								<hr>
								<a href="<?php echo $link_resultados; ?>" target="_blank">Descargar resultado en .CSV</a>
								<br>
								<span id="prueba_r"></span>
								<hr>
								<button id="b_refresco">Refrescar</button>
							</div>
						</td>
					</tr>
				</table>
			</td>
			<td></td>
		</tr>

	</table>
</div>

<script>
	var timeout_historico=60;
	var interval_vista_historico;
	var tienda=0;
	var charts;

	$("#refresco").text("Sin refresco de actualizacion. Pulse el boton REFRESCAR.");
	
	$("#b_refresco").on("click", function () {
		$("#refresco").text("Actualizando datos... Espere por favor...");
		prepareCharts();
		$("#refresco").text("Sin refresco de actualizacion. Pulse el boton REFRESCAR.");
	})

	function drawCharts(){
		clearTimeout(interval_vista_historico);
		prepareCharts();
	}

	function Activa_Refresco(new_timeout) {
		clearTimeout(interval_vista_historico);
		timeout_historico=new_timeout;
		if (timeout_historico>0) {
			$("#refresco").text("Tiempo de refresco: "+timeout_historico+" segundos");
			interval_vista_historico=setTimeout(drawTable, timeout_historico*1000);
			console.log("Refresco puesto a "+timeout_historico+" segundos");
		} else {
				$("#refresco").text("Sin refresco.");
		}
	}

	var TiendasFoto;
	var Totales;
	var clas_x_tipo;
	var clas_x_tipo_item;

	function getAllData() {
		var jsonData = $.ajax({ async:false, url: "PS/json_foto.php", dataType: "json", timeout:20000 }).responseText;
		TiendasFoto = new google.visualization.DataTable(jsonData);
		var formatter = new google.visualization.NumberFormat({pattern:'#0%'});
		formatter.format(TiendasFoto, 4);

		var jsonData = $.ajax({ async:false, url: "PS/json_datos_varios.php?Totales=1.php", dataType: "json", timeout:20000 }).responseText;
		Totales = new google.visualization.DataTable(jsonData);
//		console.log(jsonData);

		var jsonData = getValues("PS/json_datos_varios.php?clas_x_tipo=1");
		clas_x_tipo = new google.visualization.DataTable(jsonData);

		var jsonData = getValues("PS/json_datos_varios.php?clas_x_tipo_item=1");
		clas_x_tipo_item = new google.visualization.DataTable(jsonData);
	}

	function refresca_1() {
//		$("#t_tiendas").load("PS/tabla.php");
		var tabla_tiendas = new google.visualization.ChartWrapper({
			chartType: 'Table', containerId: 'tiendas', dataTable: TiendasFoto,
			options: {
				title: 'Desglose de tiendas procesadas',
				titleTextStyle: { color: "#170B3B", fontSize: 16,bold: true},
				width:"100%", height:"100%"
			}
		});
		tabla_tiendas.draw();
		return;
	}

	function refresca_2() {
		var pieChart1 = new google.visualization.ChartWrapper({
			chartType: 'PieChart', containerId: 'desglose_ficheros', dataTable: Totales,
			options: {
				title: 'Ficheros de la FOTO (Total <?php echo $c_non_zero+$c_zero; ?>)',
				titleTextStyle: { color: "#170B3B", fontSize: 16,bold: true},
					backgroundColor: "#FBEFF8",
					chartArea: {left:20,top:30,width:'100%',height:'80%'},
					pieSliceText: "value"
				}, view: {'rows': [0, 1]}
			});
		var pieChart2 = new google.visualization.ChartWrapper({
				chartType: 'PieChart', containerId: 'procesados', dataTable: Totales,
				options: {
					title: 'Resultado proceso foto',
					titleTextStyle: { color: "#170B3B", fontSize: 16,bold: true},
					chartArea: {left:20,top:30,width:'100%',height:'80%'},
					backgroundColor: "#E0F8F1",
					pieSliceText: "value"
				}, view: {'rows': [2, 3, 4]}
			});
		var pieChart3 = new google.visualization.ChartWrapper({
			chartType: 'PieChart', containerId: 'clas_x_tipo', dataTable: clas_x_tipo,
			options: {
				title: 'Tipo de tiendas procesadas', titleTextStyle: { color: "#170B3B", fontSize: 10,bold: true},
				chartArea: {left:20,top:30,width:'100%',height:'80%'},
				pieSliceText: "value"
			}
		});
 		var pieChart4 = new google.visualization.ChartWrapper({
			chartType: 'PieChart', containerId: 'clas_x_tipo_item', dataTable: clas_x_tipo_item,
			options: {
				title: 'Tipo de tiendas por articulos procesados', titleTextStyle: { color: "#170B3B", fontSize: 10,bold: true},
				chartArea: {left:20,top:30,width:'100%',height:'80%'},
				pieSliceText: "value", height:"100%"
			}
		});
		pieChart1.draw();
		pieChart2.draw();
		pieChart3.draw();
		pieChart4.draw();
	}

	function getValues(from){
		var result=null;
		result = $.ajax({ async:false, url: from, dataType: "json", timeout:20000 }).responseText;
//		$.ajax({ url: from, type: 'get', dataType: 'json', cache: false,
//			success: function(data) { console.log("Resultado: "+data); if(data != null){ result=data; } },
//		});
		return result;
	};

   var datos_tiendas;
	function prepareCharts() {
		if (document.getElementById('desglose_ficheros') === null)
			return;
		$("#desglose_ficheros").html("<img src='/img/cargando.gif'/>");
		getAllData();
		refresca_1();
		refresca_2();

		//Activa_Refresco(timeout_historico);
	}

	google.charts.setOnLoadCallback(drawCharts);

</script>
