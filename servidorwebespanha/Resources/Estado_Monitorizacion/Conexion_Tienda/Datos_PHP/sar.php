<?php
$COMANDO='sar -P 0 | grep ^..:..:.. | grep -v \"LIN\|CPU\"';
echo FIELDSET_DATOS("INFORMACION TRABAJO CPUs (sar -P 0)",$con_tda->cmdExec($COMANDO));
?>