<title>AMAZON</title>
<?php
require("./cabecera_vistas.php");

function get_info_dron($file,$con_total=true) {
	if (!file_exists($file))
		die("ERROR: No existe el fichero $file");
	$tmp=explode("\n",file_get_contents($file));
	$lista_tmp="";
	foreach($tmp as $k => $d) {
		if ($d!="") {
			$total=0; $lista_tmp.= "[";
			foreach(explode(',',$d) as $k1 => $d1) {
				$lista_tmp.=$d1.", "; 
				$total+=$d1;
			}
			if ($con_total) $lista_tmp.=$total;
			$lista_tmp.="],";
		}
	}
	return ($lista_tmp);
}

$DIR_AMAZON="/home/soporteweb/tools/proy_dron/";
$tmp=file_get_contents($DIR_AMAZON."/lista_tiendas_amazon.dat");
$lista_tiendas_amazon=explode("\n",$tmp);
$lista_tmp_pedi_diar="['Fecha'"; foreach($lista_tiendas_amazon as $k => $d) if ($d) $lista_tmp_pedi_diar.=",'T.$d'"; 
//$lista_tmp_pedi_diar.=",'TOTAL'],".get_info_dron($DIR_AMAZON."/tmp_pedi_diar.dat",false);
$lista_tmp_pedi_diar.="],".get_info_dron($DIR_AMAZON."/tmp_pedi_diar.dat",false);

$lista_tmp_pedi_diar_graph=get_info_dron($DIR_AMAZON."tmp_pedi_diar_graph.dat",false);
$lista_tmp_pedi_hora_graph=get_info_dron($DIR_AMAZON."tmp_pedi_hora_graph.dat",false);
$lista_tmp_pedi_diar_arti =get_info_dron($DIR_AMAZON."tmp_pedi_diar_arti.dat",false);

/*
$tmp_pedi_hora_graph=myQUERY("
	select
		  date(CONFIRM_DATE_TIME)
		, date_format(CONFIRM_DATE_TIME,'%H:00')
		, SUM(CASE WHEN Tienda=52524 THEN 1 ELSE 0 END) 'TDA 52524'
		, SUM(CASE WHEN Tienda=52549 THEN 1 ELSE 0 END) 'TDA 52549'
	from AMAZON_ORDER_SALE_ONLINE_SERVER 
	where CONFIRM_DATE_TIME >= NOW() - INTERVAL 48 HOUR 
	group by 1,2 
	order by 1,2");
*/

$div_articulos="
<fieldset class='fieldset_datos'><legend>TOP 18 ARTICULOS</legend>
	<div id='tabla_articulos_total' style='border: 1px solid #ccc; width:700; height:400'></div>
</fieldset>
";


?>
<style>
	#v_general { background-color: white; width: 1200px; height: 800px;}

	.pestania {
		border:1px solid gray;
		margin-top: 51px;
		margin:4px;
		width: 100%; height: 100%;
		vertical-align: top;
		background-color: white; 
	}
	
	.titulo_pestania {
		margin-top: 6px;
		border:1px solid gray;
		border-radius: 3px 3px 0 0;
		font: 12px Arial;
		height: 25px;
		padding: 2px 7 5px 7;
		background-color: whitesmoke;	
	}
	.activa_pestania {
		background-color: white;
		border-bottom: 1px solid white;	
	}
	.titulo_pestania:hover { background-color: white; cursor: pointer;}

</style>

<div id="v_general">
	<span asociado="vista_global"    class="titulo_pestania activa_pestania" style="margin-left:5px;">Vista Global</span>
	<span asociado="vista_articulos" class="titulo_pestania">Vista Articulos</span>

	<div class="pestania">
		<div id="vista_global" class="v_pestania">	
			<table>
				<tr><td> <div id='tabla_pedidos_total' style='border: 1px solid #ccc; height:400'></div> </td></tr>
				<tr>
					<td>
						<table><tr>
							<td><div id='graph_total_pedidos' style='border: 1px solid #ccc; width:550; height:250'></div></td>
							<td></td>
							<td><div id='graph_total_horas' style='border: 1px solid #ccc; width:550; height:250'></div></td>
						</tr></table>
					</td>
				</tr>
			</table>
		</div>
		
		<div id="vista_articulos"  class="v_pestania" style="display:none">					
			KKSDKJSKJSKDJ
		</div>
	</div>
	
</div>

<script>

	function drawCharts(){
		getData();
		drawChart();
		drawTable();
	}

	function CambiaVista() {
		var vista_actual=$("#select_vista").val();
		$("#vista_actual").html(vista_actual);
		switch(vista_actual) {
			case "Vista Global":
				$("#vista_global").show(); $("#vista_articulos").hide();
				break;
			case "Vista Articulos":
				$("#vista_global").hide(); $("#vista_articulos").show();
				break;
		}
	}
	
	$("#select_vista").change(CambiaVista);

	$(".titulo_pestania").on("click",function () {
		$(".titulo_pestania").removeClass("activa_pestania");
		$(".v_pestania").hide();
		$("#"+$(this).attr("asociado")).show();
		$(this).addClass("activa_pestania");
	});
	

	var data1, data2, data3, data4;

	function getData() {
		data1 = new google.visualization.arrayToDataTable([ <?php echo $lista_tmp_pedi_diar; ?> ]);
/*
		data2 = new google.visualization.DataTable();
			data2.addColumn('number', 'Articulo');
			data2.addColumn('string', 'Descripcion');
			data2.addColumn('number', '52524 H');
			data2.addColumn('number', '52549 H');
			data2.addColumn('number', '955 H');
			data2.addColumn('number', 'Total H');
			data2.addColumn('number', '52524 T');
			data2.addColumn('number', '52549 T');
			data2.addColumn('number', '955 T');
			data2.addColumn('number', 'T. Oper.');
			data2.addRows([<?php echo $lista_tmp_pedi_diar_arti; ?> ]);

		data3 = google.visualization.arrayToDataTable([ <?php echo $lista_tmp_pedi_diar_graph;  ?> ]);
		data4 = google.visualization.arrayToDataTable([ ['Hora','T52524','T52549','T955'], <?php echo $lista_tmp_pedi_hora_graph; ?>]);		
*/
	}

	function drawDashboard() {
		var dashboard = new google.visualization.Dashboard(document.getElementById('tabla_pedidos_total'));
		var donutRangeSlider = new google.visualization.ControlWrapper({
			'controlType': 'NumberRangeFilter',
			'containerId': 'filter_div',
			'options': {
				'filterColumnLabel': 'Donuts eaten'
			}
		});	
	}

	function drawTable() {
		var table1 = new google.visualization.LineChart(document.getElementById('tabla_pedidos_total'));
		table1.draw(data1, {
				title:'Operaciones AMAZON',
				allowHtml: true,
				width: '100%',
				'focusTarget': 'category',
				});
		}

	function drawTable_old() {
		var table1 = new google.visualization.Table(document.getElementById('tabla_pedidos_total'));
		var formatter1 = new google.visualization.BarFormat({width: 100, showValue: true});
		formatter1.format(data1, 1);
		formatter1.format(data1, 2);
		formatter1.format(data1, 3);
		formatter1.format(data1, 4);
		formatter1.format(data1, 5);
		table1.draw(data1, {title:'Operaciones AMAZON',allowHtml: true, width: '100%'});

		var table2 = new google.visualization.Table(document.getElementById('tabla_articulos_total'));
		table2.draw(data2, {title:'TOP 18 articulos',allowHtml: true, width: '100%'});
	}

	function drawChart() {
		var options1 = { title: 'Pedidos AMAZON - Ultimos 30 dias', legend: { position: 'top' }, 'width':'100%', isStacked: true, 'focusTarget': 'category' };
		var chart1 = new google.visualization.ColumnChart(document.getElementById('graph_total_pedidos'));
		chart1.draw(data3, options1);

		var options2 = { title: 'Pedidos AMAZON - Ultimas 48 horas', legend: { position: 'top' },'width': '100%', isStacked: true, 'focusTarget': 'category' };
		var chart2 = new google.visualization.ColumnChart(document.getElementById('graph_total_horas'));
		chart2.draw(data4, options2);
      }
	google.charts.setOnLoadCallback(drawCharts);

</script>


