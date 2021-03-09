<?php
$file="listado_formas_pago.sh";
$cmd=file_get_contents("scripts/$file");
// echo $cmd;
$Listado = $con_tda->cmdExec($cmd);
$Res="<pre>".$Listado."</pre>";
echo FIELDSET_DATOS($myListados." ".$Repetir_Listado,$Res);
?>