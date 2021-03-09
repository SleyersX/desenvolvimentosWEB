<?php
echo FIELDSET_DATOS($myDatos,$con_tda->cmdExec('cat /proc/tty/driver/serial'));
?>