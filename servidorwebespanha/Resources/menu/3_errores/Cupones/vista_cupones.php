<?php
require("../../cabecera_vistas.php");
$dir_actual=getcwd();
$local_url=get_url_from_local(__FILE__);
$local_info_url=get_url_from_local($dir_actual."/info_vista_cupones.php");

$array = array(); $subArray=array();
if (!empty($_GET["opcion"])) {
	switch($_GET["opcion"]) {
		case "csv":
			$mes=$_GET["mes"];
			$data=myQUERY("select * from Timeouts_SC_Completo where Fecha like '".$mes."-%' and fecha between '2018-04-01' and now()");
			array_unshift($data,array("Tienda", "Caja", "Fecha", "Comentario"));
			download_send_headers("seguimiento_timeouts.csv");
			echo array2csv($data);
			exit;

		case "total_total":
			$hoy=date("YmdH");
			$data1=myQUERY("select date_format(Fecha,'%Y%m'),count(*) from Timeouts_SC_Completo where fecha between '2018-04-01' and now() group by 1 order by 1");
			$data2=myQUERY("select fecha DIV 100,sum(cantidad) from Oper_SC_Agrup_Diario where fecha DIV 100 between 201804 and ".date("Ym")." group by 1 ");
			foreach($data1 as $k => $d) @$data[$d[0]]["Timeouts"] += $d[1];
			foreach($data2 as $k => $d) @$data[$d[0]]["Total"] += $d[1];
			ksort($data);
			foreach($data as $k => $d) {
				$subArray["Fecha"]=$k;
				$subArray["Timeouts"]=(empty($d["Timeouts"])?0:$d["Timeouts"]);
				$subArray["Oper"]=(empty($d["Total"])?0:$d["Total"]);
				$array[] =  $subArray ;
			}
			break;		

		case "total_por_mes":
			$mes=$_GET["mes"];
			$hoy=date("Ymd");
			$data1=myQUERY("select date_format(Fecha,'%Y%m%d'),count(*) from Timeouts_SC_Completo where date_format(Fecha,'%Y%m') = '".$mes."' and fecha between '2018-04-01' and now() group by 1 order by 1");
			$data2=myQUERY("select fecha,sum(cantidad) from Oper_SC_Agrup_Diario where fecha DIV 100 = $mes and fecha <= ".date("Ymd")." group by 1");
//			$data2=myQUERY("select * from Oper_SC_Diario where round(f/100,0) = $mes and f <= ".date("Ymd"));
			foreach($data1 as $k => $d) @$data[$d[0]]["Timeouts"] += $d[1];
			foreach($data2 as $k => $d) @$data[$d[0]]["Total"] += $d[1];
			ksort($data);
			foreach($data as $k => $d) {
				$subArray["Fecha"]=$k;
				$subArray["Timeouts"]=(empty($d["Timeouts"])?0:$d["Timeouts"]);
				$subArray["Oper"]=(empty($d["Total"])?0:$d["Total"]); 
				$array[] =  $subArray ;
			}
			break;		

		case "total_por_dia_tiendas":
			$dia=$_GET["dia"];
			$data=myQUERY("select date(Fecha),tienda,count(*) from Timeouts_SC_Completo where date(Fecha)='".$dia."' and fecha between '2018-04-01' and now()  group by 1,2 order by 3 desc");
			foreach($data as $k => $d) {
				list($fecha, $tienda,$cantidad)=$d;
				$subArray["Fecha"]=$fecha; $subArray["Tienda"]=$tienda;  $subArray["Timeouts"]=$cantidad; 
				$array[] =  $subArray ;
			}
			break;		

		case "total_por_dia_tienda":
			$dia=$_GET["dia"]; $tienda=$_GET["tienda"];
			$data=myQUERY("select tienda,caja,time(Fecha) from Timeouts_SC_Completo where date(Fecha)='".$dia."' and tienda=".$tienda." and fecha between '2018-04-01' and now() order by caja,fecha asc");
			foreach($data as $k => $d) {
				list($tienda,$caja,$fecha)=$d;
				$subArray["Tienda"]=$tienda; $subArray["Caja"]=$caja;  $subArray["Fecha"]=$fecha; 
				$array[] =  $subArray ;
			}
			break;		

		case "total_por_dia_tienda_agrup":
			$dia=$_GET["dia"];
			if (empty($_GET["tienda"])) {
				$data=myQUERY("select hour(Fecha),count(*) from Timeouts_SC_Completo where date(Fecha)='".$dia."' group by 1");
				for ($i=0; $i<=23; $i++ ) { $horas[$i]=0; }
				foreach($data as $k => $d) {
					list($fecha,$cantidad)=$d;
					$horas[$fecha] += $cantidad;
				}
				$array[]=array("label"=> $dia, "data" => $horas);
			}
			else {
				$data=myQUERY("select caja,hour(Fecha),count(*) from Timeouts_SC_Completo where date(Fecha)='".$dia."' and tienda=".$_GET["tienda"]." group by 1,2");
				foreach($data as $k => $d) {
					list($caja,$fecha,$cantidad)=$d;
					$point[$caja][$fecha] = $cantidad;
				}
			
				foreach($point as $k => $d) {
					$horas=array();
					for ($i=0; $i<=23; $i++ ) { $horas[$i]=0; }
					foreach($d as $k1 => $d1) $horas[$k1]=$d1;
					$array[] = array("label"=> $k, "data" => $horas);
				}
			}
			break;		

		case "total_tienda_mas_timeouts":
			$data=myQUERY("select tienda,count(*) from Timeouts_SC_Completo group by 1 order by 2 desc");
			foreach($data as $k => $d) {
				list($tienda, $cantidad)=$d;
				$subArray["Tienda"]=$tienda; $subArray["Timeouts"]=$cantidad; 
				$array[] =  $subArray ;
			}
			break;		
	}
	echo json_encode($array, JSON_NUMERIC_CHECK);
	exit;
}
require($DOCUMENT_ROOT.$DIR_LIBRERIAS."/chart.php");

?>

<style>
	#i2nfo_timeouts {
			
			text-align: center; font-size: 12;
			background-color: white;
			color: gray;
			height: 50px;
			 
			border:1px solid gray; border-radius: 3px;
	}
	.numero_timeouts {
		display: block;
	   padding: 0 1em 0 1em;
    	font-weight: bold;
    	font-size: 20;
    }
	.nueva_vista { font-family: sans-serif, arial; margin:0px; }
	.nueva_vista CAPTION { background-color: lightcyan; border-radius: 5px 5px 0 0;}
	.nueva_vista CAPTION a { text-decoration:none; font-weight: bold; font-size:110%; }
	.nueva_vista td { text-align:right; border-left: 1px solid #999; padding-right:1em; }
	.nueva_vista .centro { text-align:center; padding:0 0.5em 0 0.5em; }
	.nueva_vista th { border-left: 1px solid #999; padding: 3px; text-align: center;}
	.cesiones:hover { background-color: lightgreen !important; }
	.nueva_vista tr:hover { color: black; }
	.pendientes { background-color: red; color:white; }
	.totales { background-color: honeydew; font-weight: bold; }
	#resultado_vista_general { width:100%; height:90%; max-width:100%; padding:0px; top:50px;}
	#resultado_vista_general .modal-body { margin-top:1em; height:94%; }
	.res_conexion { font-family: sans-serif; text-align: center; margin-bottom: 1em; font-weight: bold;}
	.res_info_tienda { height: 750px !important; overflow-y: auto;}
	.diff_red { background-color: red; color:white;}
	.diff_yellow { background-color: yellow; color:black;}
	.c_red { color: red; }
	.es_vela { background-color: lightblue;}
	#error_no_registros {
		font-family: sans-serif, arial; background-color: floralwhite; height: 100; text-align: center;
    	border: 1px solid gray; margin-top: 1em; border-radius: 3px; font-size: 1.25em;
	}
	#fecha_desde , #fecha_hasta { border: 0; font-family: sans-serif, arial;text-align:center; }
	.info_cesiones label { font-size: 10px; }
	.info_cesiones button { font-size:12px; margin-left.1em; }

	.activo { background-color: lightblue; }	
	#info_total         { width: 100%; }
	#info_total_por_mes { width: 100%; }
	#info_total_por_dia_tiendas { width: 100%; }
	#info_total_por_dia_tiendas { width: 100%; }
	#info_timeouts {
		background-color: white;
		
		font-family: sans-serif, arial;
	}
	#info_timeouts td { vertical-align: top; overflow-y: auto; }
	#info_1 div { height: 500; }
	#info_timeouts table caption {
		background-color: lightcyan;
		border: 1px solid gray;
		border-radius: 2px 2px 0 0;
		font-weight: bold;
	}
	#info_1, #info_2 {  border: 1px solid gray; border-radius: 3px; width: 1200px;}
	.der { text-align: center; }
	#b_descarga_por_mes { font-size: 10; float: right; }
</style>

<table id="info_timeouts">
	<tr>
		<td>
			<table id="info_1">
				<tr>
					<td width="25%"><div id="info_total"></div></td>
					<td width="25%"><div id="info_total_por_mes"></div></td>
					<td width="25%"><div id="info_total_por_dia_tiendas"></div></td>
					<td width="25%"><div id="info_total_por_dia_tienda"></div></td>
				</tr>
			</table>
			<table>
				<tr>
					<td><canvas id="graph_info_total" height="150"></canvas><canvas id="graph_info_total_2" height="200"></canvas></td>
					<td><canvas id="graph_info_total_por_mes" height="150"></canvas><canvas id="graph_info_total_por_mes_2" height="200"></canvas></td>
					<td><canvas id="graph_info_total_por_dia_tienda1" height="150"></canvas><canvas id="graph_info_total_por_dia_tienda1_2" height="200"></td>
					<td><canvas id="graph_info_total_por_dia_tienda2" height="150"></canvas></td>
				</tr>
			</table>
		</td>
	</tr>

</table>



<script>
	var ajax_info_timeouts="<?php echo $local_url; ?>";
	var row_info_total=-1;
	var row_info_mes=-1;
	var myChart1, myChart2, myChart3,myChart4;
	var myChart1_1, myChart2_1, myChart3_1,myChart4_1;
	var chart=[];
	
	var scale_timeouts = { yAxes: [{ scaleLabel: {  display: true, labelString: 'Timeouts' } }]};
	var legend_timeouts = { display: false };
	var labels_horas = ["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23"];

	Chart.defaults.global.animation.duration=0

	function get_info_total() {
		
		$.getJSON(ajax_info_timeouts+"?opcion=total_total", function(json) {
			var table='<table class="tabla2"><caption>Total por mes</caption><thead><tr><th>Fecha</th><th class="der">Timeouts</th><th class="der">Total</th></tr></thead>';
			var labels=[], timeouts=[]; oper=[];
			$.each(json, function(index, item){
				table+='<tr class="row_total_total" title="Pulse en una fila para ver los timeouts del mes"><td><i>'+item.Fecha+'</i></td><td>'+item.Timeouts+'</td><td>'+item.Oper+'</td></tr>';
				labels.push(item.Fecha); timeouts.push(item.Timeouts);
				oper.push(item.Oper);
      	});
      	datasets1=[{ label: "Timeouts", data: timeouts }];
			datasets2=[{ label: "Total", data: oper }];
      	table+='</table>';
      	$("#info_total").html(table);

      	$(".row_total_total").on("click",function () {
      		get_info_mes($(this).find("td:first")[0].textContent);
      		row_info_total=$(this).index();
      		$(".row_total_total").removeClass("activo");
				$(this).addClass("activo");
      	});
      	if (row_info_total>-1) {
      		$("#info_total table tr:nth("+(row_info_total+1)+")").removeClass("activo").addClass("activo");
      	} else {
      		$(".row_total_total:last").click();
      	}
			if (myChart1) { myChart1.destroy(); }
      	myChart1 = new Chart(document.getElementById("graph_info_total").getContext("2d"), {
      		type: "line", data: { labels : labels, datasets : datasets1	},
      		options: { legend: legend_timeouts, title: { display: true, text: 'Timeouts: Información global'}, scales: scale_timeouts }
      	});
			if (myChart1_1) { myChart1_1.destroy(); }
      	myChart1_1 = new Chart(document.getElementById("graph_info_total_2").getContext("2d"), {
      		type: "line", data: { labels : labels, datasets : datasets2	},
      		options: { legend: legend_timeouts, title: { display: true, text: 'Total: Información global'}, scales: scale_timeouts }
      	});

    	});
    	if ($("#info_timeouts").length) window.setTimeout(get_info_total,30000);
	}

	function get_info_mes(mes) {
		
		$.getJSON(ajax_info_timeouts+"?opcion=total_por_mes&mes="+mes, function(json) {
			var table='<table class="tabla2"><caption>Total por dia ('+mes+') <button id="b_descarga_por_mes">Descargar mes</button></caption><thead><tr><th>Fecha</th><th>Timeouts</th><th>Total</th></tr></thead>';
			var labels=[], timeouts=[]; oper=[], datasets=[];
			$.each(json, function(index, item) {
				table+='<tr class="row_total_mes" title="Pulse en una fila para ver los timeouts de las tiendas desglosados por dia"><td><i>'+item.Fecha+'</i></td><td>'+item.Timeouts+'</td><td>'+item.Oper+'</td></tr>';
				labels.push(item.Fecha%100); timeouts.push(item.Timeouts); oper.push(item.Oper);       
      	});
      	
      	table+='</table>';
      	datasets1=[{ label: "Timeouts", data: timeouts }];
      	datasets2=[{ label: "Total", data: oper }];
      	
      	$("#info_total_por_mes").html(table);
      
      	$(".row_total_mes").on("click",function () {
      		get_info_dia_tiendas($(this).find("td:first")[0].textContent);
      		$(".row_total_mes").removeClass("activo");
      		$(this).addClass("activo");
      	});
      	$(".row_total_mes:last").click();
		
			$("#b_descarga_por_mes").on("click",function () {
				window.open("<?php echo $local_url.'?opcion=csv'; ?>"+"&mes="+mes);
			});
	
			if (myChart2) { myChart2.destroy(); }
			myChart2 = new Chart(document.getElementById("graph_info_total_por_mes").getContext("2d"), {
      		type: "line", data: { labels : labels, datasets : datasets1 },
      		options: { legend: legend_timeouts, title: { display: true, text: 'Timeouts: Información mensual ('+mes+')'}, scales: scale_timeouts }
      	});

			if (myChart2_1) { myChart2_1.destroy(); }
			myChart2_1 = new Chart(document.getElementById("graph_info_total_por_mes_2").getContext("2d"), {
      		type: "line", data: { labels : labels, datasets : datasets2 },
      		options: { legend: legend_timeouts, title: { display: true, text: 'TOTAL: Información mensual ('+mes+')'}, scales: scale_timeouts }
      	});

    	});
	}

	function pinta_graph_horas(id, title, json ) {
		var data1 = { labels: labels_horas, datasets:[]};
		$.each(json, function(index, item){ data1.datasets.push({label: item.label, data:item.data}); });
		if (chart[id]) { chart[id].destroy(); }
		chart[id] = new Chart(document.getElementById(id).getContext("2d"), {
			type: "line", data: data1,
			options: { legend: legend_timeouts, title: { display: true, text: title }, scales: scale_timeouts }
		});
	}


	function get_info_dia_tiendas(dia) {
		$.getJSON(ajax_info_timeouts+"?opcion=total_por_dia_tiendas&dia="+dia, function(json) {
			var table='<table class="tabla2"><caption>Total por dia y tienda ('+dia+')</caption><thead><tr><th>Fecha</th><th>Tienda</th><th>Timeouts</th></tr></thead>';
			$.each(json, function(index, item){
				table+='<tr class="row_total_dia_tiendas" title="Pulse en una fila para ver los timeouts de la tienda por horas"><td><i>'+item.Fecha+'</i></td><td>'+item.Tienda+'</td><td>'+item.Timeouts+'</td></tr>';
      	});
      	table+='</table>';
      	$("#info_total_por_dia_tiendas").html(table);
      	$(".row_total_dia_tiendas").on("click",function () {
      		get_info_dia_tienda($(this).find("td:first")[0].textContent, $(this).find("td:nth(1)")[0].textContent);
      		$(".row_total_dia_tiendas").removeClass("activo");
      		$(this).addClass("activo");
      	});
      	$(".row_total_dia_tiendas:nth(0)").click();
    	});
		$.getJSON(ajax_info_timeouts+"?opcion=total_por_dia_tienda_agrup&dia="+dia, function(json) {
			pinta_graph_horas("graph_info_total_por_dia_tienda1", 'Timeouts: Información diaria ('+dia+')', json);
		});
	}

	function get_info_dia_tienda(dia,tienda) {
		$.getJSON(ajax_info_timeouts+"?opcion=total_por_dia_tienda&dia="+dia+"&tienda="+tienda, function(json) {
			var table='<table class="tabla2"><caption>Total por tienda ('+tienda+') y dia ('+dia+')</caption><thead><tr><th>Tienda</th><th>Caja</th><th>Fecha</th></tr></thead>';
			$.each(json, function(index, item){
				table+='<tr class="row_total_dia_tiendas"><td><i>'+item.Tienda+'</i></td><td><i>'+item.Caja+'</i></td><td>'+item.Fecha+'</td></tr>';      
      	});
      	table+='</table>';
      	$("#info_total_por_dia_tienda").html(table);
      });	
      $.getJSON(ajax_info_timeouts+"?opcion=total_por_dia_tienda_agrup&dia="+dia+"&tienda="+tienda, function(json) {
      	pinta_graph_horas("graph_info_total_por_dia_tienda2", 'Información tienda ('+tienda+') del dia ('+dia+')', json);
    	});
	}
	
	$(document).ready(function () {
		get_info_total();
	});
	
</script>