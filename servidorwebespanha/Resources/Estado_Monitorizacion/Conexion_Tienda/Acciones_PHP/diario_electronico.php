<script language="javascript">
	function BUSCA_DE(Valor) {
		INPUT_HIDDEN('Busca_DE','true','myForm');
		INPUT_HIDDEN('myAcciones','<?php echo $myAcciones; ?>','myForm');
		SUBMIT('myForm');
	}
	function FICHERO_DE(Valor, Opcion) {
		INPUT_HIDDEN('Fichero_DE',Valor,'myForm'); INPUT_HIDDEN('myAcciones',Opcion,'myForm'); SUBMIT('myForm');
	}
</script>

<?php

if (isset($Fichero_DE)) {
	$tmp_file = $DIR_TMP.$Tienda."-".$Caja."-".basename($Fichero_DE);
	$local_file = $DOCUMENT_ROOT.$tmp_file;
	$con_tda->receiveFile($Fichero_DE, $local_file);
	_FLUSH();
	$Busca_DE="true";
}

_ECHO('
<fieldset id="Menu_DE" class="FONT_HELV"><legend>MENU DE OPCIONES DIARIO ELECTRONICO</legend>
	<table><tr>
		<td width="30%">
			Texto a buscar: <input type="text" name="Texto_A_Buscar" id="Texto_A_Buscar" value=""/><br>
			Filtrar por fecha <input type=date name="Fecha_DE"/>
		</td>
		<td width="10%">
			<input class="button" type="submit" name="Busqueda_DE" value="Buscar" onclick="BUSCA_DE();"/>
		</td>
		<td width="60%">
			<span class="Hint">
				<b>Nota:</b><br>
				- Se puede realizar la busqueda por ambos criterios (texto + fecha).<br>
				- Recuerde que el texto a buscar debe ir in mayusculas.<br>
				- Deje ambos campos en blanco para ver todos los ficheros de D. Electronico de la caja.</span>
		</td>
	</tr></table>
</fieldset>');

_ECHO('<fieldset id="f_DE" class="FONT_HELV" style="width:380px; height:700px;"><legend>Resultado de busqueda</legend>
		<table><tr>
		<td width="50%"><h3>Criterios de busqueda:</h3>');

if ($con_tda->SA==1) {
	$DIR="/confdia/DE/";
	$Filtro="D_E_".@$Fecha_DE."*";
}
else {
	$DIR="/usr/local/n2a/var/data/devices/electronicJournal/";
	if ($con_tda->caja>1)
		$Filtro="DiarioElectronico.log history/D_E_*.log";
	else
		$Filtro="DiarioElectronico.log history/D_E_?????????????0".$con_tda->caja."???.log";
}

if (!empty($Busca_DE)) {
	if ( empty($Texto_A_Buscar)) {
		_ECHO("- Todo el fichero.<br>");
		$Texto_A_Buscar="";
	} else {
		_ECHO("- Texto a buscar '<i>$Texto_A_Buscar</i>'<br>");
	}

	if (empty($Fecha_DE)) {
		_ECHO("- Todos los ficheros.<br>");
	} else {
		$Fecha_DE=str_replace("-","",$Fecha_DE);
		$Mascara=($con_tda->SA==1?"D_E_%08d%05d%1d*.log":"D_E_%08d%05d%02d*.log");
		if ($Fecha_DE===date('Ymd')) { 
			_ECHO("- Fichero de hoy");
			$Filtro=($con_tda->SA==1?sprintf($Mascara, $Fecha_DE, $con_tda->tienda,$con_tda->caja):"DiarioElectronico.log");
		} else {
			_ECHO("- Fichero con fecha '$Fecha_DE'.<br>");
			$Filtro=($con_tda->SA==0?"history/":"").sprintf($Mascara, $Fecha_DE, $con_tda->tienda, $con_tda->caja);
		}
	}
} else {
	_ECHO("No se han establecido criterios.");
}
_ECHO('</td><td width="50%">
	<span class="Hint">
	<b>LEYENDA:</b><br>
	Pulse <img style="width:13; height:13" src="'.$ICONOS['Download_Server'].'"/> para descargar el fichero al servidor.<br>
	Pulse <img style="width:13; height:13" src="'.$ICONOS['Recargar'].'"/> si el fichero esta ya descargado y necesita recargarlo de nuevo.<br>
	Pulse <img style="width:13; height:13" src="'.$ICONOS['Lupa'].'"/> para visualizar el fichero descargado.
	</span>
	</td></tr></table>');
	
_ECHO("<hr>");
_ECHO("<span  id='Barra'></span>");

_ECHO('<div id="div_multi" class="OF" style="height:580px;">');

_ECHO("<table><tr>");
_ECHO('<td width="45%" valign="top">');
_ECHO("<center><h3>Ficheros descargados</h3>");

$Result=shell_exec("cd $DOCUMENT_ROOT/$DIR_TMP; ls -lta ".sprintf("%05d",$con_tda->tienda)."-".$con_tda->caja."* | awk '{printf \"%s#%dKB\\n\",\$9,\$5/1024}' | cut -f3 -d'-'");
if (empty($Result)) { _ECHO(Alert("warning","No hay ficheros locales <br>descargados...")); }
else {
	_ECHO("<table id='lista_de_remoto' class='lista_ficheros' style='text-decoration:none;'>");
	_ECHO("<thead><tr><th>Fichero</th><th>Tamanio</th><th>Opciones</th></tr></thead>");
	$DE_Local=explode("\n",$Result);
	foreach ($DE_Local as $d) {
		if (empty($d)) continue;
		@list($File, $Size) = explode("#",$d);
		$tmp_file=sprintf("%s%05d-%d-%s",$DIR_TMP,$Tienda,$Caja,basename($File));
		$local_file=$DOCUMENT_ROOT.$tmp_file;
		_ECHO("<tr><td id='td_Fichero'>".basename($File)."</td>");
		_ECHO("<td id='td_Tamanio'>".$Size."</td>");
		_ECHO('<td id="td_Opciones">');
		_ECHO('<a class="button b_download" href="ver_log_2.php?file='.$local_file.'&Tipo=DE" target="_blank" title="Ver fichero"><img src="'.$ICONOS['Lupa'].'"/></a>');
		_ECHO("</td>");
		_ECHO("</tr>");
	}
	_ECHO("</center></table>");
}

_ECHO('</td>');

_ECHO('<td width="45%" valign="top">');

_ECHO("<center><h3>Ficheros remotos</h3>");

$cmd="
cd $DIR;
RES=\$(grep -cH \"".@$Texto_A_Buscar."\" $Filtro | grep -v \":0\" | cut -f1 -d':' | sort -u);
[ ! -z \"\$RES\" ] && ls -lta \$RES | awk '{printf \"%s#%dKB\\n\",\$9,\$5/1024}'";

// 	_ECHO($cmd);
$Result=$con_tda->cmdExec($cmd);
if (empty($Result)) { _ECHO(Alert("warning","No se encontraron coincidencias...")); }
else {
	_ECHO("<table id='lista_de_remoto' class='lista_ficheros' style='text-decoration:none;'>");
	_ECHO("<thead><tr><th>Fichero</th><th>Tamanio</th><th>Opciones</th></tr></thead>");
	$DE=explode("\n",$Result);
	foreach ($DE as $k => $d) {
		if (!empty($d)) {
			list($File, $Size) = explode("#",$d);
			$tmp_file=sprintf("%s%05d-%d-%s",$DIR_TMP,$Tienda,$Caja,basename($File));
			$local_file=$DOCUMENT_ROOT.$tmp_file;
			_ECHO("<tr><td id='td_Fichero'>".basename($File)."</td>");
			_ECHO("<td id='td_Tamanio'>".$Size."</td>");
			_ECHO('<td id="td_Opciones">');

			// BOTON DE DESCARGA A SERVIDOR
			$OnClick = "FICHERO_DE('".$DIR.$File."','$myAcciones')";
			if (file_exists($local_file)) $Icono=$ICONOS['Recargar']; else $Icono=$ICONOS['Download_Server'];
			_ECHO('<a id="Descargar_Server" class="button b_download" href="javascript:{}" onclick="javascript:'.$OnClick.';" title="Descargar fichero al servidor"><img src="'.$Icono.'"/></a>');
			if (file_exists($local_file)) 
				_ECHO('<a class="button b_download" href="ver_log_2.php?file='.$local_file.'&Tipo=DE" target="_blank" title="Ver fichero"><img src="'.$ICONOS['Lupa'].'"/></a>');

			_ECHO("</td>");
			_ECHO("</tr>");
		}
	}
}
_ECHO("</center></table>");
_ECHO("</td>");
_ECHO("</tr></table>");
_ECHO("</div>");
_ECHO('</fieldset>');

// if (!SoyYo()) _ECHO('
// <script language="javascript">
// 	Ajustar_Altura("div_multi");
// </script>');
?>