<title>CUPONES - TODOS LOS SERVIDORES</title>
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }

?>
<style>
	.graph_cupon {
			float: left;
			width: 700px;
			height: 400px;
	} 
</style>

<div class="graph_cupon" id="graph1_ESP" ></div>
<div class="graph_cupon" id="graph1_POR" ></div>
<div class="graph_cupon" id="graph1_ARG" ></div>
<div class="graph_cupon" id="graph1_BRA" ></div>
</table>

<script type="text/javascript">
	var timeout_historico=10;
	var interval_vista_historico;
	var d;

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
    
	function get_hora_local(pais, bonito) {
		var quita;
		switch(pais) {
			case "ESP": quita=0; break;	
			case "POR": quita=1; break;
			case "ARG": quita=4; break;
			case "BRA": quita=3; break;
			
		}
		if (bonito===true)
			return (d.getHours()-quita)+":"+d.getMinutes()+":"+d.getSeconds();
		else
			return [d.getHours()-quita, d.getMinutes(),0];
	}    
    
	function drawTable() {
		if (document.getElementById('graph1_ESP') === null)
			return;

		var jsonData=$.ajax({
 			async:false,
			url: "Servidor_Cupones/json_servidor_cupones.php",
 			dataType: "json",
			timeout:20000
 		}).responseText;

 		var tabla = new google.visualization.DataTable(jsonData);

		function Crea_Graph_Tiendas_Pais(pais, id_graph, titulo) {
			d = new Date();
			var new_view = new google.visualization.DataView(tabla);
 			new_view.setRows(tabla.getFilteredRows([{column:7, value:pais}]));
			new_view.setColumns([
				{ calc:toHour, sourceColumn:0, label:'Periodo', type:'timeofday' },
				{ sourceColumn:1, label:"SC1", type:'number'},
				{ sourceColumn:3, label:"SC2", type:'number'},
			]);
	 		var options= {
				title: titulo + " (Hora local: " + get_hora_local(pais,true) + ")",
				focusTarget: 'category', width:"100%", height:"600px",
	 			hAxis: {
					title: 'Time of Day', format: 'HH:mm', viewWindow: { min: [6, 30, 0], max: get_hora_local(pais,false) },
					textStyle: { fontSize: 12, color: '#053061', bold: true, italic: false },
					titleTextStyle: { fontSize: 14, color: '#053061', bold: true, italic: false }
				},
				vAxis: { minValue: 0 },
				chartArea: {width: '75%', height: '60%'},
				displayAnnotations: true
	 		};
			var wrapper = new google.visualization.ChartWrapper({
				chartType: 'ColumnChart', dataTable: new_view, options: options, containerId: id_graph });

	 		return wrapper;
		}

		function Crea_Graph_WEB_Pais(pais, id_graph, titulo) {
			var new_view = new google.visualization.DataView(tabla);
 			new_view.setRows(tabla.getFilteredRows([{column:7, value:pais}]));
			new_view.setColumns([
				{ calc:toHour, sourceColumn:0, label:'Periodo', type:'timeofday' },
				{ sourceColumn:2, label:"SC1", type:'number'},
				{ sourceColumn:4, label:"SC2", type:'number'},
			]);
	 		var options= {
				title: titulo, focusTarget: 'category', width:"100%",
	 			hAxis: {
					title: 'Time of Day', format: 'HH:mm', viewWindow: { min: [0, 0, 0], max: [d.getHours(), d.getMinutes(),0] },
					textStyle: { fontSize: 12, color: '#053061', bold: true, italic: false },
					titleTextStyle: { fontSize: 14, color: '#053061', bold: true, italic: false }
				},
				vAxis: { minValue: 0 }
	 		};
			var wrapper = new google.visualization.ChartWrapper({
				chartType: 'ColumnChart', dataTable: new_view, options: options, containerId: id_graph });

	 		return wrapper;
		}

		function toHour(d,r) {
			var x=new Date(d.getValue(r, 0)); var res=[x.getHours(),x.getMinutes(),0];
//			console.log(d.getValue(r, 0),x,res);
			return(res);
		}

 		var graph1_ESP = Crea_Graph_Tiendas_Pais("ESP", "graph1_ESP", "ESPAÑA - Tiendas");
// 		var graph2_ESP = Crea_Graph_WEB_Pais("ESP", "graph2_ESP", "ESPAÑA - WEB");

		var graph1_POR = Crea_Graph_Tiendas_Pais("POR", "graph1_POR", "PORTUGAL - Tiendas");
//		var graph2_POR = Crea_Graph_WEB_Pais("POR", "graph2_POR", "PORTUGAL - WEB");
		
		var graph1_ARG = Crea_Graph_Tiendas_Pais("ARG", "graph1_ARG", "ARGENTINA - Tiendas");
		//var graph2_ARG = Crea_Graph_WEB_Pais("ARG", "graph2_ARG", "ARGENTINA - WEB");
		
		var graph1_BRA = Crea_Graph_Tiendas_Pais("BRA", "graph1_BRA", "BRASIL - Tiendas");
		//var graph2_BRA = Crea_Graph_WEB_Pais("BRA", "graph2_BRA", "BRASIL - WEB");
		
	 	
	 	graph1_ESP.draw();
	 	//graph2_ESP.draw();
	 	graph1_POR.draw();
	 	//graph2_POR.draw();
	 	graph1_ARG.draw();
	 	//graph2_ARG.draw();
	 	graph1_BRA.draw();
	 	//graph2_BRA.draw();

		Activa_Refresco(timeout_historico);
	}

	google.charts.setOnLoadCallback(drawCharts_Historico);	

</script>
