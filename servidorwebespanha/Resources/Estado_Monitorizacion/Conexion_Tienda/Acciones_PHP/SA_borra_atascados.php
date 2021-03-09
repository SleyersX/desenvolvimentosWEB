<?php
if (isset($Opcion_Borrado_Ficheros)) {
	$Orig=$DOCUMENT_ROOT.$DIR_RAIZ."/Estado_Monitorizacion/Conexion_Tienda/scripts/fix_ciss.sh";
	$Dest="/root/fix_ciss.sh";

echo '<script>Desbloqueo();</script>';
echo '<div class="Aviso">
	<table>
		<tr><td>Sending tool...  <td><td><span id="p1"></span></td></tr>';
		$con_tda->putFile_2($Orig,$Dest, "p1");
echo '	<tr><td>Executing tool... <td>';
		$Res=$con_tda->cmdExec("cd /root; [ ! -f fix_ciss.sh ] && echo 10; sh fix_ciss.sh; echo $?");
echo '<td><span id="p2">Hecho!</span></td></tr>
	</table>
	<p><b>';

switch ($Res) {
	case 0: echo "<font color=blue>Operation successful. Files erased." ; break;
	case 1: echo "<font color=red>No se ha podido crear el directorio de soporte para CISS."; break;
	case 2: echo "<font color=red>La ruta de soporte no se corresponde con un directorio."; break;
	case 3: echo "<font color=red>No se pudo mover el fichero al directorio de soporte."; break;
	case 4: echo "<font color=red>Error en CISS (java exception)."; break;
	case 5: echo "<font color=red>No hay certificado produccion. Hacer BAT 126.."; break;
	case 6: echo "<font color=pink>WSD no estaba funcionando. Ha sido lanzado y hay que esperar (entre 2 y 3 minutos) para comprobar si se procesan los mensajes mensajes pendientes."; break;
	case 7: echo "<font color=red>Error de conexión con CISS. No se puede enviar el fichero a CISS. Esto  puede deberse a un error de conexion o a que CISS este desconectado o a que el CWS esté caído."; break;
	case 8: echo "<font color=red>La version de la TPV no es valida. Esta herramienta es para SA31."; break;
	default: echo "<font color=red>Error en la ejecucion de la herramienta";
}
echo "</font></b></p>";

} else {
	echo '
	<div class="Aviso" style="width: 80%; ">
		<p>Esta opci&oacute;n permite eliminar los ficheros atascados de CISS de la TPV.</p>
		<input type=submit name=Opcion_Borrado_Ficheros value="Borrar ficheros"/>
	</div>';
}
?>