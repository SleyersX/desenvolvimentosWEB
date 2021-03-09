<?php
@session_start();

require($_SERVER['DOCUMENT_ROOT']."/config.php");

if (empty($_SESSION['usuario'])) { require($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }
if ($_SESSION['grupo_usuario'] > 2) { require($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/incorrect_profile.php"); die(); }

// require_once($_SERVER['DOCUMENT_ROOT']."/Resources/styles_js/comun.php");
// require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Usuario/usuario.php");

$Server=$_SERVER['SERVER_ADDR'];
// if ($PAIS_SERVER = "ESP") $Server="10.208.162.6";

echo '
	<script>
		window.location="http://'.$Server.'/'.get_url_from_local(dirname(__FILE__)).'/gestion.php";
	</script>
';
?>

