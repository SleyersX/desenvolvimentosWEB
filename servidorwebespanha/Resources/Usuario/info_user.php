<?php

if (empty($_GET["opcion"]))
	die("ERROR: no hay opcion...");

switch($_GET["opcion"]) {
	case "info_user":
		if (empty($_SESSION["nombre_usuario"])) {
			echo '
			<table class="t_info" width="100%">
				<tr><td class="subrayado"><i class="fas fa-info-circle"></i><b style="margin-left:1em">INFORMACION</b></td><tr>
				<tr><td class="d2">Se requiere ingresar en el sistema para acceder a las funcionalidades avanzadas.</td></tr>
				<tr><td>Pulse en el icono <i class="fa fa-sign-in-alt"></i> para hacer LOG-IN</td></tr>
			</table>';
		} else {
			echo '
				<table class="t_info" width="100%">
					<tr><td class="subrayado" colspan="2"><b>IDENTIFICACION</b></td></tr>
					<tr><td class="d1">ID:</td>             <td class="d2">'.$_SESSION["usuario"].'</td></tr>
					<tr><td class="d1">Nombre completo:</td><td class="d2">'.$_SESSION["nombre_usuario"].'</td></tr>
					<tr><td class="d1">Grupo:</td>          <td class="d2">'.$_SESSION["nombre_grupo"].'</td></tr>
				</table>';
		}
		break;
	
	case "info_login":
		if ($_GET["tipo"] == "logout")
			$txt='Pulse este aquí para cerrar la sesión del usuario/a actual.<br>Volverá a ser Invitado/a.';
		else
			$txt='Pulse este aquí para ingresar en el sistema y acceder a las funcionalidades avanzadas.';
		echo '
			<table class="t_info" width="100%">
				<tr><td class="subrayado"><i class="fas fa-info-circle"></i><b style="margin-left:1em">INFORMACION</b></td><tr>
				<tr><td class="d2">'.$txt.'</td></tr>
			</table>';
		break;
}
?>
