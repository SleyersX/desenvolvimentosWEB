<?php

if (empty($_GET["opcion"]))
	die("ERROR: no hay opcion...");

switch($_GET["opcion"]) {
	case "info_1":
		echo '
		<table class="t_info" width="100%">
			<tr><td class="subrayado"><i class="fas fa-info-circle"></i><b style="margin-left:1em">TIENDAS y VERSIONES POR CENTRO</b></td><tr>
			<tr><td class="d2">Pulse un alguna celda para acceder a un filtro y obtener el listado de tiendas pertenecientes a esa celda (criterio)</td></tr>
		</table>';
		break;
}

?>
