<?php
$file="listado_concesiones.sh";
$cmd=file_get_contents("scripts/$file");
$Sub_Actual="list_concesion";
$Item = "CONCESION";

if (@$Subaction!=$Sub_Actual) {
	$Res=Desde_Hasta($Item, $Sub_Actual, $myListados);
} else {
	$Listado = $con_tda->cmdExec("D='$Desde'; H='$Hasta'; $cmd");
	$Res="<pre>".$Listado."</pre>";
}
echo FIELDSET_DATOS($myListados." ".$Repetir_Listado,$Res);
?>