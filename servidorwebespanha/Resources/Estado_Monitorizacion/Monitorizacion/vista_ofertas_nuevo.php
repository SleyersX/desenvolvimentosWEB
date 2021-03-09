<?php
require("./cabecera_vistas.php");

switch(@$_GET["opcion"]) {
	case "all_ofertas":
//		$lista_ofertas_group=myQUERY("select oferta,count(*) from Tienda_Oferta group by oferta");
		$lista_ofertas_group=myQUERY("select a.oferta, SUM(CASE WHEN b.Conexion AND b.Version LIKE '0%' THEN 1 ELSE 0 END) AS suma from Tienda_Oferta a JOIN tmpTiendas b ON a.tienda=b.numerotienda and b.centro<>'SEDE' group by a.oferta");
		echo "
			<table id='t_ofertas' class='tabla2 font_ofertas'>
				<caption class='caption capt_ofertas'>Esta tabla muestra para cada oferta vigente, el número de tiendas en donde está presente</caption>
				<tr>
					<th title='CODIGO DE OFERTA'>Oferta</th><th title='CANTIDAD DE TIENDAS EN DONDE LA OFERTA ESTA PRESENTE'>Cantidad de tiendas</th>
				</tr>";
		foreach($lista_ofertas_group as $d) {
			echo "<tr class='oferta' title='PULSE AQUI PARA VER EL DETALLE'>
				<td id='oferta_code' style='text-align:center'>".$d[0]."</td><td style='text-align:right'>".$d[1]."</td></tr>";
		}
		echo "</table>";
		exit;

	case "info_ofertas":
		$oferta=$_GET["oferta"];
		$tmp=myQUERY("select info, count(*) from Tienda_Oferta where oferta=$oferta group by info");
		foreach($tmp as $d) {
			$tmp1=myQUERY("select count(*) from tmpTiendas where centro<>'SEDE' AND subtipo='".$d[0]."';");
			$lista_por_subtipo[$d[0]] = array($d[0], $d[1], $tmp1[0][0]);
		}
		if (count($lista_por_subtipo)>0) {
			echo "
				<table id='t_detalle_ofertas' class='tabla2 font_ofertas'>
				<caption class='caption capt_detalle'>
					Detalle de la oferta ".$oferta.", donde tenemos la clasificación por subtipo, en NUMERO de TIENDAS donde está vigente esta oferta, y el total de tiendas de ese subtipo en la base de datos de tiendas del servidor (conectadas y no conectadas).
					 <button id='b_ofertas_cvs'>Descargar CVS</button><iframe id='iframe_descarga' style='display:none'></iframe>
				</caption>
				<tr class='detalle_oferta'>
					<th title='SUBTIPO DE TIENDA DONDE LA OFERTA ESTA VIGENTE'>Subtipo tiendas</th>
					<th title='NUMERO DE TIENDAS DONDE ESTA VIGENTE LA OFERTA Y ESTAN EN LINEA'>Numero tiendas</th>
					<th title='NUMERO TOTAL DE TIENDAS DE ESE SUBTIPO EN LA BBDD DEL SERVIDOR (INCLUYEN LAS TIENDAS DONDE LA OFERTA ESTA VIGENTE)'>Total subtipo</th></tr>";
			foreach($lista_por_subtipo as $d) {
				echo "<tr class='oferta'><td id='oferta_subtipo'>".$d[0]."</td><td>".$d[1]."</td><td>".$d[2]."</td></tr>";
			}
			echo "</table>
				<script>
					$('#t_detalle_ofertas tr').on('click', function(event) {
						var subtipo = $(this).children().first().text();
						Recarga('detalle_tiendas','Monitorizacion/vista_ofertas_nuevo.php?opcion=info_tiendas&oferta='+oferta+'&subtipo='+subtipo.replace(' ','%20'));
					});
					$('#b_ofertas_cvs').on('click',function () {
						var url = 'Monitorizacion/vista_ofertas_nuevo.php?opcion=to_cvs&oferta=".$oferta."';
						window.open(url,'_blank');
					});
				</script>";
		} else {
			echo "<b>No hay datos disponibles para la oferta ".$oferta."</b>";
		}
		exit;

	case "info_tiendas":
		$oferta=$_GET["oferta"]; $subtipo=urldecode($_GET["subtipo"]);
		echo "<b><center>Detalle de la oferta ".$oferta.", subtipo ".$subtipo."</center></b><hr>";

		$lista_ofertas_group=myQUERY("
			select b.numerotienda, b.centro, b.tipo, b.subtipo, IF(b.version NOT LIKE '0%', 'MASTER NO CONECTADA',b.Version)
			from tmpTiendas b
			where
				b.numerotienda not in (select distinct(a.tienda) from Tienda_Oferta a where a.oferta=$oferta and info='$subtipo')
				and subtipo='$subtipo'
				and (conexion IS NULL OR Conexion = 0)
				and b.centro<>'SEDE'
			");
		$c_lista_ofertas_group=count($lista_ofertas_group);
		if ($c_lista_ofertas_group > 0) {
			echo "<table id='t_detalle_tiendas' class='tabla2 font_ofertas'>";
			echo "<caption class='caption no_vigente'><b>Tiendas donde la oferta no está vigente:</b> ".$c_lista_ofertas_group."</caption>";
			echo "<tr><th>Tienda</th><th>Centro</th><th>Tipo</th><th>Subtipo</th><th>Version</th></tr>";
			foreach($lista_ofertas_group as $d) {
				echo "<tr class='detalle_tienda'>"; foreach($d as $d1) echo "<td>".$d1."</td>"; echo "</tr>";
			}
			echo "</table>";
		}
		echo "<hr>";

		$lista_ofertas_group=myQUERY("
			select tienda, centro, tipo, subtipo, version
			FROM Tienda_Oferta a
				JOIN tmpTiendas b ON b.numerotienda=a.tienda AND b.Version LIKE '0%' AND b.Centro <> 'SEDE'
			WHERE a.oferta=$oferta and info='$subtipo'");
		$c_lista_ofertas_group=count($lista_ofertas_group);
		if ($c_lista_ofertas_group > 0) {
			echo "
				<table id='t_detalle_tiendas' class='tabla2 font_ofertas'>
					<caption class='caption vigente'><b>Tiendas donde la oferta está vigente:</b> ".$c_lista_ofertas_group."</caption>
					<tr><th>Tienda</th><th>Centro</th><th>Tipo</th><th>Subtipo</th><th>Version</th></tr>";
			foreach($lista_ofertas_group as $d) {
				echo "<tr class='detalle_tienda'>"; foreach($d as $d1) echo "<td>".$d1."</td>"; echo "</tr>";
			}
			echo "</table>";
		}
		exit;
		
	case "to_cvs":
		$Res=myQUERY("select a.oferta, b.numerotienda, b.centro, b.tipo, b.subtipo, IF(b.version NOT LIKE '0%', 'MASTER NO CONECTADA',b.Version) from Tienda_Oferta a JOIN tmpTiendas b ON a.tienda=b.numerotienda where b.centro<>'SEDE' and a.oferta in (".$oferta.") order by 6 desc,1,4,5");
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$oferta}.csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		$outputBuffer = fopen("php://output", 'w');
		foreach($Res as $val) {
			fputcsv($outputBuffer, $val);
		}
		fclose($outputBuffer);

		exit;
}
//	$todas_ofertas=myQUERY("select oferta,tienda,centro,tipo,subtipo from Tienda_Oferta a join tmpTiendas b on b.numerotienda=a.tienda");
$Alto_Tablas="800px";

if (empty($opcion)) {
	echo '<script type="text/javascript" src="/Resources/js/jquery.js"></script>';
	echo "<style src='/css/tabla2.css'/>";
}

?>
<title>VISTA OFERTAS</title>
<style>
	.gene_ofertas { overflow:auto;border:1px solid blue;border-radius:3px; padding:2px; height:<?php echo $Alto_Tablas;?>; width:400px;  }
	.centrado { font-size:18px; margin-left: 2em; text-align: center; vertical-align: middle; line-height:<?php echo $Alto_Tablas;?>;  }
	.oculto { display: none; }
	.visible { display: block; }
	.caption {border:1px solid black;border: 1px solid black; border-radius: 3px; margin-bottom: 2px; }
	.capt_detalle { background-color: lightsalmon; }
	.capt_ofertas { background-color: lightcyan; }
	.no_vigente { background-color: lightcoral; }
	.vigente { background-color: lightgreen; }
	.font_ofertas { font-size:13px; }
	#info_oferta { width:600px; height:<?php echo ($Alto_Tablas*30)/100; ?>px; margin-bottom:2px; }
	#detalle_tiendas { width:600px; height:<?php echo ($Alto_Tablas*70)/100; ?>px; }
</style>

<div style="border:1px solid black; border-radius:2px; background-color:white; font-family:'Roboto'; ">
	<span id="id_oferta" style="display:none"></span><span id="id_subtipo"></span>
	<table class="px14">
		<caption class="caption"><b>Informacion de todas las ofertas de la red</b><br>Solo aparecen las ofertas VIGENTES, y sobre tiendas donde hay conexión.</caption>
		<tr>
			<td><div id="gene_oferta" class="gene_ofertas"></div></td>
			<td>
				<div id="info_oferta" class="gene_ofertas"><b>&#9664 Pulse en una oferta para ver el detalle</b></div>
				<div id="detalle_tiendas" class="gene_ofertas"><b>&#9650 Pulse en subtipo para ver el detalle</b></div>
			</td>
		</tr>
	</table>
</div>

<script  type="text/javascript">
	var doHandle = true;
	var oferta;
	function Recarga(donde, url) {
		$("#"+donde).html("<img src='/img/Loading-data.gif'/>").load(url);
	}

	Recarga("gene_oferta","Monitorizacion/vista_ofertas_nuevo.php?opcion=all_ofertas");

	$("#gene_oferta").on("click", '.oferta', function(event) { 
		oferta = $(this).children().first().text();
		$("#id_oferta").html(oferta);
		$("#detalle_tiendas").html("&#9650 Pulse en subtipo para ver el detalle");
		Recarga("info_oferta","Monitorizacion/vista_ofertas_nuevo.php?opcion=info_ofertas&oferta="+oferta);
	});


</script>