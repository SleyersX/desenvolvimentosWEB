<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once($DOCUMENT_ROOT.$DIR_TOOLS.'tools.php');
require_once($DOCUMENT_ROOT.$DIR_TOOLS.'comun.php');
require_once("$DOCUMENT_ROOT/$DIR_TOOLS/head_1.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }

if (empty($Tienda)) { die (Alert("error","ERROR: No hay definida Tienda para conexion...")); }
if (@$Centro=="SEDE") { $Pais="XXX"; $Table="Checks".$Pais; }

$Input_Cambiar_IP="CONCAT('<input type=button value=\"',t.IP,'\" title=\"Pulse aqu&iacute; para cambiar temporalmente la IP de esta tienda\" id=\"Cambiar_IP\" />')";

$Result=myQUERY("select
		CAST(t.Numerotienda AS UNSIGNED),
		t.Centro, t.Tipo, t.Subtipo, t.Direccion, t.Poblacion, t.Provincia, t.Telefono,
		".$Input_Cambiar_IP.", t.tipoConexion, IFNULL(c.NTPVS,'N/A'),
		tipoEtiquetadora, Frescos, Pruebas, Pais
	from tmpTiendas t left join $Table c ON t.numerotienda=c.tienda and c.caja=1
	where t.numerotienda=$Tienda and t.centro like '%".@$Centro."%' and pais in ('$Pais')");
	
	if (!empty($Result[0])) {
		list($Tienda,$Centro,$Tipo,$Subtipo,$Direccion,$Poblacion,$Provincia,$Telefono,$IP,$tipoConexion,$NTPVs,$tipoEtiquetadora,$Frescos,$qPruebas,$qPais)=$Result[0];
		$tabla_datos_tienda="<table id='t_datos_tienda'>
				<tr><th colspan='2'>Datos de la tienda</th></tr>
				<tr><td>Tienda</td><td>".$Tienda."</td></tr>
				<tr><td>Centro</td><td>".$Centro."</td></tr>
				<tr><td>Tipo</td><td>".$Tipo."</td></tr>
				<tr><td>Subtipo</td><td>".$Subtipo."</td></tr>
				<tr><td>Direccion</td><td>".$Direccion."</td></tr>
				<tr><td>Poblacion</td><td>".$Poblacion."</td></tr>
				<tr><td>Provincia</td><td>".$Provincia."</td></tr>
				<tr><td>Telefono</td><td>".$Telefono."</td></tr>
				<tr><td>IP:</td><td>".$IP."</td></tr>
				<tr class='b_abajo'><td>Num.TPVs</td><td>".$NTPVs."</td></tr>
				<tr><td>Frescos:</td><td>".$Frescos."</td></tr>
				<tr><td>Etiquetas:</td><td>".$tipoEtiquetadora."</td></tr>
			</table>";
	} else {
		$tabla_datos_tienda="ERROR: NO EXISTEN DATOS DE LA TIENDA";
	}

	if (@$Frescos=="SI") {
		$Result=myQUERY("set @Tienda=$Tienda;
			select
				sum(case when conexion=1 and Elemento like 'balanza%' then 1 else 0 end) 'Balanza ON',
				sum(case when Elemento like 'balanza%' then 1 else 0 end) 'Balanza Total',
				sum(case when conexion=1 and Elemento like 'pc%' then 1 else 0 end) 'PC ON',
				sum(case when Elemento like 'pc%' then 1 else 0 end) 'PC Total',
				sum(case when conexion=1 and Elemento like 'impres%' then 1 else 0 end) 'Impresora ON',
				sum(case when Elemento like 'impres%' then 1 else 0 end) 'Impresora Total'
			from Elementos where tienda=".$Tienda.";
			");
		list($balanza_on,$balanza_total,$pc_on,$pc_total,$impresora_on,$impresora_total)=$Result[0];
		$tabla_elementos_tienda="<table id='t_datos_elementos_tienda'>
				<tr><th>Elemento</th><th>Conectados</th><th>Total disponibles</th></tr>
				<tr><td>Balanzas</td><td>".$balanza_on."</td><td>".$balanza_total."</td></tr>
				<tr><td>PCs</td><td>".$pc_on."</td><td>".$pc_total."</td></tr>
				<tr><td>Impresoras</td><td>".$impresora_on."</td><td>".$impresora_total."</td></tr>
			</table>";
	} else {
		$tabla_elementos_tienda="<span>Tienda sin PC</span>";
	}
	if ($qPruebas==1) $tabla_query="ChecksXXX"; else $tabla_query="Checks$Pais";
	$Result=myQUERY("select * from ".$tabla_query." where tienda=".$Tienda." order by caja");

	for($caja=0; $caja<$NTPVs; $caja++) {
		list($cTienda,$cCaja,$cConexion,$cVersion,$cModelo,$cExec,$cMSG,$cRAM,$cHDD,$cBIOS,$cLAN,$cNTPVS,$cN_APAG,
			$cDAT1,$cDAT2,$cDAT3,$cDAT4,$cDAT5,$cLastM,$cIP,$cTemper,$cHUB,$cPINPAD,$cReleaseDate,$cINV_HW_SW,
			$cMySQL,$cWSD,$cSWD,$cRAID,$cCDMANAGER) = $Result[$caja];
		@$tabla_info_cajas.="
<div class='burbuja_caja'>
	<table class='t_burbuja_caja'>
		<tr><th colspan='2'>Caja ".$cCaja."</th></tr>
		<tr><td colspan='2' class='b_abajo'>APP: ".($cExec?"SI":"NO")." MySQL: ".($cMySQL?"SI":"NO")." WSD: ".($cWSD?"SI":"NO")."</td></tr>
		<tr><td>Tienda</td><td>".$cTienda."</td></tr>
		<tr><td>Caja</td><td>".$cCaja."</td></tr>
		<tr><td>Conexion</td><td>".$cConexion."</td></tr>
		<tr><td>Version</td><td>".$cVersion."</td></tr>
		<tr><td>Modelo</td><td>".$cModelo."</td></tr>
		<tr><td>Caja</td><td>".$cCaja."</td></tr>
		<tr><td>Caja</td><td>".$cCaja."</td></tr>
		<tr><td>Caja</td><td>".$cCaja."</td></tr>
		<tr><td>Caja</td><td>".$cCaja."</td></tr>
	</table>
</div>";
	}

$tabla_opciones_tienda="NECESITA DARSE DE ALTA PARA ACCEDER A LAS OPCIONES";

?>

<style>
	.burbuja_caja { float:left; position:relative; margin:1px; padding:1px; border-radius:3px; border:1px solid blue; }
	#t_info_tienda { background-color:white; border:1px solid black;}
	#t_info_tienda * { font-size:12px; border-collapse: collapse; }
	#t_datos_tienda { border:1px solid blue; float:left; position:relative; }
	#t_datos_elementos_tienda { border:1px solid blue; float:left; position:relative; }
	#t_datos_elementos_tienda th, #t_datos_tienda th { border-bottom:1px solid blue; background-color:lightgray; }

	.t_burbuja_caja th { border-bottom:1px solid blue; background-color:lightgray; }

	.b_abajo { border-bottom:1px solid; }
</style>

<table id='t_info_tienda'>
	<tr><td><?php echo $tabla_datos_tienda.$tabla_elementos_tienda.$tabla_opciones_tienda; ?></td></tr>
	<tr><td><hr></td></tr>
	<tr><td><?php echo $tabla_info_cajas; ?></td></tr>
	<tr><td><hr></td></tr>
	<tr><td><?php echo $tabla_elementos_tienda; ?></td></tr>
</table>

<?php
$tmp=myQUERY("select DATE_FORMAT(NOW(),'%Y,%m,%d,%H,%i,%S')");
$Now=$tmp[0][0];
$tmp=myQUERY("select count(distinct(caja)) from Versiones_TPV where tienda=$Tienda");
for ($i=1; $i<=$tmp[0][0]; $i++) {
	$tmp1=myQUERY("select caja,version,DATE_FORMAT(Fecha,'%Y,%m,%d,%H,%i,%S') from Versiones_TPV where caja=$i and tienda=".$Tienda." group by Version ORDER BY Caja,Version");
	$d_ante=null;
	foreach($tmp1 as $d) {
		if ($d_ante)
			$versiones[]=array_merge($d_ante,array($d[2]));
		$d_ante=$d;
	}
	$versiones[]=array_merge($d_ante,array($Now));
}
?>
<div style="padding:5px; width:1200;background-color:white; overflow:auto; border:1px solid black; border-radius:2px;">
	<div id="example4.2"></div>
</div>

<script>
	google.charts.setOnLoadCallback(drawChart);
	function drawChart() {
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
					list($caja, $version, $start,$end)=$d;
					echo "[ 'Caja ".$caja."','".$version."',new Date(".$start."),new Date(".$end.") ],";
				}
			?>
		]);
		var options = {
			timeline: { fontSize: 8 }
		};

		chart.draw(dataTable, options);
	}
	
</script>
