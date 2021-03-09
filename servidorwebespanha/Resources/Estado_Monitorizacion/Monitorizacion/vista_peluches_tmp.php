<title>PELUCHES</title>
<?php
require("./cabecera_vistas.php");

// $tmp_peluches_tiendas=myQUERY("
// 	select
// 		  pp.Tienda
// 		, SUM(pp.Entregados) as suma_entregados
// 		, SUM(pp.No_Entregados)  as suma_no_entregados
// 		, ROUND(SUM(pp.Entregados)/(SUM(pp.Entregados)+SUM(pp.No_Entregados))*100,1)
// 		, ROUND(SUM(pp.No_Entregados)/(SUM(pp.Entregados)+SUM(pp.No_Entregados))*100,1)
// 		, IFNULL((select SUM(pe.cerd+pe.ovej+pe.vaca+pe.burr+pe.caba) from PeluchesEntregados pe where pe.tienda=pp.tienda and pe.fecha=pp.fecha),0) as suma_peluches
// 		, IFNULL(SUM(peg.cerd+peg.ovej+peg.vaca+peg.burr+peg.caba),0) as suma_peluches_gratis
// 		, date(pp.Fecha)
// 	from PuntosPeluche pp
// 		/* left join PeluchesEntregados pe on pp.tienda=pe.tienda */
// 		left join PeluchesEntregadosGratis peg on pp.tienda=peg.tienda
// 	group by pp.tienda,pp.Fecha
// 	having
// 		   (suma_entregados+suma_no_entregados)>0
// 		or suma_peluches>0
// 		or suma_peluches_gratis>0
// 	",true);
// $rellena_tiendas="";
// foreach($tmp_peluches_tiendas as $k => $d) {
// 	$rellena_tiendas.="[".$d[0].", ".$d[1].", ".$d[2].", ".$d[3].", ".$d[4].", ".$d[5].", ".$d[6].", '".$d[7]."'],";
// }
// 
// $tmp_peluches_diario=myQUERY("
// 	select
// 		 year(Fecha)
// 		,month(Fecha)
// 		,day(fecha)
// 		,sum(Entregados)
// 		,sum(No_Entregados)
// 	from PuntosPeluche
// 	group by date(Fecha)
// 	order by date(Fecha)");
// 
// $tmp_total_peluches_entregados=myQUERY("
// select 'CERDITOS',SUM(a.CERD),IFNULL(SUM(b.CERD),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
// select 'OVEJITAS',SUM(a.OVEJ),IFNULL(SUM(b.OVEJ),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
// select 'VAQUITAS',SUM(a.VACA),IFNULL(SUM(b.VACA),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
// select 'BURRITOS',SUM(a.BURR),IFNULL(SUM(b.BURR),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
// select 'CABALLITOS',SUM(a.CABA),IFNULL(SUM(b.CABA),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;
// select 'PERRITOS',SUM(a.PERR),IFNULL(SUM(b.PERR),0) from PeluchesEntregados a LEFT JOIN PeluchesEntregadosGratis b ON a.Tienda=b.Tienda;");


// $rellena_total_peluches_entregados="";
// foreach($tmp_total_peluches_entregados as $k => $d) {
// 	$rellena_total_peluches_entregados.="['".$d[0]."', ".$d[1].", ".$d[2]."],"; }

// print_r($tmp_peluches_tiendas);

?>

<div class='REDONDO' style='margin-left:3em;'>
	<table style='background-color:white; margin:10px'>
		<tr>
			<td>
				<fieldset class='fieldset_datos'><legend>INFORMACION POR TIENDA</legend>
				<div id='tabla_total_peluches' style='border: 1px solid #ccc; height:400'></div>
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
			<td >
				<fieldset class='fieldset_datos'><legend>OPERACIONES DIARIAS</legend>
				<div id='tabla_peluches_diario' style='border: 1px solid #ccc; width:500; height:400'></div>
				</fieldset>
			</td>

		</tr>
	</table>
</div>

<script>

	function drawCharts(){
		drawTable();
	}

	function CreateTable() {
		$.getJSON('json_peluches.php', function(json) {
			var tabla_peluches = new google.visualization.DataTable(json);
		});
	}

	function drawTable() {
		var data1 = new google.visualization.DataTable();
		data1.addColumn('date', 'Fecha');
		data1.addColumn('number', 'Entregados');
		data1.addColumn('number', 'No Entregados');
		data1.addRows([
<?php
	foreach($tmp_peluches_diario as $k => $d) {
		echo "[ new Date(".$d[0].",".$d[1].",".$d[2]."), ".$d[3].", ".$d[4]."],";
	};
?>
		]);

		var view1 = new google.visualization.DataView(data1);
		data1.sort([{column: 0, desc: true}]);
		var table1 = new google.visualization.Table(document.getElementById('tabla_peluches_diario'));
		var chart1 = new google.visualization.LineChart(document.getElementById('graph_total_puntos'));

// 		var tabla_peluches = new google.visualization.DataTable();


		var jsonData = $.ajax({
              url: "./Monitorizacion/json_peluches.php",
              dataType: "json",
              async: false
          }).responseText;
          var array  = JSON.parse(jsonData);

// 		$.getJSON('./Monitorizacion/json_peluches.php', function(json) {
		var tabla_peluches = new google.visualization.arrayToDataTable(array);
// 		});
		tabla_peluches.addColumn('number', 'Tienda');
		tabla_peluches.addColumn('number', 'Entregados');
		tabla_peluches.addColumn('number', 'No Entregados');
		tabla_peluches.addColumn('number', '%Entregados');
		tabla_peluches.addColumn('number', '%No Entregados');
		tabla_peluches.addColumn('number', 'Peluches Pagados');
		tabla_peluches.addColumn('number', 'peluches Gratis');
		tabla_peluches.addColumn('string', 'Fecha');


// 		tabla_peluches.addRows([ <?php  echo $rellena_tiendas; ?> ]);

		tabla_peluches.sort([{column: 1, desc: true}]);

		var view2 = new google.visualization.DataView(tabla_peluches);
		view2.setRows(view2.getFilteredRows([{column: 2, minValue: 1 }]));
// 		view2.hideColumns([7]);
		var table2 = new google.visualization.Table(document.getElementById('tabla_total_peluches'));

		var view_peluches_total = new google.visualization.DataView(tabla_peluches);

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


