<script language="javascript">
	function QUE_LOG_APP(Valor) {
		INPUT_HIDDEN('Que_Log',Valor,'myForm');
		INPUT_HIDDEN('myAcciones','<?php echo $myAcciones; ?>','myForm');
		SUBMIT('myForm');
	}
</script>

<?php

if (SoyYo()) {
	require_once("./Acciones_PHP/ver_logs_aplicacion_2.php");	
}
else {
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

if ($con_tda->SA==1) {
	echo '<fieldset id="fd_1" ><legend>LOGS DE TPV (Version SA)</legend>';
	echo '<p>LISTA DE LOGS:<ul>';
	echo '<li><a href="javascript:{}" onclick="Show_DIV(\'div_Periferia\');">Log de periferia / Devices info log.</a>';
		echo "<span id=\"div_Periferia\" style=\"display: none\">";
		echo "<br>Introduzca la fecha y pulse Aceptar:";
		echo "<input type=date name=Fecha_Periferia />";
		echo "<input type=button onclick=\"QUE_LOG_APP('Descarga_Periferia');\" value='Aceptar' />";
		echo "</span></li>";
	echo '<li><a href="javascript:{}" onclick="QUE_LOG_APP(\'error_log\')">Log de aplicacion / Application log (error.log).</a></li>';
	echo '<li><a href="javascript:{}" onclick="QUE_LOG_APP(\'messages\')">Log de sistema / System log (/var/log/messages).</a></li>';
	echo '<li><a href="javascript:{}" onclick="QUE_LOG_APP(\'comunication\')">Log de comunicaciones / Communications log (comunicacion.log).</a></li>';
	echo '</ul></p>';
	echo '</fieldset>';
	echo "<div id='div_descargando_informacion' >
			<h1>Espere por favor...</h1>
			<p>Descargando ficheros solicitados</p>
			<span id='res_descarga_ficheros'></span></div>";
	echo '<script>
			Desbloqueo();
 			$("#div_descargando_informacion").dialog({autoOpen: false, modal: true, width: "auto", height: 400, resizable: false });
		</script>';
	echo '<fieldset><legend>Resultados</legend>';
	echo '<div id="logs_periferia">';
	echo '<pre>';
	switch (@$Que_Log) {
		case "Descarga_Periferia":
			if (empty($Fecha_Periferia)) { _ECHO("You must select a date!!."); die(); }
			// VARIABLES LOCALES.
			$F_Periferia="/tmp/periferia.log";
			$tmpFecha=str_replace("-","",$Fecha_Periferia);	
			_ECHO("<h2>Searching info for selected date '$tmpFecha'</h2>");
			_ECHO("<p>Get information from POS::<br>");
			_ECHO("<ul>");

			_ECHO("<li>Setting up enviroment... ");
			_ECHO($con_tda->cmdExec("rm $F_Periferia* -f")."OK</li>");

			_ECHO("<li>Searching cashier display info... ");
				$cmd="(zcat /confdia/backup/cajera.log.gz.*; cat /confdia/backup/cajera.log.? /var/log/cajera.log) | grep $tmpFecha >> $F_Periferia";
				$con_tda->cmdExec($cmd); 
			_ECHO(" Done!</li>");

			_ECHO("<li>Searching keyboard info... ");
				$cmd="(zcat /confdia/backup/teclado.log.gz.*; cat /confdia/backup/teclado.log.? /var/log/teclado.log) | grep $tmpFecha >> $F_Periferia";
				$con_tda->cmdExec($cmd); 
			_ECHO("Done!</li>");

			_ECHO("<li>Searching printer info... ");
				$cmd="(zcat /confdia/backup/impresora.log.gz.*; cat /confdia/backup/impresora.log.? /var/log/impresora.log) | grep $tmpFecha >> $F_Periferia";
				$con_tda->cmdExec($cmd); 
			_ECHO("Done!</li>");

			_ECHO("<li>Searching scanner info... ");
				$cmd="(zcat /confdia/backup/scanner.log.gz.*; cat /confdia/backup/scanner.log.? /var/log/scanner.log) | grep $tmpFecha >> $F_Periferia";
				$con_tda->cmdExec($cmd); 
			_ECHO("Done!</li>");

			_ECHO("</ul></p>");

			$cmd="echo -n \$(wc -l $F_Periferia | awk '{print $1}')";
			$LineResu=$con_tda->cmdExec($cmd);
			_ECHO("<p>Result: $LineResu lines</p>");

			if ($LineResu == 0)
				_ECHO("<big>RESULTS WERE NOT FOUND!!</big>");
			else {
				$con_tda->cmdExec("sort $F_Periferia > $F_Periferia.srt");
				$local_file=sprintf("%s/%s-%02d-%s-periferia.log", $_SESSION['DIR_TMP'],$con_tda->tienda, $con_tda->caja, $tmpFecha);
				$con_tda->Get_File_URL("$F_Periferia.srt", $local_file);
			}
			break;

		case "error_log":
			$Patron='-name "error.*.log" -o -name "error.log.*" -o -name "error.*.log"';
			_ECHO("<ul>");
				_ECHO("<li>Searching files error.log... ");
				//$con_tda->cmdExec('cd /confdia; cat $(find . -name "error.*.log") > /tmp/error_all.log; zcat $(find . -name "error.*gz*") >> /tmp/error_all.log;');
				$con_tda->cmdExec('cd /confdia; cat $(find . -name "error.*.log") > /tmp/error_all.log;');
				//$con_tda->cmdExec('cd /confdia; cat $(find . '.$Patron.') > /tmp/error_all.log;');
				_ECHO("Done!</li>");

				_ECHO("<li>Making an unique file...");
				$con_tda->cmdExec('cd /tmp; grep "^[0-9]" error_all.log > error_all_grep.log;');
				_ECHO("Done!</li>");

				_ECHO("<li>Sorting result file...");
				$con_tda->cmdExec('cd /tmp; sort error_all_grep.log > error.log;');
				_ECHO("Done!</li>");
			_ECHO("</ul>");
			$local_file=sprintf("%s/%s-%02d-error.log", $_SESSION['DIR_TMP'],$con_tda->tienda, $con_tda->caja);
			$con_tda->Get_File_URL("/tmp/error.log", $local_file);
			break;

		case "messages":
			_ECHO("<ul>");
				_ECHO("<li>Searching files with system messages... ");
				$con_tda->cmdExec('cd /var/log; cat messages.5 messages.4 messages.3 messages.2 messages.1 messages > /tmp/messages.log;');
				_ECHO("Done!</li>");
			_ECHO("</ul>");
			$local_file=sprintf("%s/%s-%02d-messages.log", $_SESSION['DIR_TMP'],$con_tda->tienda, $con_tda->caja);
			$con_tda->Get_File_URL("/tmp/messages.log", $local_file);
			break;

		case "comunication":
			_ECHO("<ul>");
				_ECHO("<li>Searching files comunication.log... ");
				$con_tda->cmdExec('cd /confdia; cat ./backup/comunicacion.log.5 ./backup/comunicacion.log.4 ./backup/comunicacion.log.3 ./backup/comunicacion.log.2 ./backup/comunicacion.log.1 ./ficcaje/comunicacion.log > /tmp/comunicacion.log;');
				_ECHO("Done!</li>");
			_ECHO("</ul>");
			$local_file=sprintf("%s/%s-%02d-comunicacion.log", $_SESSION['DIR_TMP'],$con_tda->tienda, $con_tda->caja);
			$con_tda->Get_File_URL("/tmp/comunicacion.log", $local_file);
			break;

		default;
			break;
	}
	_ECHO("</pre>");
	echo '</div></fieldset>';
}
else {

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
}
?>