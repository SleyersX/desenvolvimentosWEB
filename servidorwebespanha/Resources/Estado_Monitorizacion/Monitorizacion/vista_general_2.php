<title>GENERAL</title>
<?php
require("./cabecera_vistas.php");

if (!empty($_GET['get_info'])) {
	switch($_GET['get_info']) {
		case "get_detalle_tiendas":
			echo "DETALLE";
			$centro=$_GET['centro']; $subtipo=$_GET['subtipo'];
			$tmp=myQUERY("select numerotienda,centro,tipo,subtipo,version from tmpTiendas where centro='$centro' and subtipo='$subtipo' order by numerotienda");

			echo '<table id="listado_general" class="TABLA2">';
			echo "<caption>LISTADO DE TIENDAS PARA EL CENTRO:".$centro." Y SUBTIPO ".$subtipo."</caption>";
			echo '<thead><tr>';
			$CAB_LISTADO_GENERAL=array("Tienda","Centro","Tipo","Subtipo", "Version");
			foreach($CAB_LISTADO_GENERAL as $k => $d) { echo "<th>$d</th>"; }
			echo '</tr></thead>';

			foreach($tmp as $d) {
				echo "<tr class='c_centro'>";
				foreach($d as $k1 => $d1)
					echo "<td class=''>".$d1."</td>";
					echo "</tr>";
			}
			echo "</table>";
			exit;
			
case "Imprime_Centros_Detalle":
	$tmpVersiones=array(); $sumVersiones=""; $sumVersionesTotal="";
	$corte=basename(glob("/home/Versiones_Tienda/0*")[0]);
	//$corte="06.70.03-p1";
	$version_actual="06.80.01-p3";
	$tmp=myQUERY("select distinct(version) from tmpTiendas where version>='$corte' AND centro <> 'SEDE' order by version;");
	$sumVersiones=",SUM(CASE WHEN version<'".$corte."' THEN 1 ELSE 0 END) '< $corte'";
	$sumVersionesTotal=",SUM(CASE WHEN version<'".$corte."' THEN 1 ELSE 0 END) 'TOTAL'";
	$tmpVersiones[0]="<$corte";
	foreach($tmp as $k => $d) {
		$sumVersiones.=",SUM(CASE WHEN version='".$d[0]."' THEN 1 ELSE 0 END) '$corte'";
		$sumVersionesTotal.=",CONCAT('<b>',SUM(CASE WHEN version='".$d[0]."' THEN 1 ELSE 0 END),'</b>') 'TOTAL'";
		$tmpVersiones[$k+1]=$d[0];
	}
	$sumVersiones.=", CONCAT('<b>', count(centro), '</b>')";
	$sumVersionesTotal.=", CONCAT('<b>', count(centro), '</b>')";

	echo "<caption>LISTADO GENERAL VERSIONES POR CENTRO Y SUBTIPO TIENDAS</caption>";
	echo '<thead><tr>';
	$CAB_LISTADO_GENERAL=array_merge(array("CENTRO","T.TIENDA"),$tmpVersiones,array("TOTAL TDAs"));
	foreach($CAB_LISTADO_GENERAL as $k => $d) { echo "<th>$d</th>"; }
	echo '</tr></thead>';

	//$Tipos = myQUERY("SELECT DISTINCT(Tipo) FROM tmpTiendas WHERE Centro <> 'SEDE'");
	//$Subtipos = myQUERY("SELECT DISTINCT(Subtipo) FROM tmpTiendas WHERE Centro <> 'SEDE'");

	$Centros = myQUERY("SELECT DISTINCT(Centro) FROM tmpTiendas WHERE Centro <> 'SEDE' order by centro");
	foreach($Centros as $c) {
		$centro=$c[0];
		$tmp=myQUERY("SELECT centro,'&#9660' $sumVersiones FROM tmpTiendas WHERE Centro = '$centro' group by centro order by centro asc");
		foreach($tmp as $k => $d)	{
			echo "<tr class='c_centro'>";
			foreach($d as $k1 => $d1)
				echo "<td class=''>".$d1."</td>";
			echo "</tr>";
		}
	//	$tmp=myQUERY("SELECT '',concat(subtipo,' (',substring(tipo,1,1),')') ".$sumVersiones." FROM tmpTiendas WHERE Centro = '$centro' group by centro, subtipo, tipo order by centro,subtipo,tipo");
		$tmp=myQUERY("SELECT centro,subtipo ".$sumVersiones." FROM tmpTiendas WHERE Centro = '$centro' group by centro, subtipo order by centro,subtipo");
		foreach($tmp as $k => $d)	{
			echo "<tr id='".$centro."' class='detalle'>";
			foreach($d as $k1 => $d1) echo "<td class=''>".$d1."</td>";
			echo "</tr>";
			echo "<tr><div></div></tr>";
		}
	}
	$tmp=myQUERY("SELECT 'TOTAL','' $sumVersiones FROM tmpTiendas");
	foreach($tmp as $k => $d)	{
		echo "<tr class='c_centro total'>";
		foreach($d as $k1 => $d1)
			echo "<td>".$d1."</td>";
		echo "</tr>";
	}

	exit;
}

} /* Del switch */

/* ----------------------------------------------------------------------------------------------------------------------------- */

function Imprime_Centros_Detalle() {
}

?>
<style type="text/css">
	#listado_general td { border-left:1px solid gray; }
	#listado_general thead th { padding:3px; border-left:1px solid gray; text-align:center; }
	.c_centro { font-weight:bold; }
	.c_centro td:nth-child(n) { text-align: right;}
	.c_centro td:nth-child(2) { text-align: center;}
	.total { border-top:2px solid black; background-color: #BDBDBD;}
	.c_centro td:nth-child(last) { border-left:2px solid black; background-color: #BDBDBD;}
	.detalle { font-size:8px; font-style:italic; border-left:1px solid gray; display:none }
	.detalle td:nth-child(n) { text-align: right; }
	.oculto { display:none }
	.visible { display: table-cell;}
</style>

<div class="PANEL">
	<table id="listado_general" class="TABLA2"></table>
	<div id="detalle"></div>
</div>

<script>
	clearInterval(interval_centros);
	var interval_centros=en_background("#listado_general", "Monitorizacion/vista_general_2.php?get_info=Imprime_Centros_Detalle", 10000);

//	$("#listado_general").load("Monitorizacion/vista_general_2.php?get_info=Imprime_Centros_Detalle");

	$("#listado_general .c_centro").on("click", function (e) {
		e.preventDefault();
		$(this).nextUntil(".c_centro").toggle();
	});

	$("#listado_general").on("click", ".detalle", function (e) {
		var centro = encodeURI($('td:eq(0)',this).text());
		var subtipo = encodeURI($(this).closest('tr').find('td:eq(1)').text());
		$("#detalle").load("Monitorizacion/vista_general_2.php?get_info=get_detalle_tiendas&centro="+centro+"&subtipo="+subtipo);
	});
	
/*	function getSum(data, column) {
		var total = 0;
		for (i = 0; i < data.getNumberOfRows(); i++)
			total = total + data.getValue(i, column);
		return total;
	}

	function add_total_row(data) { // add a total row to the DataTable
		var column_count = data.getNumberOfColumns();
		var new_row = new Array();

		for (col_i = 1; col_i < column_count; col_i++) { // total each column
			new_row[col_i] = {v: getSum(data,col_i) };
		}
		return new_row;
	}

	function drawTable() {
		var data_1 = new google.visualization.arrayToDataTable([
			["Tienda","Conexion","Version","Centro","Tipo","Subtipo","Direccion","Poblacion","Provincia","Telefono","IP","tipoConexion","Etiquetas","Frescos","Pruebas","Pais"],
			<?php
				foreach($tmp as $d) {
						echo "[".$d[0].",".$d[1].",'".$d[2]."','".$d[3]."','".$d[4]."','".$d[5]."','".$d[6]."',";
						echo "'".$d[7]."','".$d[8]."','".$d[9]."','".$d[10]."','".$d[11]."','".$d[12]."','".$d[13]."',".$d[14].",'".$d[2]."'],";
					}
			?>
		]);
		var view = new google.visualization.DataView(data_1);
 		var table_1 = new google.visualization.Table(document.getElementById('tabla_tiendas'));
 		table_1.draw(view, {allowHtml: true, width: '100%', frozenColumns:true});
	}

	function drawCharts_1(){
		drawTable();
	}

	google.charts.setOnLoadCallback(drawCharts_1);
*/
	
	
</script>
