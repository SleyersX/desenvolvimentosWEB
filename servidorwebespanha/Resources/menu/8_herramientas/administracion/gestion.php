<?php
// $Traza=true;
require("./comun_administracion.php");

if (empty($_SESSION['usuario'])) {
	require($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php");
}
else if ($_SESSION['grupo_usuario'] > 2 && $_SESSION['grupo_usuario'] != 6) {
	require($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/incorrect_profile.php");
}
else {
// require("listado_usuarios.php");

echo '
<fieldset id="Menu_Gestion" >
<ul>
	<li><a href="'.$DIR_ADMINISTRACION.'/gestiona_scripts.php">Gestionar scripts</a></li>
	<li><a href="'.$DIR_ADMINISTRACION.'/gestiona_grupos.php">Gestionar grupos</a></li>
	<li><a href="'.$DIR_ADMINISTRACION.'/gestiona_usuarios.php">Gestionar usuarios</a></li>
	<li><a href="'.$DIR_ADMINISTRACION.'/gestiona_scripts_x_grupo.php">Gestionar scripts x grupo</a></li>
	<li><a href="'.$DIR_ADMINISTRACION.'/gestiona_scripts_x_usuario.php">Gestionar scripts x usuario</a></li>
	'.(SoyYo()?'<li><a href="/'.$DIR_ADMINISTRACION.'/gestion_servidores.php">Gestionar servidores</a></li>':'').'
	<hr>
	<li><a href="/">Volver a la pagina soporte</a></li>
</ul>
</fieldset>';
}
?>