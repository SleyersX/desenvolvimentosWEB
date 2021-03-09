<script language="javascript">
	function ACTIVA_OPCION(Etiqueta, Valor) {
		INPUT_HIDDEN(Etiqueta, Valor, 'myForm');
		INPUT_HIDDEN('myAcciones','<?php echo $myAcciones; ?>','myForm'); SUBMIT('myForm');
	}
</script>

<?php

if ($con_tda->caja != 1) {
	echo '<script>javascript:Desbloqueo();</script>';
	echo Alert("warning","SOLO SE PERMITE DESCARGAR BBDD DE CAJAS MASTER DESDE ESTA HERRAMIENTA");
	die();
}

$DIR_ACTUAL="/confdia/backup/";
$DIR_OTROS="/confdia/logscomu/";

if (@$Fichero_BBDD) {
	$tmp_file = $DIR_TMP.$Tienda."-".$Caja."-".basename($Fichero_BBDD);
	$local_file = $DOCUMENT_ROOT.$tmp_file;

	if (basename($Fichero_BBDD) === "BBDD_Actual.tgz")
		$con_tda->BBDD_Actual($local_file);
	else 
		$con_tda->receiveFile($Fichero_BBDD, $local_file);
	flush(); @ob_flush();
}

$Result=$con_tda->cmdExec("cd $DIR_OTROS; ls -lsita *Backup* | awk '{printf \"%s#%dKB\\n\",\$11,\$7/1024}';");
$Lista_Ficheros=explode("\n",$Result);
$Lista_Ficheros[]="BBDD_Actual.tgz#N/A";
rsort($Lista_Ficheros);

$Res="<table id='lista_bbdd' class='lista_ficheros' style='text-decoration:none;'>   <thead><tr><th>Fichero</th><th>Tamanio</th><th>Opciones</th></tr></thead>   <tbody>";

foreach ($Lista_Ficheros as $k => $d) {
	if (!empty($d)) {
		list($File, $Size) = explode("#",$d);
		$tmp_file=$DIR_TMP.$Tienda."-".$Caja."-".$File;
		$local_file=$DOCUMENT_ROOT.$tmp_file;
		$OnClick = "ACTIVA_OPCION('Fichero_BBDD','$DIR_OTROS$File')";
		$Res.="<tr><td id='td_Fichero'>".basename($File)."</td>";
		$Res.="<td id='td_Tamanio'>".$Size."</td>";
		$Res.='<td id="td_Opciones">';
		if (file_exists($local_file)) $Icono="recargar.png"; else $Icono="download_to_server.gif";
		$Res.='<a class="button b_download" onclick="'.$OnClick.'" title="Descargar fichero al servidor"><img src="'.$DIR_IMAGE.'/'.$Icono.'"/></a>';
		if (file_exists($local_file)) {
			$Res.='<a class="button b_download" href="'.$tmp_file.'" title="Descargar a PC" target="_blank"><img src="'.$DIR_IMAGE.'/download_to_pc.gif" /></a>';
			$OnClick = "ACTIVA_OPCION('Descarga_USB','$tmp_file')";
			$Res.='<a class="button b_download" href="bbdd_to_usb_sa.php?FILE='.$tmp_file.'" title="Grabar a USB" target="_blank"><img src="'.$DIR_IMAGE.'/i1/media-flash.png" /></a>';
		}
		$Res.="</td>";
		$Res.="</tr>";
	}
}
$Res.="</table>";
echo FIELDSET_DATOS("DESCARGA DE BBDDs - SA",$Res);
?>
