<title>PENDIENTES DE SERVIR</title>
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }

$Alto_Tablas="700px";
$Fecha_Foto = '2017-01-08';

$tmp = myQUERY("SELECT count(*) from Inic_Pend_Serv WHERE DATE(Fecha) >= '$Fecha_Foto'");
$Inic_Tiendas = $tmp[0][0];

?>
<style>
	.rdiv {
		background-color:white; border:1px solid black; border-radius:2px; margin:2px;
		font: 12px helvetica, arial, sans-serif;
	}
	.centrado { font-size:16px; margin-left: 2em; text-align: center; vertical-align: middle; line-height: <?php echo $Alto_Tablas;?>; }
	.alto_tablas { height: <?php echo $Alto_Tablas;?>; }
	#tabla1 { width: 320px; }
	#detalle1 { width: 850px; }
</style>

<div class="rdiv">
	<div id="fecha_foto" class="rdiv" style="float:left; position:relative; padding:5px;">
		<p><b>Fecha de la foto: </b><span id="id_Fecha"></span></p>
		<hr>
		<p>
			<b>Tiendas con descuadre: </b><span id="numero_tiendas"></span>
			<b style="margin-left:2em;">Tiendas inicializaron pendientes: </b><span id="inic_tiendas"></span>
		</p>
	</div>
	<table>
	<tr>
		<td  class="rdiv"><div class="alto_tablas" id="tabla1"></div></td>
		<td  class="rdiv"><div class="alto_tablas" id="detalle1"><b class="centrado"> &#9664 Pulse en una tienda para ver el detalle</b></div></td>
	</tr>
</table>
</div>

<script type="text/javascript">
	var timeout_historico=0;
	var interval_vista_historico;
	var tabla;
	var tienda=0;
	var Inic_Tiendas = '<?php echo (!empty($Inic_Tiendas)?$Inic_Tiendas:"N/D"); ?>';
	var Fecha_Foto = '<?php echo (!empty($Fecha_Foto)?$Fecha_Foto:"N/D"); ?>';

	function drawCharts_Historico(){
		clearTimeout(interval_vista_historico);
		drawTable();
	}

	function Activa_Refresco(new_timeout) {
		clearTimeout(interval_vista_historico);
		timeout_historico=new_timeout;
		if (timeout_historico>0) {
			interval_vista_historico=setTimeout(drawTable, timeout_historico*1000);
			console.log("Refresco puesto a "+timeout_historico+" segundos");
		}
	}
    
	function drawTable() {
		if (document.getElementById('tabla1') === null)
			return;
		var jsonData=$.ajax({ async:false, url: "PS/json_ps.php?FechaFoto"+Fecha_Foto, dataType: "json", timeout:20000 }).responseText;
 		tabla = new google.visualization.DataTable(jsonData);
		var view_tabla = new google.visualization.Table(document.getElementById('tabla1'));
	 	view_tabla.draw(tabla, { height:"100%", width:"100%"});

		$("#id_Fecha").html(Fecha_Foto);
		$("#numero_tiendas").html(tabla.getNumberOfRows());
		$("#inic_tiendas").html(Inic_Tiendas);

		google.visualization.events.addListener(view_tabla, 'select', function (e) {
				var row = view_tabla.getSelection()[0].row;
				tienda=tabla.getValue(row, 0);
				Genera_Detalle(tienda);
		});

		function Genera_Detalle(tienda) {
			var jsonData=$.ajax({ async:false, url: "PS/json_ps.php?detalle=1&Tienda="+tienda, dataType: "json", timeout:20000 }).responseText;
 			var detalle = new google.visualization.DataTable(jsonData);
			var view_detalle = new google.visualization.Table(document.getElementById('detalle1'));
	 		view_detalle.draw(detalle, { width:"900"});
	 	}

		if (tienda > 0) Genera_Detalle(tienda);
		
		Activa_Refresco(timeout_historico);
	}

	google.charts.setOnLoadCallback(drawCharts_Historico);	

</script>
