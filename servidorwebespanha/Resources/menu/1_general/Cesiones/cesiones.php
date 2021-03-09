<title>CESIONES ONLINE</title>
<?php
require_once('/home/soporteweb/config.php');
require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/library/google_charts.php');
require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/library/jquery.php');
?>

<style>
	.t_cesiones {
		height: 600px !important;
		overflow-y: auto;
		width: 300px;
	}
	.tabla_1 { padding: 1em; border:1px solid blue; border-collapse: collapse; }
	#top_10 , #graph_cesiones_tienda { height: 300px;}
</style>

<div id="v_general">
	<table class="tabla_1">
		<tr>
			<td><div class="t_cesiones" id='tabla_cesiones_total'  ></div></td>
			<td>
				<div class="" id="top_10"></div>
				<div class="" id="graph_cesiones_tienda"></div>
			</td>
		</tr>
	</table>
</div>

<script>

	function drawCharts(){
//		getData();
//		drawChart();
		drawTable1();
	}

	var listado_tiendas, grouped_data;
	var table1, table2;
	var detalle_tienda;
	var selectedItem;

	function StrtoDate(dateTable, row) {
		const newFecha=new Date(dateTable.getValue(row, 1).replace(/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, "$1-$2-$3"))
		return newFecha;
	}

  function visualiza_datos_tienda() {
    selectedItem = table1.getSelection()[0];
    if (selectedItem) {
    	var tienda = grouped_data.getValue(selectedItem.row, 0);
    	var jsonData = $.ajax({ url: "json_get_cesiones.php?opcion=detalles_tienda&tienda="+tienda, dataType: "json", async: false }).responseText;
    	detalle_tienda=new google.visualization.DataTable(jsonData);
    	detalle_tienda.sort([{column:1, desc: true}]);

		var graph_detalle_tienda=new google.visualization.DataView(detalle_tienda);
		graph_detalle_tienda.setColumns([1,2]);

		graph_cesiones_tienda = new google.visualization.ColumnChart(document.getElementById('graph_cesiones_tienda'));
		graph_cesiones_tienda.draw(graph_detalle_tienda, { title:"Datos de cesiones de la tienda "+tienda,
			width:"500px", legend: 'top', hAxis: { textStyle: { fontName: 'Arial', fontSize: '10' } }, vAxis: { textStyle: { fontName: 'Arial', fontSize: '10' } }  });
	}
  }

	function drawTable1() {
		if (selectedItem) { var oldselectedItem = table1.getSelection()[0]; }
		var jsonData = $.ajax({ url: "json_get_cesiones.php?opcion=agrupado", dataType: "json", async: false }).responseText;
		grouped_data = new google.visualization.DataTable(jsonData);
//		grouped_data.sort([{column: 0}]);

		table1 = new google.visualization.Table(document.getElementById('tabla_cesiones_total'));
		table1.draw(grouped_data, { title: 'CESIONES POR TIENDA'  } );
		google.visualization.events.addListener(table1, 'select', visualiza_datos_tienda);

		var view_top=new google.visualization.DataView(grouped_data);
		var tmp_rows=grouped_data.getSortedRows({column: 1, desc: true});
		view_top.setRows(tmp_rows);
		console.log(view_top);
		view_top.setColumns([ {column:0, calc:function (t,row) { return t.getValue(row,0) + " ("+t.getValue(row,2)+")"; }, type:"string" },1 ]);
		view_top.hideRows(10,62);
		console.log(view_top);

		var graph_top = new google.visualization.BarChart(document.getElementById('top_10'));
		graph_top.draw(view_top, { title:"TOP 10 tiendas con más cesiones",
			chartArea: { width: "50%"},
			width:"500x", legend: 'top', hAxis: { textStyle: { fontName: 'Arial', fontSize: '10' } }, vAxis: { textStyle: { fontName: 'Arial', fontSize: '10' } } });

		if (oldselectedItem) { visualiza_datos_tienda(); }
	}

	google.charts.setOnLoadCallback(drawCharts);
		

</script>


