<?php
$No_Carga_ssh2=true;
require($_SERVER['DOCUMENT_ROOT']."/config.php");
//<script type="text/javascript" src="/Resources/js/jquery.dataTables.min.js"></script>
//<link rel="stylesheet" type="text/css" href="/Resources/css/jquery.dataTables.min.css" />

if (!preg_match("/ESP|POR|BRA/",$PAIS_SERVER))
	exit;

switch (@$_GET["opcion"]) {
	case "Historico":
		$start=(empty($_GET["start"])?1:$_GET["start"]); $limit=(empty($_GET["limit"])?45:$_GET["limit"]);
		$query="select
				  a.Tienda
				, a.Caja
				, c.Version
				, a.Fecha
				, b.centro
				, b.tipo
				, b.subtipo
				, a.Comentario 
			from Historico a
				join tmpTiendas b on a.tienda=b.numerotienda
				LEFT join Checks$Pais c on a.tienda=c.tienda AND a.Caja=c.Caja
			where date(fecha)=date(NOW()) and b.pais='$Pais' order by a.Fecha desc,a.Tienda,a.Caja limit ".$start.",".$limit;
		//if ($Pais!="ESP") echo $query;
		$sql = myQUERY($query);

		if (count($sql)>1) {
			$tmp="<table id='t_historico_hoy' class='tabla2'><tr><th>Tienda</th><th>Caja</th><th>Version</th><th>Fecha</th><th>Centro</th><th>Tipo</th><th>Subtipo</th><th>Comentario</th></tr>";
			foreach($sql as $d) {
				$clase="";
				if (preg_match("/INTERVENCION/", $d[7])) $clase="Intervencion";
				if (preg_match("/INSTALACION/", $d[7])) $clase="Instalacion'";
				if (preg_match("/ERROR/", $d[7])) $clase="ERROR";
				$tmp.="<tr class='$clase'>"; foreach($d as $d1) { $tmp.="<td>".$d1."</td>"; };
				$tmp.="</tr>";
			}
			$tmp.="</table>";
		}
		else {
			$tmp="<div style='border:1px solid black; background-color:yellow; border-radius:2px; text-align:center;'>No hay entradas aún</div>";
		}
		echo $tmp;
		exit;
		
	case "Actu_Futu":
		if ($Pais=='ESP') {
			//$sql=myQUERY("select DAT_ADIC5, sum(case when caja=1 then 1 else 0 end) as m, sum(case when caja>1 then 1 else 0 end) as s, count(*) from Solo_DATS group by 1 order by 4 desc");
			$sql=myQUERY("select md5sum, sum(case when caja=1 then 1 else 0 end) as m, sum(case when caja>1 then 1 else 0 end) as s, count(*) from Versiones_Futuras group by 1 order by 4 desc");
			$res1=file_get_contents("/home/soporteweb/tmp/futuras_versiones/checklist.dat");
			$tmp="<table id='t_actu_futu_hoy' class='tabla2'><tr><th>Actu. Futu.($res1)</th><th>Master</th><th>Esclavas</th><th>TOTAL</h></tr>";
			foreach($sql as $d) {
				if ($res1 == $d[0]) $clase="style='background-color:lightgreen;'"; else $clase="";
				$tmp.="<tr $clase>"; foreach($d as $d1) { $tmp.="<td>".$d1."</td>"; }; $tmp.="</tr>"; }
			$tmp.="</table>";
			echo $tmp;
		} else {
			echo "No implementado en este pais";
		}
		exit;

	case "get_totals_pie":
		exit;

	case "get_totals":
//		) > DATE_SUB(NOW(), INTERVAL 24 HOUR)
		$data = myQUERY("select FROM_UNIXTIME(600 * FLOOR(UNIX_TIMESTAMP(fecha)/600)), count(*) from Historico where DATE(Fecha) = DATE(NOW()) group by FROM_UNIXTIME(600 * FLOOR(UNIX_TIMESTAMP(fecha)/600))");
		if (empty($data)) {
			header('status: 400 Bad Request', true, 400);
		}
		@$responce->records = count($data);
		$responce->cols=array(
			array ( "id"=>'Intervalo', "label"=>'Intervalo', "type"=>'datetime'),
			array ( "id"=>'Cantidad',   "label"=>'Cantidad', "type"=>'number')
		);

		foreach($data as $k => $d) {
			$temp = array();
				$year=date("Y", strtotime($d[0]));
				$month=date("m", strtotime($d[0]));
				$day=date("d", strtotime($d[0]));
				$time=date("H,i", strtotime($d[0]));
			$temp[] = array('v' => "Date(".$year.",".($month-1).",".$day.",".$time.",0)");
			$temp[] = array('v' => (int) $d[1]); 
			$responce->rows[]=array('c' => $temp);
		}
		$json = json_encode($responce, JSON_NUMERIC_CHECK);
		exit($json);
}
$dir_actual=dirname(__FILE__);
$url_actual=str_replace($_SERVER['DOCUMENT_ROOT'], "/", __FILE__);
?>
<title>HISTORICO MOVIMIENTOS</title>
<style>
	.Instalacion { background-color: #58FA58; }
	.Instalacion:hover { background-color: #58FA59; }
	.Intervencion { background-color:#F5DA81; }
	.Intervencion:hover { background-color:#F5DA82; }
	.ERROR { background-color:#FA5858; }
	.ERROR:hover { background-color:#FA5859; }
	#grafica_historico { border: 1px solid #ccc; width:500; height:100;margin-bottom:2px }
	#Actu_Futu { border: 1px solid #ccc; height:100;margin-bottom:2px;overflow:auto; }
	#vista_historico { border: 1px solid #ccc; height:750; overflow:auto; }
</style>
<div style='border:1px solid black; border-radius:2px;background-color:white;'>
	<table>
		<tr>
			<td><div id='grafica_historico'></div></td>
			<td><div id='Actu_Futu'></div></td>
		</tr>
		<tr>
			<td colspan="2"><div id='vista_historico'></div></td>
		</tr>
	</table>
</div>
</body>

<script>
	var parado=false;
	var url_actual="<?php echo $url_actual; ?>";
	clearInterval(interval_historico_hoy); var interval_historico_hoy=en_background("#vista_historico", url_actual+'?opcion=Historico',10000);
	clearInterval(interval_actu_futu);     var interval_actu_futu=en_background("#Actu_Futu", url_actual+'?opcion=Actu_Futu',5000);


	var timeout_historico=30;
	var interval_vista_historico;

	function drawCharts_Historico(){
		clearTimeout(interval_vista_historico);
		drawTable_Historico();
	}

	function Activa_Refresco(new_timeout) {
		clearTimeout(interval_vista_historico);
		timeout_historico=new_timeout;
		if (timeout_historico>0) {
			interval_vista_historico=setTimeout(drawTable_Historico, timeout_historico*1000);
		}
	}
    
	function drawTable_Historico() {
		if (document.getElementById('grafica_historico') === null)
			return;

		var jsonData=$.ajax({ async:false, url: url_actual+"?opcion=get_totals", dataType: "json", timeout:20000}).responseText;
 		var tabla = new google.visualization.DataTable(jsonData);

	 	var graph_historico = new google.visualization.ColumnChart(document.getElementById('grafica_historico'));
	 	graph_historico.draw(tabla, {
	 		width:"100%", title:"Histograma últimas 24 horas",
	 		legend: { position: "none" },
	 		hAxis: {
					format: 'HH:mm',
					titleTextStyle: { fontSize: 8, color: '#053061', bold: true, italic: false }
				},
			});

		Activa_Refresco(timeout_historico);
	}

	google.charts.setOnLoadCallback(drawCharts_Historico);

</script>
