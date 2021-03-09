<?php

$Ayuda=Alert("info",utf8_decode('
		<h2>LEYENDA:</h2>
		<p>
		Pulse <img class="size_15" src="'.$ICONOS['Download_Server'].'"/> para descargar el fichero al servidor.<br>
		Pulse <img class="size_15" src="'.$ICONOS['Recargar'].'"/> para descargar de nuevo el fichero al servidor.<br>
		Pulse <img class="size_15" src="'.$ICONOS['Lupa'].'"/> para ver el fichero de log completo (descargado).<br>
		Pulse <img class="size_15" src="'.$ICONOS['Lupa-Edit'].'"/> para filtrar el log con solo la periferia: visor de cajera, teclas, llavero, scanner e impresora.<br>
		<hr>
		<p>Los ficheros est&aacute;n ordenados los m&aacute;s recientes primero.</p>
		<p>En el nombre del fichero aparece una fecha. Esa fecha es cuando se rot&oacute; el fichero, por lo que recordamos que su contenido siempre es de un d&iacute;a antes.</p>
		<p><i><b>NOTA:</b> los ficheros de gran tamaño pueden tardar varios minutos en la transferencia.</i></p>
	'));

$tunel=$con_tda->Abre_Tunel();
$res=glob($tunel."/usr/local/n2a/var/log/n2a_application.log-*",GLOB_BRACE);
rsort($res);
$tabla_ficheros="";
foreach($res as $k => $d) {
	if (file_exists($d)) {
		$tabla_ficheros.="<tr class='fichero'><td>".basename($d)."</td><td>".filesize($d)."</td></tr>";
	}
}

if (0) {

if (isset($Fichero_Log)) {
	$tmp_file = $DIR_TMP.$Tienda."-".$Caja."-".basename($Fichero_Log);
	$local_file = $DOCUMENT_ROOT.$tmp_file;
	$con_tda->receiveFile($Fichero_Log, $local_file);
	_FLUSH();
}

	if (isset($Fichero_Log_Reducido)) {
		$remoto_tmp_file = basename($Fichero_Log_Reducido).'-Reducido';
		$tmp_file = $DIR_TMP.$Tienda."-".$Caja."-".$remoto_tmp_file;
		$local_file = $DOCUMENT_ROOT.$tmp_file;
		$Filtro="OPERATOR.DISPLAY\|Key.press\|Writting.line.to.the.JOURNAL\|S_KEY_POSITION\|scanner.read.this";

		$cmd="zgrep \"$Filtro\" $Fichero_Log_Reducido > /tmp/$remoto_tmp_file";
		$con_tda->cmdExec($cmd,"Extrayendo informacion de la caja...");
		_FLUSH();
		$con_tda->receiveFile("/tmp/$remoto_tmp_file", $local_file);

		echo "<script>window.open('/Resources/Estado_Monitorizacion/Conexion_Tienda/ver_log.php?file=$local_file&Tipo=LOG','_new');</script>";
	}

	$DIR="/usr/local/n2a/var/log/";
	$Result=$con_tda->cmdExec("cd $DIR; ls -ltHa n2a_application.log* | awk '{printf \"%s#%dKB\\n\",\$9,\$5/1024}';");
	$DE=explode("\n",$Result);

	$Res='
	<table>
	<tr>
		<td width="40%">
			<table id="lista_logs" class="lista_ficheros" style="text-decoration:none;">   <thead><tr><th>Fichero</th><th>Tamanio</th><th>Opciones</th></tr></thead>';
	foreach ($DE as $k => $d) {
		if (!empty($d)) {
			list($File, $Size) = explode("#",$d);
			$tmp_file=$DIR_TMP.$Tienda."-".$Caja."-".$File;
			$local_file=$DOCUMENT_ROOT.$tmp_file;
			$OnClick = "INPUT_HIDDEN('Fichero_Log','".$DIR.$File."','myForm'); INPUT_HIDDEN('myAcciones','$myAcciones','myForm'); SUBMIT('myForm');";
			$Res.="<tr><td id='td_Fichero'>".basename($File)."</td>";
			$Res.="<td id='td_Tamanio'>".$Size."</td>";
			$Res.='<td id="td_Opciones">';

			// FICHERO NORMAL
			if (file_exists($local_file)) $Icono="recargar.png"; else $Icono="download_to_server.gif";
			$Res.='<a class="button b_download" onclick="'.$OnClick.'" title="Descargar fichero al servidor"><img src="'.$DIR_IMAGE.'/'.$Icono.'"/></a>';
			if (file_exists($local_file)) {
				$Res.='<a class="button b_download" href="ver_log.php?file='.$local_file.'&Tipo=LOG" target="_blank" title="Ver fichero"><img src="'.$DIR_IMAGE.'/lupa.png"/></a>';
				$Res.='<a class="button b_download" href="'.$tmp_file.'" title="Descargar a PC" target="_blank"><img src="'.$DIR_IMAGE.'/download_to_pc.gif" /></a>';
			}

			// FICHERO REDUCIDO
			$local_file.='-Reducido';
			$Icono="i1/edit-find.png";
			$OnClick = "INPUT_HIDDEN('Fichero_Log_Reducido','".$DIR.$File."','myForm'); INPUT_HIDDEN('myAcciones','$myAcciones','myForm'); SUBMIT('myForm');";
			$Res.='<a class="button b_download" onclick="'.$OnClick.'" title="Ver log solo periferia"><img src="'.$DIR_IMAGE.'/'.$Icono.'"/></a>';

			$Res.="</td>";
			$Res.="</tr>";
		}
	}
	$Res.='</table>
	</td>
	<td valign="top">'.$Ayuda.'</td>
	<tr>
	</table>';
	echo FIELDSET_DATOS("LOGS DE LA APLICACION",$Res);

}
?>

<div class="PANEL">
	<table class='TABLA2' style='width:300px'>
		<thead><tr><th>Fichero</th><th>Size</th></tr>
		<?php echo @$tabla_ficheros; ?>
	</table>
</div>
<div class="PANEL" id="stats_fichero">
</div>

<script>
	Desbloqueo();
	$(".fichero").on("click",function () {
		$("#stats_fichero").load("/Resources/Estado_Monitorizacion/Conexion_Tienda/Acciones_PHP/ver_logs_aplicacion_2.php?opcion=stats_fichero");
	});
</script>