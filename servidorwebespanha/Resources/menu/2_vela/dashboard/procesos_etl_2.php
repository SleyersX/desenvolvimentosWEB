<?php

require("./comun_dashboard.php");
$DIR_DATOS="/home/soporteweb/tools/VELA/datos/";

$array_critical=array(
	"CountManagement.CreateAutomaticLists"
);

$tiendas_total=array();
$tmp=file_get_contents($DIR_DATOS."listado_tiendas_total_vela.dat");
foreach(explode("\n",$tmp) as $k => $d)
	if ($d) {
		list($bu,$code,$ver)=explode(",",str_replace("\"","",$d));
		if ($ver=="E") $tiendas_total[$code]=$bu;
	}
$count_tiendas_total=count($tiendas_total);

$json=file_get_contents($DIR_DATOS."start_day_shops_ko.dat");
$tmp=json_decode($json);
$count_tiendas_ko=$tmp->hits->total;


	function get_json_etl_historico($fichero) {
		$json=file_get_contents($fichero);
		$tmp=json_decode($json);
		$lista=array();
		foreach ($tmp->aggregations->historico->buckets as $k => $d) $lista[date("Y/m/d",strtotime($d->key_as_string))]=$d->doc_count;
		return $lista;
	}

	function get_json_etl_all($fichero) {
		return json_decode(file_get_contents($fichero));;
	}


	function busca_en_array($array, $idx) {
		if (array_key_exists($idx, $array))
			return $array[$idx];
		else
			return 0;
	}

$lista_start_day_ok=get_json_etl_historico($DIR_DATOS."start_day_shops_ok_historico.dat");
$lista_start_day_ko=get_json_etl_historico($DIR_DATOS."start_day_shops_ko_historico.dat");
$lista_end_day_ok=get_json_etl_historico($DIR_DATOS."end_day_shops_ok_historico.dat");
$lista_end_day_ko=get_json_etl_historico($DIR_DATOS."end_day_shops_ko_historico.dat");
$lista_etl_post_ok=get_json_etl_historico($DIR_DATOS."etl_post_shops_ok_historico.dat");
$lista_etl_post_ko=get_json_etl_historico($DIR_DATOS."etl_post_shops_ko_historico.dat");

$lista_dias=array();

$t_ko_jobs=array();
$tmp=get_json_etl_all("/home/soporteweb/tools/VELA/datos/start_day_shops_ko_jobs.dat");
foreach($tmp->hits->hits as $k => $d) {
	if ( $d->_source->cmap->STORE_CODE < 98000) {
		$t_ko_jobs[$d->_source->cmap->STORE_CODE]=array( "SERVICE" => $d->_source->cmap->SERVICE, "JOB" => $d->_source->cmap->JOB);
//		echo "<pre>"; print_r($d); echo "</pre>";
	}
}

$tiendas_ko_jobs='["Tienda","Servicio","Job"],';
foreach($t_ko_jobs as $tienda => $d) {
	if (in_array($d["SERVICE"], $array_critical))
		$tiendas_ko_jobs.=" [ ".$tienda.",'".$d["SERVICE"]."','".$d["JOB"]."'],";
}

$hace_un_mes=mktime(0, 0, 0, date("m")-1, date("d"));
$hoy=mktime(0, 0, 0, date("m"), date("d"));
for($i=30; $i>=0; $i--) {
	$lista_dias[]=date("Y/m/d",mktime(0, 0, date("Y"), date("m"), date("d")-$i));
}

	$data_start_day="['Fecha','Inicio.Dia OK','Inicio.Dia KO'],";
	$data_end_day="['Fecha','FIN.Dia OK','FIN.Dia KO'],";
	$data_etl_post="['Fecha','POST ETL OK','POST ETL KO'],";
	foreach($lista_dias as $k => $d) {
		$data_start_day.="['".$d."',".(busca_en_array($lista_start_day_ok,$d)).",".(busca_en_array($lista_start_day_ko,$d))."],";
		$data_end_day.="['".$d."',".(busca_en_array($lista_end_day_ok,$d)).",".(busca_en_array($lista_end_day_ko,$d))."],";
		$data_etl_post.="['".$d."',".(busca_en_array($lista_etl_post_ok,$d)).",".(busca_en_array($lista_etl_post_ko,$d))."],";
//		$ultima_fecha=$k; $ultimo_ok=$d; $ultimo_ko=$lista_start_day_ko[$k]; 
	}
?>
<style>
	.info_procesos {
		border:1px solid black;
		border-radius: 3px;
		font-size: 110%; text-align: center;
		background: linear-gradient(#eeefef, #ffffff 20%);
		padding: 5px; height: 150px; width: 100%;
	}
	.info_procesos td { vertical-align: center; }
	.info_procesos:hover { background: linear-gradient(#ffffff,#eeefef 20%); }
	.grande {
		text-align: center; margin-top: 20px;
		font-size: 24px; color:gray;
	}
	.normal { font-size: 10px; display: block;}
	.titulo { font-size: 18px; display: block;}
	.tiendas_ko { font-weight:bold; }
</style>
<body>
	<div>
		<table style="width:100%; height:100%">
			<tr style="border:1px solid gray;">
				<td width="33%">
					<table class="info_procesos" title="Pulse aquí para ver desglose de JOBs KO" >
						<tr><td colspan="2"><span class="titulo">Inicios de día</span></td></tr>
						<tr><td colspan="2"><span class="normal">Tiendas actuales: </span><b class="grande"><?php echo $count_tiendas_total; ?></b></td></tr>
						<tr>
							<td><b class="grande tiendas_ok" id="start_day_ok"></b><span class="normal">(OK)</span></td>
							<td><b class="grande tiendas_ko" id="start_day_ko"></b><span class="normal">(KO)</span></td>
						</tr>
					</table>
				</td>
				<td width="33%">
					<table class="info_procesos" title="Pulse aquí para ver desglose de JOBs KO" >
						<tr><td colspan="2"><span class="titulo">Fines de día</span></td></tr>
						<tr><td colspan="2"><span class="normal">Tiendas actuales: </span><b class="grande"><?php echo $count_tiendas_total; ?></b></td></tr>
						<tr>
							<td><b class="grande tiendas_ok" id="end_day_ok"></b><span class="normal">(OK)</span></td>
							<td><b class="grande tiendas_ko" id="end_day_ko"></b><span class="normal">(KO)</span></td>
						</tr>
					</table>
				</td>
				<td width="33%">
					<table class="info_procesos" title="Pulse aquí para ver desglose de JOBs KO">
						<tr><td colspan="2"><span class="titulo">Proc. POST-ETL</span></td></tr>
						<tr><td colspan="2"><span class="normal">Tiendas actuales: </span><b class="grande"><?php echo $count_tiendas_total; ?></b></td></tr>
						<tr>
							<td><b class="grande tiendas_ok" id="etl_ok"></b><span class="normal">(OK)</span></td>
							<td><b class="grande tiendas_ko" id="etl_ko"></b><span class="normal">(KO)</span></td>
						</tr>
					</table>
				</td>

			</tr>
			<tr class="graphs_etl"><td colspan="3"><div id='graph_total_historico_inicio_dia'></div></td></tr>
			<tr class="graphs_etl"><td colspan="3"><div id='graph_total_historico_fin_dia'></div></td></tr>
			<tr class="graphs_etl"><td colspan="3"><div id='graph_total_historico_etl_post'></div></td></tr>
			
			<tr class="jobs_etl" style="display:none"><td colspan="3"><div id='tabla_ko_jobs'></div></td></tr>
		</table>
	</div>
</body>

<script>
	var data1_etl,data2_etl,data3_etl;
	var lista_jobs_ko;
	
	function drawCharts_etl(){
		getData_etl();
		drawChart_etl();
		pinta_resultados_hoy();
	}

	function pon_rojo(x) {
		if (x.text() == "0")
			x.css("color","red");
	}	
	
	function pinta_resultados_hoy() {
		$("#start_day_ok").html(data1_etl.og[data1_etl.getNumberOfRows()-1].c[1].v+0);
		$("#start_day_ko").html(data1_etl.og[data1_etl.getNumberOfRows()-1].c[2].v+0);
		$("#end_day_ok").html(data2_etl.og[data2_etl.getNumberOfRows()-1].c[1].v+0);
		$("#end_day_ko").html(data2_etl.og[data2_etl.getNumberOfRows()-1].c[2].v+0);
		$("#etl_ok").html(data3_etl.og[data3_etl.getNumberOfRows()-1].c[1].v+0);
		$("#etl_ko").html(data3_etl.og[data3_etl.getNumberOfRows()-1].c[2].v+0);
		$(".tiendas_ko").css("color",function () {
			if ($(this).text() != "0") return "red";
		})
		$(".tiendas_ok").css("color",function () {
			if ($(this).text() == "0") return "red";
		})		
		
	}	
	
	function getData_etl() {
		data1_etl = new google.visualization.arrayToDataTable([<?php echo $data_start_day;?>]);
		data2_etl = new google.visualization.arrayToDataTable([<?php echo $data_end_day; ?>]);
		data3_etl = new google.visualization.arrayToDataTable([<?php echo $data_etl_post;?>]);
		lista_ko_jobs = new google.visualization.arrayToDataTable([<?php echo $tiendas_ko_jobs; ?>]);
	}
	function drawChart_etl() {
		options1 = { title: 'Historico inicios dia - Ultimos 30 dias (Tiendas VELA no escuela)', legend: { position: 'top' }, 'height':'100%','width':'100%', isStacked: true, 'focusTarget': 'category',chartArea: {width: '80%', height: '60%'} };

		if ( (elemento=document.getElementById('graph_total_historico_inicio_dia')) != null ) {
			chart1_etl = new google.visualization.ColumnChart(elemento);
			chart1_etl.draw(data1_etl, options1);
		}

		options1 = { title: 'Historico fines de dia - Ultimos 30 dias (Tiendas VELA no escuela)', legend: { position: 'top' }, 'height':'100%','width':'100%', isStacked: true, 'focusTarget': 'category',chartArea: {width: '90%', height: '60%'} };

		if ( (elemento=document.getElementById('graph_total_historico_fin_dia')) != null ) {
			chart2_etl = new google.visualization.ColumnChart(elemento);
			chart2_etl.draw(data2_etl, options1);
		}

		options1 = { title: 'Historico ETL-POST - Ultimos 30 dias (Tiendas VELA no escuela)', legend: { position: 'top' }, 'height':'100%','width':'100%', isStacked: true, 'focusTarget': 'category',chartArea: {width: '90%', height: '60%'} };

		if ( (elemento=document.getElementById('graph_total_historico_etl_post')) != null ) {
			chart3_etl = new google.visualization.ColumnChart(elemento);
			chart3_etl.draw(data3_etl, options1);
		}

		if ((elemento=document.getElementById('tabla_ko_jobs')) != null ) {
			chart4_etl = new google.visualization.Table(elemento);
			chart4_etl.draw(lista_ko_jobs);
		}
	}
	google.charts.setOnLoadCallback(drawCharts_etl);
	
	$(".info_procesos").on("click",function () {
		var ko=$(this).find(".tiendas_ko");
		if (ko.text() != "0") {
			$(".graphs_etl").toggle();
			$(".jobs_etl").toggle();
		}
	});
</script>