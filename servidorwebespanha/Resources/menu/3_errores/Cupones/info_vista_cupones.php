<?php

switch(@$_GET["opcion"]) {
	case "info_1":
		echo '
	<table class="t_info" width="100%">
		<tr><td class="subrayado"><i class="fas fa-info-circle"></i><b style="margin-left:1em">INFORMACION</b></td><tr>
		<tr><td class="d2">Se requiere ingresar en el sistema para acceder a las funcionalidades avanzadas.</td></tr>
		<tr><td>Pulse en el icono <i class="fa fa-sign-in-alt"></i> para hacer LOG-IN</td></tr>
	</table>
';
		break;
}
?>