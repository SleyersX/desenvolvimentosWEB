<?php
$Altura_Ventas=710; $Altura_Grafica=$Altura_Ventas*30/100; $Altura_Items=$Altura_Ventas-$Altura_Grafica-30;
$Ancho_Total=1000;  $Ancho_Ventas=$Ancho_Total*25/100;     $Ancho_Otros=$Ancho_Total-$Ancho_Ventas-2;

if (!empty($_GET["opcion_ventas"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();
	require_once("/home/soporteweb/tools/mysql.php");
	foreach($_GET as $k => $d) $$k=$d;
	switch($opcion_ventas) {
		case "get_ventas_diarias_json":
			$mysqli_tienda = new mysqli($IP_Tienda, "root", "", "n2a");
			$data=myQUERY_Tienda($mysqli_tienda, "select DATE(BEGIN_DATE) 'Fecha', ROUND(SUM(AMOUNT),2) 'V.Diaria' from  RETAIL_TRANSACTION_SUMMARY GROUP BY 1 order by 1 desc");
			if (empty($data)) {
				header('status: 400 Bad Request', true, 400);
			}
			@$responce->records = count($data);
			$responce->cols=array(
				array ( "id"=>'Fecha', "label"=>'Fecha', "type"=>'date'),
				array ( "id"=>'Cantidad',   "label"=>'Cantidad', "type"=>'number')
			);

			foreach($data as $k => $d) {
				$temp = array();
					$year=date("Y", strtotime($d[0]));
					$month=date("m", strtotime($d[0]));
					$day=date("d", strtotime($d[0]));
				$temp[] = array('v' => "Date(".$year.",".($month-1).",".$day.")");
				$temp[] = array('v' => $d[1]); 
				$responce->rows[]=array('c' => $temp);
			}
			$json = json_encode($responce, JSON_NUMERIC_CHECK);
			exit($json);			
			break;

		case "get_ventas_x_articulo":
			$mysqli_tienda = new mysqli($IP_Tienda, "root", "", "n2a");
			$tmp=myQUERY_Tienda($mysqli_tienda, "select a.ITEM_ID 'Articulo',b.DESCRIPTION 'Descripcion',SUM(ROUND(a.AMOUNT,2)) 'V. Total' from RETAIL_TRANSACTION_SUMMARY a JOIN ITEM b ON a.item_id=b.item_id GROUP BY 1 order by 3 desc");
			if (count($tmp)>0) {
				echo "<table class='tabla2'>";
				echo "<thead><tr><th>Articulo</th><th>Descripcion</th><th>Venta &#9660;</th></tr></thead>";
				foreach($tmp as $d) {
					echo "<tr class='row_item'><td>".$d[0]."</td><td>".$d[1]."</td><td>".$d[2]."</td></tr>";
				}
				echo "</table>";
				echo "</div>";
			} else {
				echo "<b class='Error'>NO HAY DATOS</b>";
			}
			break;
	}
	@mysqli_close($db);
	exit;
}

?>
<style type="text/css">
	#resultado { overflow-y:auto; height:<?php echo $Altura_Ventas-26; ?>px; }
	#resultado_2 { overflow-y:auto; width:<?php echo $Ancho_Otros; ?>px; height:<?php echo $Altura_Items; ?>px; }
	.Aviso_1 { margin:1em; background-color: lightcyan; border:1px solid red; border-radius: 2px; }
	#grafica { width: <?php echo $Ancho_Otros; ?>px; height: <?php echo $Altura_Grafica; ?>px;}
	.cuadros1 { border: 1px solid blueviolet; border-radius: 3px; padding: 2px; background-color: white; }
	#t_ventas td { vertical-align: top;}
	#recargar { cursor: pointer;}
	.titulo { font-family: sans-serif; text-align: center; width: 100%; display: table; font-weight: bold;}
</style>

<fieldset style="height:870px !important">
	<legend id="recargar" title="Pulse aqui para recargar datos">INFORME DE VENTAS DIARIAS DE LA TIENDA</legend>
	<div class="Aviso Aviso_New">
		<h3>Los datos facilitados en esta opci&oacute;n son meramente informativos.</h3>
		<p>En ning&uacute;n caso, deben sustituir los datos de otras aplicaciones reglamentarias de la empresa.</p>
	</div>
	<table id="t_ventas">
		<tr>
			<td rowspan="2">
				<div class="cuadros1">
					<span class="titulo">Ventas Diarias</span>
					<hr>
					<div id="resultado"></div>
				</div>
			</td>
			<td><div class="cuadros1" id="grafica"></div></td>
		</tr>
		<tr>
			<td rowspan="2">
				<div class="cuadros1">
					<span class="titulo">Acumulado por articulo (de mayor a menor venta)</span>
					<hr>
					<div id="resultado_2"></div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>	
<script>
	var url_local="Datos_PHP/<?php echo basename(__FILE__); ?>";
	var IP_Tienda="<?php echo $con_tda->GetIP(); ?>";
	Desbloqueo();

	function drawVentasDiarias() {
		url=url_local+"?opcion_ventas=get_ventas_diarias_json&IP_Tienda="+IP_Tienda+"&altura="+$("#resultados").height();
		var jsonData=$.ajax({ async:false, url: url, dataType: "json", timeout:20000}).responseText;
		var tabla = new google.visualization.DataTable(jsonData);
		var view_tabla = new google.visualization.Table(document.getElementById('resultado'));
	 	view_tabla.draw(tabla, { height:"100%", width:"100%"});
	 	var graph = new google.visualization.LineChart(document.getElementById('grafica'));
		graph.draw(tabla, { width: "<?php echo $Ancho_Otros; ?>", height: "<?php echo $Altura_Grafica; ?>", title:"Ventas Diarias", trendlines: { 0: { type: 'exponential', visibleInLegend: false } } });

		$("#resultado_2").html("<img src='/img/Loading-data.gif'/>");
		$.get(url_local, { opcion_ventas:"get_ventas_x_articulo", IP_Tienda:IP_Tienda, altura:$("#resultados").height() }, function (data) {
			$("#resultado_2").html(data);
		});
	}

	$("#recargar").on("click",function () {
		drawVentasDiarias();
	})
	
	google.charts.setOnLoadCallback(drawVentasDiarias);

</script>
