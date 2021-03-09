<head>
	<link rel="stylesheet" type="text/css" href="/Resources/css/tabla2.css">
</head>

<?php
$Altura_Ventas=710; $Altura_Grafica=$Altura_Ventas*30/100; $Altura_Items=$Altura_Ventas-$Altura_Grafica-30;
$Ancho_Total=1000;  $Ancho_Ventas=$Ancho_Total*25/100;     $Ancho_Otros=$Ancho_Total-$Ancho_Ventas-2;

$url_local="Datos_PHP/".basename(__FILE__);

if (!empty($_GET["opcion_usb"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();
//	require_once("/home/soporteweb/tools/mysql.php");
	foreach($_GET as $k => $d) $$k=$d;
	switch($opcion_usb) {
		case "lsusb":
			$cmd='sudo ssh2 '.$Tienda.' '.$Caja.' "lsusb | sort | tr \'\n\' \'#\'"';
			$ret=shell_exec($cmd); $lsusb = explode("#",$ret);
			echo "<table class='tabla2' style='font-size:1em;'><tr><th>BUS-Device</th><th>Dispositivo</th></tr>";
			foreach($lsusb as $d) {
				@list($bus,$info,$info2) = explode(":",$d);
				if (!empty($bus)) {
					if (preg_match("/0000/",$info2)) {
						$info2.=" - NADA CONECTADO";
						$clase="style='color:red'";
					}
					echo "<tr $clase><td>$bus</td><td>".$info.":".$info2."</td></tr>";
				}
			}
			echo "</table>";
			exit;
			break;

		case "log_guc":
			$cmd='sudo ssh2 '.$Tienda.' '.$Caja.' "cd /usr/share/guc/log/; tac \$(ls -ra | head -1)"';
			$ret=shell_exec($cmd);
			echo "<pre>".$ret."</pre>";
			exit;
			break;

	}
//	@mysqli_close($db);
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
	#info_lsusb { width: 100%; height: 300; border:none;}
	#log_guc { width: 100%; height: 400; border:none;}
	.format_td { border:1px solid blue; border-radius:2px; }
</style>

<fieldset>
	<legend>Check de dispositivos USB</legend>
	<table>
		<tr>
			<td class="format_td">
				<input type="button" id="b_recargar_usb" value="Recargar" title="Pulse aqu&iacute; para recargar la informaci&oacute;n."/>
			</td>
		</tr>
		<tr>
			<td class="format_td">
				<h3>Informacion de USB conectados y registrados en estos momentos en la caja:</h3>
				<iframe id="info_lsusb" src=""></iframe>
			</td>
		</tr>
		<tr>
			<td class="format_td">
				<h3>Trazas de conexiones de dispositivos USB (trazas ordenadas por m&aacute;s reciente):</h3>
				<iframe id="log_guc" src=""></iframe>
			</td>
		</tr>
	</table>
</fieldset>

<script>
	var url_local="<?php echo $url_local; ?>";
//	var IP_Tienda="<?php echo $con_tda->GetIP(); ?>";
	var Tienda="<?php echo $Tienda; ?>";
	var Caja="<?php echo $Caja; ?>";
	Desbloqueo();

	var url_lsusb   = url_local + "?opcion_usb=lsusb&Tienda="+Tienda+"&Caja="+Caja;
	var url_logguc = url_local + "?opcion_usb=log_guc&Tienda="+Tienda+"&Caja="+Caja;
	$("#info_lsusb").attr("src",url_lsusb);
	$("#log_guc").attr("src",url_logguc);

	$("#b_recargar_usb").on("click",function () {
		$("#info_lsusb").attr("src",url_lsusb);
		$("#log_guc").attr("src",url_logguc);
	})
</script>
