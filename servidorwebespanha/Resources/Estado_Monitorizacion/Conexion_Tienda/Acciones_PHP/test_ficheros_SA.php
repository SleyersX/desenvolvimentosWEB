<?php

$Fich_Test=$con_tda->cmdExec("cd /confdia/bin; ls tsfd3* | sort -r | head -1");

_ECHO($con_tda->cmdExec("cd /confdia/bin; $Fich_Test 0 0"));


?>