<?php
$No_Carga_ssh2=true;
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once($DOCUMENT_ROOT.$DIR_LIBRERIAS."/chart.php");
$dir_actual=dirname(__FILE__);
$PAIS=empty($hsr_config)?"BRA":$hsr_config->pais;
switch ($PAIS) {
	case "BRA":
		$url_actual=str_replace($_SERVER['DOCUMENT_ROOT'], "/", __FILE__);
		break;
	default:
		$url_actual=str_replace($_SERVER['DOCUMENT_ROOT'], "", __FILE__); break;
}
$URL_JSON_INTERVENCIONES="/Resources/menu/5_intervenciones/Historico_HOY/json_intervenciones.php";  

?>
<title>HISTORICO MOVIMIENTOS</title>
<style>
	.Instalacion { background-color: #58FA58; }
	.Instalacion:hover { background-color: #58FA59; }
	.Intervencion { background-color:#F5DA81; }
	.Intervencion:hover { background-color:#F5DA82; }
	.ERROR { background-color:#FA5858; }
	.ERROR:hover { background-color:#FA5859; }
	#grafica_historico_old { border: 1px solid #ccc; width:500; height:100;margin-bottom:2px }
	#Actu_Futu { border: 1px solid #ccc; height:100;margin-bottom:2px;overflow:auto; }
	#vista_historico { border: 1px solid #ccc; overflow:auto; }
	.panel_historico {
		background-color: whitesmoke;
		border:1px solid black;
		border-radius: 3px;
		box-shadow: 1px 0 0 rgba(255, 255, 255, 0.7);
		float:left;
	}
	#graficas { border-right:1px solid gray; }

</style>

<div style='border:1px solid black; border-radius:2px;background-color:white;'>
	<table id="tabla_intervenciones">
		<tr>
			<td style="border: 1px solid black; border-radius: 3px; padding: 5px;"><div id='Actu_Futu'></div></td>
		</tr>
		<tr>
			<td >
				<div class="panel_historico">
					<table style="width:100%">
						<tr>
							<td width="50%" id="graficas">
								<canvas id="grafico_resumen_historico" height="200"></canvas>
							</td>
							<td width="50%" id="tablas">
								<canvas id='grafica_historico' height="100"></canvas>
								<div id='vista_resumen_historico_global'></div>
								<div id='vista_resumen_historico'></div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id='vista_historico'></div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="regions_div" style="width: 900px; height: 500px;"></div>
			</td>
		</tr>
	</table>
</div>
</body>

<script>
	var parado=false;
	var url_actual="<?php echo $URL_JSON_INTERVENCIONES; ?>";
	//clearInterval(interval_resumen_historico_hoy);
//	var interval_resumen_historico_hoy=en_background("#vista_resumen_historico", url_actual+'?opcion=Resumen_Intervenciones',10000);
	clearInterval(interval_historico_hoy); var interval_historico_hoy=en_background("#vista_historico", url_actual+'?opcion=Historico',10000);
	clearInterval(interval_actu_futu);     var interval_actu_futu=en_background("#Actu_Futu", url_actual+'?opcion=Actu_Futu',5000);

	var timeout_historico=10;
	var timeout_resumen=10;
	var interval_vista_historico, interval_vista_resumen;
	var clases=[ { css:"Otros", color:"yellow"}, {css:"Intervencion", color:"orange"}, {css:"Instalacion", color:"green"},{css:"ERROR", color:"red"}];
	var graph_planograma;
	var graph_resumen_historico;
	if (graph_planograma != null) { graph_planograma.destroy(); graph_planograma=null; } 
	if (graph_resumen_historico != null) { graph_resumen_historico.destroy(); graph_resumen_historico=null; }
	clearTimeout(interval_vista_resumen);

	function Pinta_Graph_Resumen_Historico(datasets, labels) {
		if (graph_resumen_historico == null) {	
			graph_resumen_historico = new Chart(document.getElementById("grafica_historico").getContext("2d"), {
				type: "bar", data: { datasets: datasets, labels: labels },
				options: {
					responsive: true,
					legend: { display: false},					
					tooltips: { mode: 'index', intersect: false}, 
					title: { display: true, text: "Resumen acciones" },
					animation: { duration: 0 } 
				}
			});
		} else {
			graph_resumen_historico.data = { datasets: datasets, labels: labels };
			graph_resumen_historico.update();
		}
	}

	function Pinta_Graph_Planograma(datasets, labels) {
		console.log(graph_planograma,interval_vista_resumen);
		if (graph_planograma == null) {
			graph_planograma = new Chart(document.getElementById("grafico_resumen_historico").getContext("2d"), {
				type: "line",
				data: { labels:labels, datasets: datasets },
				options: {
					responsive: true,
					legend: { display: false},					
					tooltips: { mode: 'index', intersect: false}, 
					title: { display: true, text: "Resumen acciones" },
					animation: { duration: 0 } 
				}
			});
		}
		else {
			graph_planograma.data = { labels:labels, datasets: datasets };
			graph_planograma.update();
		}
	}		

		
	function Pinta_Resumen() {
		if (document.getElementById('vista_resumen_historico') === null) {
			clearTimeout(interval_vista_resumen); return;
		}
		$.getJSON(url_actual+'?opcion=Resumen_Intervenciones',function (json) {
			console.log(json);
			var texto=[], cant=[];
			var $table = $('<table id="t_resumen_historico_hoy" class="tabla2"></table>');
			$table.append("<tr><th>Intervencion/Accion</th><th>Cantidad</th></tr>")
			$.each(json.Resumen, function(index, item){
				texto.push(item.texto); cant.push(item.cant);
				var tr = $('<tr>').append($('<td>').text(item.texto), $('<td>').text(item.cant)).addClass(clases[item.tipo].css);
				$table.append(tr);
			});
			$("#vista_resumen_historico").html($table);

			var tipos=[], cant2=[];
			$.each(json.Resumen_Pie, function(index, item){ tipos.push(item.tipo); cant2.push(item.cant); });

			Pinta_Graph_Resumen_Historico(
				[{ label: "Cantidad de acciones", data: cant2, backgroundColor:["yellow","orange","green","red"	], borderColor:"black"}],
				["OTROS", "INTERV.","INSTAL.","ERRORES"]);

			var hora=[], otros=[], interv=[], instal=[], errores=[], totales=[];
			$.each(json.get_totals, function(index, item){
				hora.push(item.hora); otros.push(item.otros); interv.push(item.interv); instal.push(item.instal); errores.push(item.errores);
				totales.push(item.otros+item.interv+item.instal+item.errores);
			});

			Pinta_Graph_Planograma(
				[
					{ label: "Total", data: totales, backgroundColor: 'transparent', borderColor: 'black'},
					{ label: "Otros", data: otros, backgroundColor: 'transparent', borderColor: 'gray'},
					{ label: "Intervenciones", data: interv, backgroundColor: 'transparent', borderColor: 'orange'},
					{ label: "Instalaciones", data: instal, backgroundColor: 'transparent', borderColor: 'green'},
					{ label: "Errores", data: errores, backgroundColor: 'transparent', borderColor: 'red'}
				],
				hora
				);
			interval_vista_resumen=setTimeout(Pinta_Resumen, timeout_resumen*1000);
		});
	}
    
	Pinta_Resumen();

</script>
