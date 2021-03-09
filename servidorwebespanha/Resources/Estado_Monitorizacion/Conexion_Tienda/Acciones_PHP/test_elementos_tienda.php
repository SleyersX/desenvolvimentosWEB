<?php
echo '<script>javascript:Desbloqueo();</script>';
_ECHO('<div class="Aviso">
	<h2>Comprobacion de conexiones a elementos en tienda.</h2>');
_ECHO("<pre>".$con_tda->cmdExec("[ -f /root/check_Elementos_Tienda.sh ] && (export MODO_WEB=1 && sh /root/check_Elementos_Tienda.sh 2>/dev/null) || echo 'Error'")."</pre>");
_ECHO('</div>');

?>