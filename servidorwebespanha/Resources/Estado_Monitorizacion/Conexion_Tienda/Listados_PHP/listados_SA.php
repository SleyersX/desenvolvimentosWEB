<script language="javascript">
	function Pon_Opcion_Listado(Opc) {
		INPUT_HIDDEN('Opc_Listado',Opc,'myForm');
		INPUT_HIDDEN('myListados','Listados SA','myForm');
		SUBMIT('myForm');
	}
</script>

<?php

if (isset($Opc_Listado)) {
	$Res='<a class="button" style="text-decoration:none;" href="javascript:{}" onclick="INPUT_HIDDEN(\'myListados\',\'Listados SA\',\'myForm\'); SUBMIT(\'myForm\');">Otro listado</a>  ';
// 	$Res.='<a class="button" style="text-decoration:none;" href="javascript:{}" onclick="INPUT_HIDDEN(\'myListados\',\'Listados SA\',\'myForm\');SUBMIT(\'myForm\');">Salvar a fichero...</a><br>';
	_ECHO("<script>Desbloqueo();</script>");
	_ECHO("<div class='Aviso' style='width:80%'>");
	_ECHO("<p>Building information requested list <i>(this can take several minutes depending on the size of the list)</i>... ");
		$con_tda->cmdExec("cd /confdia/bin; . ./functions; ./miraBD $Opc_Listado > /tmp/list_$Opc_Listado.tmp");
	_ECHO("Done!</p>");
	$local_file=sprintf("%s/%05d-%02d-%s", $_SESSION['DIR_TMP'],$con_tda->tienda, $con_tda->caja,"list_$Opc_Listado.csv");
	$con_tda->Get_File_URL("/tmp/list_$Opc_Listado.tmp", $local_file);
	_ECHO("</div>");

	$tmp = file_get_contents($DOCUMENT_ROOT.$local_file);
	$Resultado=explode("\n",$tmp);
	$Res.="<table id='listado_sa' class='lista_ficheros' style='text-decoration:none;'>";
	foreach($Resultado as $k => $d) {
		$Linea = preg_split("/\|/", $d); 
		if ($k == 0) {
			$Res.="<thead><tr>"; foreach ($Linea as $k => $d) $Res.="<th>$d</th>"; $Res.="</tr></thead>";
		} else {
			$Res.="<tr>"; foreach ($Linea as $k => $d) $Res.="<td>$d</td>"; $Res.="</tr>";
		}
	}
	$Res.="</table>";
	_ECHO("<div class='Aviso' style='width:950px; height:600px; overflow:auto;'>$Res</div>");
} else {
	$tmp=$con_tda->cmdExec("cd /confdia/bin; . ./functions; ./miraBD -v | grep \"Vista \"");
	$Lista_Listados=explode("\n",$tmp);

	foreach($Lista_Listados as $k => $d) {
		@list($p1, $p2, $p3) = explode(":", $d); @list($null, $opc) = explode(" ",$p1);
		@$Res.='<a href="javascript:{}" onclick="Pon_Opcion_Listado(\''.$opc.'\');">'.$d.'</a><br>';
	}
	_ECHO("<div class='Aviso' style='width:90%; height:750px; overflow:auto;'>$Res</div>");
}
// echo FIELDSET_DATOS($myListados,$Res);
?>