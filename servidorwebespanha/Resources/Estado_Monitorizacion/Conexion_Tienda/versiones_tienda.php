<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
$Tienda=$_GET['Tienda'];
$Centro=urldecode($_GET['Centro']);

if ($Centro == "SEDE") {
	echo "<div style='padding:3em; text-align:center;'><b>Cronograma de versiones de todas las TPVs</b><h3>DATOS NO DISPONIBLES PARA TIENDAS DE PRUEBA</h3></div>";
	exit;
}

$tmp=myQUERY("select DATE_FORMAT(NOW(),'new Date(%Y,%m-1,%d,%H,%i,%S)')");
$Now=$tmp[0][0];

$tmp=myQUERY("select count(distinct(caja)) from Versiones_TPV where tienda=$Tienda");
if ($tmp[0][0] == 0) {
	echo "<div style='padding:3em; text-align:center;'><b>Cronograma de versiones de todas las TPVs</b><h3>NO HAY DATOS DISPONIBLES PARA LA TIENDA ".$Tienda."</h3></div>";
	exit;
} else {
	for ($i=1; $i<=$tmp[0][0]; $i++) {
		$tmp1=myQUERY("select caja,version,concat('prueba '),DATE_FORMAT(Fecha,'new Date(%Y,%m-1,%d,%H,%i,%S)') from Versiones_TPV where caja=$i and tienda=".$Tienda." group by Version ORDER BY Caja,Version");
		$d_ante=null;
		if (count($tmp1) > 0) {
			foreach($tmp1 as $d) {
				if ($d_ante) {
					if ($d_ante[3] > $d[3]) $d[3]=$d_ante[3];
					$versiones[]=array_merge($d_ante,array($d[3]));
				}
				$d_ante=$d;
			}
			$versiones[]=array_merge($d_ante,array($Now));
		} else {
			echo "<div style='padding:3em; text-align:center;'><b>Cronograma de versiones de todas las TPVs</b><h3>NO HAY DATOS DISPONIBLES PARA LA TIENDA ".$Tienda."</h3></div>";
			exit;
		}
	}
}
?>

<div style="height:95%; background-color:white; text-aling:center">
	<b>Cronograma de versiones de todas las TPVs</b>
	<div id="example4.2" style="height:95%; overflow:auto;"></div>
</div>

<script>
	function setTooltipContent(dataTable,row) {
		if (row != null) {
			var Caja = dataTable.getValue(row, 0);
			var Version = dataTable.getValue(row, 1);
			var Fecha_Inicio = dataTable.getValue(row, 2);
			var Fecha_Fin = dataTable.getValue(row, 3);
			var content = '<div><b>'+Caja+'</b><hr>' + Version + '<hr><b>Fecha inicio:</b> '+Fecha_Inicio+'<br><b>Fecha Fin:</b> '+Fecha_Fin+'</div>';
			var tooltip = document.getElementsByClassName("google-visualization-tooltip")[0];
			tooltip.innerHTML = content;
		}
	}

	google.charts.setOnLoadCallback(drawChartCronogram);

	function drawChartCronogram() {
		var container = document.getElementById('example4.2');
		var chart = new google.visualization.Timeline(container);
		var dataTable = new google.visualization.DataTable();

		dataTable.addColumn({ type: 'string', id: 'Caja' });
		dataTable.addColumn({ type: 'string', id: 'Version' });
		dataTable.addColumn({ type: 'date', id: 'Start' });
		dataTable.addColumn({ type: 'date', id: 'End' });
		dataTable.addRows([
			<?php
				$Fech_Fin="";
				foreach($versiones as $k => $d) {
					list($caja, $version, $tooltip, $start, $end)=$d;
					echo PHP_EOL."[ 'Caja ".$caja."','".$version."',".$start.",".$end." ],";
				}
			?>
		]);

	dataTable.insertColumn(2, {type: 'string', role: 'tooltip', p: {html: true}});


    var dateFormat = new google.visualization.DateFormat({
      pattern: 'dd/MM/yyyy HH:mm'
    });

    for (var i = 0; i < dataTable.getNumberOfRows(); i++) {
      var tooltip = '<div class="ggl-tooltip"><b>' + dataTable.getValue(i, 0) + '</b></div><hr>' +
      	'<div class="ggl-tooltip"><b>' + dataTable.getValue(i, 1) + '</b></div><hr>' +
      	'<div class="ggl-tooltip"><b>Inicio: </b>'+ dateFormat.formatValue(dataTable.getValue(i, 3)) + '<br>' +
      	'<b>Fin:</b> '+dateFormat.formatValue(dataTable.getValue(i, 4)) + '</div>';

      dataTable.setValue(i, 2, tooltip);
    }

		var options = {
			explorer: {axis: 'horizontal'},
			height:'95%',
			'width': dataTable.getNumberOfRows() * 50,
			viewWindowMode: 'explicit',
			
			tooltip: { isHtml: true}
		};

		chart.draw(dataTable, options);
		google.visualization.events.addListener(chart, 'error', function () {
			google.visualization.errors.removeAll(container);
		});
	}
	
   
</script>
