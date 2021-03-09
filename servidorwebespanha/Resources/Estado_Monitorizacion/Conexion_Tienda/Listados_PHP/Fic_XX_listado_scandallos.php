<?php
$file="listado_relacion_padre_hijo.sh";
$cmd=file_get_contents("scripts/$file");

if ($con_tda->VELA) {
	echo '
	<div class="Aviso Aviso_New" style="font-size:15px; margin-top:50px;">
		<h3>OPCION NO DISPONIBLE - TIENDA CONFIGURADA CON BACKOFFICE VELA</h3>
		<p>
			Esta opci&oacute;n est&aacute; deshabilitada, porque en TPV no se mantienen estos datos.<br>
			<b>Revisar en VELA.</b>
		</p>
	</div>
	';
} else {
	$Listado = utf8_decode($con_tda->cmdExec("$cmd"));
	// $Tabla=explode("\t",$Listado); var_dump($Tabla);
	$Res="<h1>Listado de Scandallos</h2><pre>".$Listado."</pre>";

	echo FIELDSET_DATOS($myListados." ".$Repetir_Listado,$Res);
}
?>