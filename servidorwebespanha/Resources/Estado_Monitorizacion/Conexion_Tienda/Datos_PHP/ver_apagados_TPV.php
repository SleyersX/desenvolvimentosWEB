<?php
$Res=$con_tda->cmdExec("mysql n2a -e \"select BEGIN_DATE 'FECHA APAGADO\n-------------------' from TRANSACTION where TRANSACTION_TYPE_ID = 31 and WORKSTATION_ID = ".$con_tda->caja."\"");

echo FIELDSET_DATOS("APAGADOS DE TPV   ",$Res);
?>