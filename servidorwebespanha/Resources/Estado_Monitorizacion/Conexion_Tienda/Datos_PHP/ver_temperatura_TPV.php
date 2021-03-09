<?php
$Res=$con_tda->cmdExec("sensors");

echo FIELDSET_DATOS("TEMPERATURA DE TRABAJO DE LA TPV",$Res);
?>