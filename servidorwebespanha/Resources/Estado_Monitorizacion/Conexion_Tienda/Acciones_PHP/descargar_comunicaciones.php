<script language="javascript">
	function ACTIVA_OPCION(Etiqueta, Valor) {
		INPUT_HIDDEN(Etiqueta, Valor, 'myForm');
		INPUT_HIDDEN('myAcciones','<?php echo $myAcciones; ?>','myForm'); SUBMIT('myForm');
	}
</script>

<?php
if ($con_tda->caja != 1) {
	echo '<script>javascript:Desbloqueo();</script>';
	echo Alert("warning","SOLO SE PERMITE DESCARGAR COMUNICACIONES DE CAJAS MASTER DESDE ESTA HERRAMIENTA");
} else {

switch (@$Pais) {
	case "ESP":
		$DIR_IN="/usr/local/n2a/var/data/communications/historyIn";
		$DIR_OUT="/usr/local/n2a/var/data/communications/historyOut";
		break;
	case "POR":
	case "ARG":
	case "BRA":
		$DIR_IN="/confdia/logscomu/";
		$DIR_OUT="/confdia/logscomu/";
		break;
}

if ($con_tda->caja != 1) {
	echo '<script>javascript:Desbloqueo();</script>';
	die(Alert("warning","SOLO SE PERMITE DESCARGAR BBDD DE CAJAS MASTER DESDE ESTA HERRAMIENTA"));
}

if (@$Fichero_BBDD) {
	$tmp_file = $DIR_TMP.$Tienda."-".$Caja."-".basename($Fichero_BBDD);
	$local_file = $DOCUMENT_ROOT.$tmp_file;

	if (basename($Fichero_BBDD) === "BBDD_Actual.tgz")
		$con_tda->BBDD_Actual($local_file);
	else 
		$con_tda->receiveFile($Fichero_BBDD, $local_file);
	flush(); @ob_flush();
}

if ($con_tda->SA == 1)
	$Result=$con_tda->cmdExec("ls -lsita $DIR_IN/*_Comms*.tgz | awk '{printf \"%s#%dKB\\n\",\$11,\$7/1024}';");
else 
	$Result=$con_tda->cmdExec("ls -lsita $DIR_IN/* $DIR_OUT/* | awk '{printf \"%s#%dKB\\n\",\$11,\$7/1024}';");

$Lista_Ficheros=explode("\n",$Result);
// rsort($Lista_Ficheros);

_ECHO(utf8_decode('
<div class="Hint Hint2" style="left:50%; top:20%; width:480;">
	<h2>AYUDA:</h2>
	<ul>
		<li>Pulse <img style="width:13; height:13" src="'.$ICONOS['Download_Server'].'"/> para descargar el fichero al servidor.</li>
		<li>Pulse <img style="width:13; height:13" src="'.$ICONOS['Recargar'].'"/> si el fichero esta ya descargado y necesita recargarlo de nuevo.</li>
		<li>Pulse <img style="width:13; height:13" src="'.$ICONOS['To_PC'].'"/> para grabar el fichero descargado al PC.</li>
	</ul>
	<hr>
	<p>Los ficheros est&aacute;n ordenados los m&aacute;s recientes primero.</p>
	<p>Los ficheros en azul, son ficheros emitidos.<br>Los ficheros en negro, son ficheros recibidos</p>
	<p><i><b>NOTA:</b> los ficheros de gran tamaño pueden tardar varios minutos en la transferencia.</i></p>
	<hr>
	<p style="font-size:80%">
		<b>INFORMACION SOBRE LOS FICHEROS:</b><br><b>VGZ</b>: Ventas.<br><b>SGZ</b>: Fidelizacion.<br><b>LGZ</b>: Diario Electronico.<br>
		<p style="font-size:80%"><b>NOTA</b>: Se pueden incorporar directamente los ficheros, pero renombrando como AXXXXXBB.CCC, siendo XXXXX el codigo de tienda, BB un numero entre 00 y 99 y CCC la extension adecuada (VGZ, SGZ, LGZ, etc).</p>
	</p>
</div>'));

$Res="<table id='lista_bbdd' class='lista_ficheros' style='text-decoration:none;'>   <thead><tr><th>Fichero</th><th>Tamanio</th><th>Opciones</th></tr></thead>   <tbody>";
foreach ($Lista_Ficheros as $k => $d) {
	if (!empty($d)) {
		list($File, $Size) = explode("#",$d);
		$tmp_file=$DIR_TMP.$Tienda."-".$Caja."-".basename($File);
		$local_file=$DOCUMENT_ROOT.$tmp_file;
		$OnClick = "ACTIVA_OPCION('Fichero_BBDD','$File')";
		$Res.="<tr>";
		if (preg_match("/^A|CommsGN/",basename($File))) $FONT="<font color='blue'>"; else $FONT="";
		$Res.="<td id='td_Fichero'>".$FONT.basename($File)."</td>";
		$Res.="<td id='td_Tamanio'>".$FONT.$Size."</td>";
		$Res.='<td id="td_Opciones">';
		if (file_exists($local_file)) $Icono="recargar.png"; else $Icono="download_to_server.gif";
			$Res.='<a class="button b_download" onclick="'.$OnClick.'" title="Descargar fichero al servidor"><img src="'.$DIR_IMAGE.'/'.$Icono.'"/></a>';
		if (file_exists($local_file)) {
			$Res.='<a class="button b_download" href="'.$tmp_file.'" title="Descargar a PC" target="_blank"><img src="'.$DIR_IMAGE.'/download_to_pc.gif" /></a>';
// 			$OnClick = "ACTIVA_OPCION('Descarga_USB','$tmp_file')";
// 			$Res.='<a class="button b_download" href="bbdd_to_usb_sa.php?FILE='.$tmp_file.'" title="Grabar a USB" target="_blank"><img src="'.$DIR_IMAGE.'/i1/media-flash.png" /></a>';
		}
		$Res.="</td>";
		$Res.="</font></tr>";
	}
}
$Res.="</table>";

echo FIELDSET_DATOS("DESCARGA DE FICHEROS DE COMUNICACION",$Res);
}
?>
