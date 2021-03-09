<title>PELUCHES</title>
<?php
require("./cabecera_vistas.php");

$tmp_peluches_tiendas=myQUERY("select Tienda, sum(Entregados), sum(No_Entregados) from PuntosPeluche where (Entregados+No_Entregados)>0 group by tienda");
$tmp_peluches_diario=myQUERY("select year(Fecha),month(Fecha),day(fecha),sum(Entregados),sum(No_Entregados) from PuntosPeluche group by date(Fecha) order by date(Fecha)");

$tmp_total_peluches_entregados=myQUERY("
select 'CERDITOS',SUM(a.CERD),IFNULL(SUM(b.CERD),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
select 'OVEJITAS',SUM(a.OVEJ),IFNULL(SUM(b.OVEJ),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
select 'VAQUITAS',SUM(a.VACA),IFNULL(SUM(b.VACA),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
select 'BURRITOS',SUM(a.BURR),IFNULL(SUM(b.BURR),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
select 'CABALLITOS',SUM(a.CABA),IFNULL(SUM(b.CABA),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
select 'PERRITOS',SUM(a.PERR),IFNULL(SUM(b.PERR),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;");


$rellena_total_peluches_entregados="";
foreach($tmp_total_peluches_entregados as $k => $d) {
	$rellena_total_peluches_entregados.="['".$d[0]."', ".$d[1].", ".$d[2]."],"; }

// print_r($tmp_peluches_tiendas);

?>

<div class='REDONDO' style='margin-left:3em;'>
	<table style='background-color:white; margin:10px'>
		<tr>
			<td>
				<fieldset class='fieldset_datos'><legend>OPERACIONES DIARIAS</legend>
				<div id='tabla_peluches_diario' style='border: 1px solid #ccc; width:500; height:400'></div>
				</fieldset>
			</td>
			<td>
				<fieldset class='fieldset_datos'><legend>PUNTOS ENTREGADOS</legend>
				<div id='tabla_total_peluches' style='border: 1px solid #ccc; width:500; height:400'></div>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<fieldset class='fieldset_datos'><legend>GRAFICAS</legend>
				<table>
				<tr>
					<td><div id='graph_total_puntos' style='border: 1px solid #ccc; width:800; height:250'></div></td>
					<td><div id='tabla_peluches entregados' style='border: 1px solid #ccc; height:250'></div></td>
				</tr>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
</div>

<script>

	function drawCharts(){
		drawTable();
	}

	function drawTable() {
		var data1 = new google.visualization.DataTable();
		data1.addColumn('date', 'Fecha');
		data1.addColumn('number', 'Entregados');
		data1.addColumn('number', 'No Entregados');
		data1.addRows([
<?php
	foreach($tmp_peluches_diario as $k => $d) {
		echo "[ new Date(".$d[0].",".($d[1]-1).",".$d[2]."), ".$d[3].", ".$d[4]."],";
	};
?>
		]);

		var view1 = new google.visualization.DataView(data1);
		data1.sort([{column: 0, desc: true}]);
		var table1 = new google.visualization.Table(document.getElementById('tabla_peluches_diario'));
		var chart1 = new google.visualization.LineChart(document.getElementById('graph_total_puntos'));

		var data2 = new google.visualization.DataTable();
		data2.addColumn('number', 'Tienda');
		data2.addColumn('number', 'Entregados');
		data2.addColumn('number', 'No Entregados');
		data2.addRows([ <?php foreach($tmp_peluches_tiendas as $k => $d) { echo "[".$d[0].", ".$d[1].", ".$d[2]."],"; } ; ?> ]);
		data2.sort([{column: 1, desc: true}]);
		var view2 = new google.visualization.DataView(data2);
		view2.setRows(view2.getFilteredRows([{column: 2, minValue: 1 }]));
		var table2 = new google.visualization.Table(document.getElementById('tabla_total_peluches'));

		var data3 = new google.visualization.DataTable();
		data3.addColumn('string', 'PELUCHE');
		data3.addColumn('number', 'Entregados');
		data3.addColumn('number', 'Gratis');
		data3.addRows([ <?php echo $rellena_total_peluches_entregados; ?> ]);
		data3.sort([{column: 1, desc: true}]);
		var view3 = new google.visualization.DataView(data3);
		var table3 = new google.visualization.Table(document.getElementById('tabla_peluches entregados'));


		table1.draw(view1, {title:'Operaciones AMAZON',allowHtml: true, width: '100%'});
		table2.draw(view2, {allowHtml: true, width: '100%'});
		chart1.draw(data1, { title: 'Puntos - Ultimos 30 dias', legend: { position: 'none' }, 'width':'100%' });
		table3.draw(view3, {allowHtml: true, width: '100%'});
	}
	google.charts.setOnLoadCallback(drawCharts);


</script>


