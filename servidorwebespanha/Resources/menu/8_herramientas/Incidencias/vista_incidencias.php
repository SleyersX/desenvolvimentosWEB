<title>INCIDENCIAS</title>
<?php
$No_Carga_ssh2=true;
require($_SERVER['DOCUMENT_ROOT']."/config.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }
if (!in_array($_SESSION['grupo_usuario'], array(1,2,6,7,8))) {
	require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/incorrect_profile.php"); die(); }

if(empty($_GET['x'])) {
	require_once($DOCUMENT_ROOT.$DIR_RAIZ."/styles_js/head_1.php");
	echo '<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />';
}

//myQUERY("CREATE OR REPLACE VIEW Incidencias_N2A AS (SELECT * FROM Incidencias WHERE Servicio");

shell_exec("cd /home/soporteweb/Incidencias/; if [ `find export.txt -newer ultima_vez` ]; then php ../tools/genera_incidencias.php && touch ultima_vez ; fi");

function Busca_en_Array($lista, $valor, $indice, $campo) {
	foreach ($lista as $k => $d) if ($d[$indice-1] == $valor) return $d[$campo-1];
	return 0;
}

$years=array(
// 	"All"  => array("All", "block"),
	"2017" => array("2017","block"),
	"2016" => array("2016","block"),
	"2015" => array("2015","block"),
	"2014" => array("2014","none"),
	"2013" => array("2013","none"),
	"2012" => array("2012","none"),
	"2011" => array("2011","none"),
	"2010" => array("2010","none")
);

$sql_Desglose_x_Prioridad="SUM(CASE WHEN Estado='Resolved' AND DiaNiveActu<=1 THEN 1 ELSE 0 END),";
for ($nivel=1; $nivel<=4; $nivel++) {
	if ($nivel>1) $sql_Desglose_x_Prioridad.=",";
	$sql_Desglose_x_Prioridad.="
	SUM(CASE WHEN DiaNiveMax=$nivel AND Estado='Closed' AND CodiReso NOT LIKE 'ANUL%'  THEN 1 ELSE 0 END) 'Solucionadas',
	SUM(CASE WHEN DiaNiveActu=$nivel AND Estado NOT IN ('Closed','Resolved') THEN 1 ELSE 0 END) 'Pendientes',
	SUM(CASE WHEN DiaNiveMax=$nivel AND CodiReso LIKE 'ANUL%' THEN 1 ELSE 0 END) 'Anuladas'";
}

$sql_Desglose_x_Prioridad.="
	,SUM(CASE WHEN Estado='Closed' AND CodiReso NOT LIKE 'ANUL%'  THEN 1 ELSE 0 END)
	,SUM(CASE WHEN Estado NOT IN ('Closed','Resolved') THEN 1 ELSE 0 END)
	,SUM(CASE WHEN CodiReso LIKE 'ANUL%' THEN 1 ELSE 0 END)";

$sql_Desglose_x_Prioridad.="
	,SUM(CASE WHEN DATEDIFF(FechReso,FechGrab)=0 THEN 1 ELSE 0 END) '<1'
	,SUM(CASE WHEN DATEDIFF(FechReso,FechGrab) in (1,2,3) THEN 1 ELSE 0 END) '1-3'
	,SUM(CASE WHEN DATEDIFF(FechReso,FechGrab)>3 THEN 1 ELSE 0 END) '>3'";

$t_Prio=array(
	1 => "Muy Urgente",
	2 => "Urgente",
	3 => "Importante",
	4 => "Puede Esperar",
	'Total' => 'TOTAL' );

function Imprime_Linea_x_Prioridad($d1,$total=NULL) {
	global $t_Prio,$tabla_1;
	@list($Prioridad, $PU, $S1,$A1,$P1, $S2,$A2,$P2, $S3,$A3,$P3, $S4,$A4,$P4, $TS,$TA,$TP, $Menor_que_1, $Entre_1_y_3, $Mayor_que_3) = $d1;
	$tabla_1[$Prioridad]=$d1;
	$tmp='<tr><td class="separa">'.($total?$Prioridad:$t_Prio[$Prioridad]).'</td>';
	$tmp.="<td class='separa'>$PU</td>";
	$tmp.="<td>$S1</td><td>$A1</td><td class='separa'>$P1</td>"; 
	$tmp.="<td>$S2</td><td>$A2</td><td class='separa'>$P2</td>"; 
	$tmp.="<td>$S3</td><td>$A3</td><td class='separa'>$P3</td>"; 
	$tmp.="<td>$S4</td><td>$A4</td><td class='separa'>$P4</td>";
	$tmp.="<td>".$TS."</td><td>$TA</td><td class='separa'>$TP</td>";
	$tmp.="<td class='separa'>".($TS+$TA+$TP+$PU)."</td>";
	$tmp.="<td>".$Menor_que_1."</td><td>$Entre_1_y_3</td><td class='separa'>$Mayor_que_3</td>";
	$tmp.="</tr>";
	return $tmp;
}

function Cabecera_Linea_x_Prioridad() {
	$tmp='
		<thead> 
		<tr class="separa">
			<th rowspan="2" class="separa"></th>
			<th rowspan="2" class="separa" title="Incidencias pendientes de cerrar por el usuario">Pend.<br>Usuario</th>
			<th colspan="3" class="separa">NIVEL 1</th>
			<th colspan="3" class="separa">NIVEL 2</th>
			<th colspan="3" class="separa">NIVEL 3</th>
			<th colspan="3" class="separa">NIVEL 4</th>
			<th class="separa" colspan="3">TOTALES</th>
			<th rowspan="2" class="separa">TOTAL</th>
			<th class="separa" colspan="3">Tiempo resolucion (dias)</th>
		</tr>
		<tr>';
	for($i=0; $i<5; $i++) $tmp.='<th>Solu.</th><th>Pend.</th><th class="separa">Anul.</th>';
	$tmp.='<th style="margin-left:20" width=40>< 1</th><th width=40>1 - 3</th><th class="separa" width=40>> 3</th></tr></thead>';
	return $tmp;
}

$tabla_1=array();
$t_incidencias='<table class="t_incidencias" id="t_total_incidencias">';
$t_incidencias.=Cabecera_Linea_x_Prioridad();
foreach($years as $year => $datos) {
	$tmp=myQUERY("select 'Total $year',$sql_Desglose_x_Prioridad from Incidencias where year(FechGrab) in ($year)");
	$t_incidencias.=Imprime_Linea_x_Prioridad($tmp[0],true);
}
$tmp=myQUERY("select 'TOTAL',$sql_Desglose_x_Prioridad from Incidencias");
$t_incidencias.=Imprime_Linea_x_Prioridad($tmp[0],true);
$t_incidencias.='</table>';

//foreach($years as $year => $datos) {
if (0 == 1) {
	echo '
	<div style="background:lightyellow; border:2px solid black; border-radius:4px">
	<center><h3 class="h3_incidencias" title="Pulse aqu&iacute; para desplegar informacion">Incidencias del a&ntilde;o '.$year.'</h3></center>
	<div id="incidencias_'.$year.'">';

// $Incidencias_grabadas_x_dia_nivel=myQUERY("select DATE(FechGrab),DiaNiveMax,count(*) from Incidencias where year(FechGrab)='2015' group by 1,2");
// 	if ($year

	/* ---------------------------------------------------------------------------------------------------------------------- */
	$Desglose_x_Prioridad=myQUERY("
		select Prioridad, $sql_Desglose_x_Prioridad from Incidencias where year(FechGrab) in ($year) group by Prioridad order by Prioridad;
		select 'Total',$sql_Desglose_x_Prioridad from Incidencias where year(FechGrab) in ($year)");

	echo '<table class="t_incidencias" id="t_incidencias_'.$year.'" >';
	Cabecera_Linea_x_Prioridad();
	foreach($Desglose_x_Prioridad as $k1 => $d1) {
		Imprime_Linea_x_Prioridad($d1);
	}
	echo '</table>';

	/* ---------------------------------------------------------------------------------------------------------------------- */
	echo '<div>
			<img src="/Incidencias/mensual_'.$year.'.jpg?'.time().'" height="160" />
			<img src="/Incidencias/semanal_'.$year.'.jpg?'.time().'" height="160" />
			<img src="/Incidencias/diario_'.$year.'.jpg?'.time().'" height="160" />
		</div>';

	/* ---------------------------------------------------------------------------------------------------------------------- */
	$meses=array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic");
	$txt_meses="";  $sql="";
	for($i=0; $i<12; $i++) {
		if ($i) { $sql.=","; }
		$sql.="SUM(CASE WHEN MONTH(FechGrab)=".($i+1)." THEN 1 ELSE 0 END) '".$meses[$i]."'";
		$txt_meses.="<th width='40'>".$meses[$i]."</th>";
	}
	$Totales_x_CodiReso_Mes=myQUERY("
		select distinct(if(CodiReso='','PENDIENTE COD. RESOLUCION - PENDING RESOLUTION CODE',CodiReso)),$sql,count(*) as total from Incidencias where year(FechGrab) in ($year) group by 1 order by total desc;
		select 'TOTAL',$sql,count(*) from Incidencias where year(FechGrab) in ($year)");

	echo '
		<table class="t_incidencias">
		<thead>
			<tr class="separa"><th class="separa">Codigo de Resolucion</th>'.$txt_meses.'<th>TOTAL</th><th>GRAFICOS</th></tr>
		</thead>';
		$primero=0; 
		foreach($Totales_x_CodiReso_Mes as $k1 => $d1) {
			$texto_codireso=explode("-",$d1[0]);
			echo '<tr><td class="separa">'.@$texto_codireso[($Pais=='CHI'?1:0)].'</td>';
			for($i=1; $i<=12; $i++) echo "<td>".$d1[$i]."</td>";
			echo "<td class='separa' style='font-weight:bold'>".$d1[13]."</td>";
			if (!$primero) {
				echo "<td rowspan='".count($Totales_x_CodiReso_Mes)."' id='grafico_codireso_$year'>GRAFICO</td>"; $primero++;
			}
			echo "</tr>";
		}
	echo '</table>';

	/* ---------------------------------------------------------------------------------------------------------------------- */
	$Totales_x_ElemProd_Mes=myQUERY("
		select distinct(if(tipoprobl='','PENDIENTE CLASIFICAR - PENDING',tipoprobl)), $sql, count(*) as total from Incidencias where year(FechGrab) in ($year) group by 1 order by total desc;
		select 'TOTAL',$sql,count(*) from Incidencias where year(FechGrab) in ($year)");

	echo '
		<table class="t_incidencias">
		<thead>
			<tr class="separa"><th class="separa">Origen del problema</th>'.$txt_meses.'<th>TOTAL</th></tr>
		</thead>';

		foreach($Totales_x_ElemProd_Mes as $k1 => $d1) {
			$texto_tipoprob=explode("-",$d1[0]);
			echo '<tr><td class="separa">'.@$texto_tipoprob[($Pais=='CHI'?1:0)].'</td>';
			for($i=1; $i<=12; $i++) echo "<td>".$d1[$i]."</td>";
			echo "<td class='separa' style='font-weight:bold'>".$d1[13]."</td>";
			echo "</tr>";
		}
	echo '</table>';

	$Inci_Pend=myQUERY("select ID,substr(Titulo,1,50),FechGrab,Defecto from Incidencias where DiaNiveActu=4 and year(FechGrab)='$year'");
	echo '<table class="t_incidencias">';
	foreach ($Inci_Pend as $k => $d) {
		list($InciID, $Titulo, $FechGrab, $Defecto)=$d;
		echo "<tr><td>$InciID</td><td>$Titulo</td><td>$FechGrab</td><td>$Defecto</td></tr>";
	}
	echo "</table>";

	echo '</div></div>';
}

$all_years=myQUERY("select distinct(year(FechGrab)) from Incidencias order by 1 desc");
$actual_year=$all_years[0][0]	;
$todos_los_meses=myQUERY("select date_format(FechGrab,'%Y-%m'),count(ID) from Incidencias group by 1");

$Nivel1="SUM(CASE WHEN DiaNiveMax=1 THEN 1 ELSE 0 END) AS 'N1'";
$Nivel2="SUM(CASE WHEN DiaNiveMax=2 THEN 1 ELSE 0 END) AS 'N2'";
$Nivel3="SUM(CASE WHEN DiaNiveMax=3 THEN 1 ELSE 0 END) AS 'N3'";
$Nivel4="SUM(CASE WHEN DiaNiveMax=4 THEN 1 ELSE 0 END) AS 'N4'";

$tmp=myQUERY("SELECT YEAR(FechGrab), $Nivel1 , $Nivel2 , $Nivel3 , $Nivel4 FROM Incidencias GROUP BY 1");
foreach($tmp as $k => $d) $inci_x_year[$d[0]]=$d;
$q_mes=myQUERY("SELECT YEAR(FechGrab), MONTH(FechGrab), DAY(FechGrab), $Nivel1 , $Nivel2 , $Nivel3 , $Nivel4 FROM Incidencias GROUP BY 1,2");
		
//echo "<pre>"; print_r($q_mes); echo "</pre>";
$q_semana=myQUERY("SELECT YEAR(FechGrab), WEEK(FechGrab), $Nivel1 , $Nivel2 , $Nivel3 , $Nivel4 FROM Incidencias GROUP BY 1,2");

$vista_global="['Year', 'Mes', 'Dia', 'Nivel 1', 'Nivel 2', 'Nivel 3', 'Nivel 4', { role: 'annotation'}],";

/*$vista_global="[
	{label: 'Year', id: 'year'}, {label: 'Mes', id: 'mes'}, {label: 'Dia', id: 'dia'},
	{label: 'Nivel 1', id: 'N1'},
	{label: 'Nivel 2', id: 'N2'},
	{label: 'Nivel 3', id: 'N3'},
	{label: 'Nivel 4', id: 'N4'},
],"; */
foreach($q_mes as $k => $d) {
	$total=$d[3]+$d[4]+$d[5]+$d[6];
	$vista_global.="[ ".$d[0].",".$d[1].",".$d[2].",".$d[3].",".$d[4].",".$d[5].",".$d[6].",".$total.",],";
};

$vista_semanal="[ {label: 'Year', id: 'year'}, {label: 'Week', id: 'week'}, {label: 'Nivel 1', id: 'N1'}, {label: 'Nivel 2', id: 'N2'}, {label: 'Nivel 3', id: 'N3'}, {label: 'Nivel 4', id: 'N4'} ],";
foreach($q_semana as $k => $d) { $vista_semanal.="[ ".$d[0].",".$d[1].",".$d[2].",".$d[3].",".$d[4].",".$d[5]."],"; };

$q_total=myQUERY("SELECT * FROM Incidencias");
$vista_total="";
foreach($q_total as $k => $d) {
	$vista_total.="[ ";
	$vista_total.="'".$d[0]."',";
	$vista_total.="'".$d[1]."',";
	$vista_total.="new Date(0,0,0),";
	$vista_total.="new Date(0,0,0),";
	$vista_total.="'".$d[4]."',";
	$vista_total.="'".$d[5]."',";
	$vista_total.="'".$d[6]."',";
	$vista_total.=$d[7].",";
	$vista_total.=$d[8].",";
	$vista_total.=$d[9].",";
	$vista_total.="'".$d[10]."',";
	$vista_total.="'".$d[11]."',";
	$vista_total.="'".$d[12]."',";
	$vista_total.=$d[13]."";
	$vista_total.="],";
	break;
}
//print_r($q_total);
//echo $vista_total;

$origen_total=array();
$tmp=myQUERY("select 'TOTAL',IF((servicio<>'TPVS - (ES)' AND servicio<>'VELA BACKOFFICE - (ES)') OR servicio is null,'OTROS',servicio),count(*) from Incidencias group by 1,2; select year(FechGrab),IF((servicio<>'TPVS - (ES)' AND servicio<>'VELA BACKOFFICE - (ES)') OR servicio is null,'OTROS',servicio),count(*) from Incidencias group by 1,2");
foreach($tmp as $d) {
	$origen_total[$d[0]]=array($d[1],$d[2]);
}

$Ancho_Pantalla=1250;
$Alto_Pantalla=950;
$Altura_Cuadro=$Alto_Pantalla-100;

?>

<style>
	.h3_incidencias { cursor:pointer; }
	.rdiv {
		float:left; position:relative;
		border-radius:8px;
		border:1px solid red;
		padding:2px;
		background-color:white;
		margin:1px;
	}
	#texto_year span { font-size:200%;}
	#div_global { width:<?php echo $Ancho_Pantalla; ?>px !important; height: <?php echo $Altura_Cuadro; ?>px !important;}
	#div1 { width: 99% !important;  }
	#div2 { width: 99% !important; height: <?php echo $Altura_Cuadro*70/100; ?>px !important; overflow-y:auto; padding:2px;}
</style>


	<div id="div_global" class='rdiv'>
		<div id="div1" class="rdiv"><?php echo $t_incidencias; ?></div>
		<div id="div2"><table id="tabla_global"></table></div>
	</div>

<script>

	var all_years=[ <?php foreach($all_years as $d) echo $d[0].","; ?> ];

	var data1, data2, data3, data4;
	var chart_pie = new Array;
	var chart_mensual = new Array;
	var chart_semanal = new Array;
	var origen_incidencia = [
		<?php
			foreach($origen_total as $k => $d) {
				echo "['".$k."','".$d[0]."',".$d[1]."],";
			}
		?>
		];

	function getSum(data, column) {
		var total = 0;
		for (i = 0; i < data.getNumberOfRows(); i++)
			total = total + data.getValue(i, column);
		return total;
	}

	function drawCharts(){
		get_Data();
		<?php
			echo "drawTable(0);";
			foreach($all_years as $d) {
				$year=$d[0];
				echo "drawTable(".$year.");";
			}
		?>
	}

Date.prototype.getWeekNumber = function(){
    var d = new Date(+this);
    d.setHours(0,0,0,0);
    d.setDate(d.getDate()+4-(d.getDay()||7));
    return Math.ceil((((d-new Date(d.getFullYear(),0,1))/8.64e7)+1)/7);
};


	function get_Data() {
		data1 = new google.visualization.DataTable();
			data1.addColumn('string','ID');
			data1.addColumn('string','Titulo');
			data1.addColumn('date','FechGrab');
			data1.addColumn('date','FechReso');
			data1.addColumn('string','CodiReso');
			data1.addColumn('string','ElemProd');
			data1.addColumn('string','TipoProbl');
			data1.addColumn('number','DiaNiveMax');
			data1.addColumn('number','Prioridad');
			data1.addColumn('number','DiaNiveActu');
			data1.addColumn('string','Asignado');
			data1.addColumn('string','Estado');
			data1.addColumn('string','VersInst');
			data1.addColumn('number','Defecto');
			data1.addRows([ <?php echo $vista_total; ?> ]);

		data2 = new google.visualization.DataTable();
			data2.addColumn('string', 'Nivel');
			data2.addColumn('number', 'Incidencias');
			data2.addColumn('number', 'Year');
			data2.addRows([
			<?php
				foreach($all_years as $d) {
					$year=$d[0];
					echo "[ 'NIVEL 1', ".$inci_x_year[$year][1].",".$year."],";
					echo "[ 'NIVEL 2', ".$inci_x_year[$year][2].",".$year."],";
					echo "[ 'NIVEL 3', ".$inci_x_year[$year][3].",".$year."],";
					echo "[ 'NIVEL 4', ".$inci_x_year[$year][4].",".$year."],";
				}
			?>		
		]);
		data3 = new google.visualization.arrayToDataTable([ <?php echo $vista_global; ?> ]);
		data4 = new google.visualization.arrayToDataTable([ <?php echo $vista_semanal; ?> ]);
		
		
	}

	function setTooltipContent(dataTable,row) {
		if (row != null) {
			var Caja = dataTable.getValue(row, 0);
			var Version = dataTable.getValue(row, 1);
			var Fecha_Inicio = dataTable.getValue(row, 2);
			var Fecha_Fin = dataTable.getValue(row, 3);
			var content = '<div><b>'+Caja+'</b><hr>' + Version + '<hr><b>Fecha inicio:</b> '+Fecha_Inicio+'<hr><b>Fecha Fin:</b> '+Fecha_Fin+'</div>';
			var tooltip = document.getElementsByClassName("google-visualization-tooltip")[0];
			tooltip.innerHTML = content;
		}
	}

	var hAxis_mensual_options={ viewWindow: { min: 0, max: 13 }, format:"" };	
	var hAxis_semanal_options={ viewWindow: { min: 0, max: 53 }, format:"" };
	var altura_year=200;
	function drawTable(year) {
		if (year==0) {
			var min_year=0; var max_year=2200; var text_year="TOTAL";
		}
		else { var min_year=year; var max_year=year; text_year=year; }

		var total_origen = '<div class="total_origen"></div>';
		var i_year='<div class="rdiv" style="width:150; height:'+altura_year+'px! important; font-family: sans-serif;"><span style="font-weight:bold; text-align:middle;">A&ntilde;o: '+text_year+'</span><hr><p style="display:none"><span>Incidencias grabadas</span><span id="total_year"></span></p><p style="display:none"><span>Incidencias resueltas</span><span id="resueltas_year"></span></p></div>';
		var info_year="<tr>";
			 info_year+="<td style='text-align: center'>"+i_year+"</td>";
			 info_year+="<td><div class='rdiv' id='pie_chart_"+text_year+"' style='width:200; height:"+altura_year+"px;'></div></td>";
			 info_year+="<td><div class='rdiv' id='graph_month_"+text_year+"' style='width:350; height:"+altura_year+"px;'></div></td>";
			 info_year+="<td><div class='rdiv' id='graph_week_"+text_year+"' style='width:450; height:"+altura_year+"px;'></div></td>";
			 info_year+="</tr>";
		$("#tabla_global").before(info_year);

		chart_pie[text_year] = new google.visualization.PieChart(document.getElementById('pie_chart_'+text_year));
		chart_mensual[text_year] = new google.visualization.ColumnChart(document.getElementById('graph_month_'+text_year));
		chart_semanal[text_year] = new google.visualization.ColumnChart(document.getElementById('graph_week_'+text_year));

//alert(google.visualization.data2.sum(0));
		if (year==0) {
			var view_global = google.visualization.data.group(data2,
				[{ column: 0, label:"Nivel", type: 'number' }],
				[{ column: 1, label: 'Incidencias', aggregation: google.visualization.data.sum, type: 'number'}]
				);
		}
		else {
			var view_global = new google.visualization.DataView(data2);
			view_global.setRows(view_global.getFilteredRows([{column: 2, minValue: min_year, maxValue: max_year }]));
			view_global.hideColumns(2);
		}

		if (year==0) {
			var view_mensual = google.visualization.data.group(data3, [
				{column: 1, label:"MES", type: 'number'},
			], [
				{column: 3, label: 'Nivel 1', aggregation: google.visualization.data.sum, type: 'number'},
				{column: 4, label: 'Nivel 2', aggregation: google.visualization.data.sum, type: 'number'},
				{column: 5, label: 'Nivel 3', aggregation: google.visualization.data.sum, type: 'number'},
				{column: 6, label: 'Nivel 4', aggregation: google.visualization.data.sum, type: 'number'},
			]);
		} else {
			var view_mensual = new google.visualization.DataView(data3);
			view_mensual.setRows(view_mensual.getFilteredRows([{column: 0, minValue: min_year, maxValue: max_year }]));
			view_mensual.hideColumns([0,2]);
		}

		if (year==0) {
			var view_semanal = google.visualization.data.group(data4, [{
				column: 1, label:"SEMANA", type: 'number'
			}], [
				{column: 2, label: 'Nivel 1', aggregation: google.visualization.data.sum, type: 'number'},
				{column: 3, label: 'Nivel 2', aggregation: google.visualization.data.sum, type: 'number'},
				{column: 4, label: 'Nivel 3', aggregation: google.visualization.data.sum, type: 'number'},
				{column: 5, label: 'Nivel 4', aggregation: google.visualization.data.sum, type: 'number'},
			]);		
		} else {
			var view_semanal = new google.visualization.DataView(data4);
			view_semanal.setRows(view_semanal.getFilteredRows([{column: 0, minValue: min_year, maxValue: max_year }]));
			view_semanal.hideColumns([0]);
		}

		chart_pie[text_year].draw(view_global, {
			title: 'Incidencias '+text_year,
			chartArea: {'width': '60%', 'height': '60%'}, 
			legend: { position: 'top',maxLines:3},
			is3D: true});	
		chart_mensual[text_year].draw(view_mensual, {
			title: 'Incidencias x Mes ('+text_year+')',
			format: 'M',
			height:"200", width:"350", 
			chartArea: {'width': '80%', 'height': '70%'},
			legend: { position: 'top', maxLines: 1 },
			isStacked: true,
			hAxis: hAxis_mensual_options });
		chart_semanal[text_year].draw(view_semanal, {
			title: 'Incidencias x Semana ('+text_year+')',
			height:"200", width:"450",
			chartArea: {'width': '80%', 'height': '70%'},
			legend: { position: 'top', maxLines: 1 },
			isStacked: true,
			hAxis: hAxis_semanal_options });
	}
	google.charts.setOnLoadCallback(drawCharts);


</script>
