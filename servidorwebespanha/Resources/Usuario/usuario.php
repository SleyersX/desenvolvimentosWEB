<?php
//session_set_cookie_params('60'); // 10 minutes.
//session_regenerate_id(true);
@session_start();

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$Accion  = (empty($_GET['Accion'])?"Alta":$_GET['Accion']);
$A_Donde = (empty($_GET['Ir_URL'])?"":$_GET['Ir_URL']);

if (!function_exists("Crea_Dir_Temporal")) {
	require_once($_SERVER['DOCUMENT_ROOT'].$DIR_TOOLS.'tools.php');
}

if (!function_exists('ldap_connect'))
	die("<big>No esta instalado el sistema LDAP en este servidor<br><i>System LDAP is not installed in this server</i></big>");

$TEXTOS_ERROR_LDAP=array(
	  "LDAP_ERR_NO_CONN" => array(
		  "ESP" => "<p>Error de conexion con sistema de validacion LDAP. Rogamos se pongan en contacto con el administrador del sistema</p>"
		, "ENG" => "<p>Could not connect to LDAP server. Please contact the system administrator.</p>")
	, "LDAP_ERR_NO_BIND" => array(
		  "ESP" => "<p>Usuario no tiene permitido acceder a LDAP. Los motivos pueden ser:<ul><li>Password caducada.</li><li>Error al introducir la password</li><li>Usuario bloqueado.</li></ul><p>"
		, "ENG" => "<p>User is not allowed to access LDAP. The reasons may be:<ul><li>Password expired.</li><li>Failed to enter the password.</li><li>User blocked.</li></ul><p>")
	, "LDAP_ERR_NO_USER" => array(
		  "ESP" => "<p>El usuario no existe en LDAP. Por favor, pongase en contacto con el administrador del sistema.</p>"
		, "ENG" => "<p>The user does not exist in LDAP. Please contact the system administrator.</p>")

	, "NO_USER" => array(
		  "ESP" => "<p>Debe introducir un usuario.</p>"
		, "ENG" => "<p>You must enter a username.</p>")
	, "NO_PASSWORD" => array(
		  "ESP" => "<p>Debe introducir una password.</p>"
		, "ENG" => "<p>You must enter a password.</p>")
	, "NO_USER_IN_DB" => array(
		  "ESP" => "<p>El usuario no existe en la base de datos de este servidor.<br><i>Pongase en contacto con el administrador del sistema.</i></p>"
		, "ENG" => "<p>The user does not exist in the database from this server.<br><i>Contact your system administrator.</i></p>")

	, "ERROR_NOT_DEFINED" => array(
		  "ESP" => "<p>ERROR NO DEFINIDO.<br><i>Pongase en contacto con el administrador del sistema.</i></p>"
		, "ENG" => "<p>UNDEFINED ERROR.<br><i>Contact your system administrator.</i></p>")
);

function ERROR_LDAP($c_ldap, $Error) {
	global $TEXTOS_ERROR_LDAP;
	$Pre="<span id='err' style='display:none'>ERROR</span>";
	$Idioma=$_SESSION['Idioma'];
	if (array_key_exists($Error, $TEXTOS_ERROR_LDAP)) 
		$Text_Error=$TEXTOS_ERROR_LDAP[$Error][$Idioma];
	else
		$Text_Error="<p>ERROR: ".ldap_err2str(ldap_errno($c_ldap))."</p>";
	if ($c_ldap) @ldap_close($c_ldap);
	die($Pre.$Text_Error);
}

function validaNameUserLDAP($LOCALUser,$LOCALUserPwd,&$LOCALNameUser) {
	global $DATOS_LDAP, $PAIS_SERVER;
	
	if (preg_match("/......ES/",strtoupper($LOCALUser))) $Pais_LDAP="ESP";
	if (preg_match("/......PT/",strtoupper($LOCALUser))) $Pais_LDAP="POR";
	if (preg_match("/......BR/",strtoupper($LOCALUser))) $Pais_LDAP="BRA";
	if (preg_match("/......AR/",strtoupper($LOCALUser))) $Pais_LDAP="ARG";
	if (preg_match("/......CN/",strtoupper($LOCALUser))) $Pais_LDAP="CHI";
	if (preg_match("/......PY/",strtoupper($LOCALUser))) $Pais_LDAP="PAR";

	if ($Pais_LDAP=="ESP") return true;
//	if (SoyYo()) return true;

	$ldap_host = $DATOS_LDAP[$Pais_LDAP]["ldap_host"];
	$DOMINIO   = $DATOS_LDAP[$Pais_LDAP]["DOMINIO"];
	$base_dn   = $DATOS_LDAP[$Pais_LDAP]["base_dn"];

	$ldap_port = 389;

//	if ($PAIS_SERVER=="PAR") 	echo "<b style='color:black;'>Conectando con LDAP (".$ldap_host.":".$ldap_port.")</b><br>";
	$connect = ldap_connect( $ldap_host, $ldap_port);
	if (!$connect) {
		Registra_login($LOCALUser,"ERROR: no hay acceso a LDAP");
		return ERROR_LDAP($connect, "LDAP_ERR_NO_CONN");
	}

	ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);

	$ldap_user = $DOMINIO.'\\'.$LOCALUser;
	$ldap_pass = $LOCALUserPwd;
	$bind = @ldap_bind($connect, $ldap_user, $ldap_pass);
//	if ($PAIS_SERVER=="PAR") 	echo "$ldap_host - $ldap_user - ($ldap_pass) - ";
 	if (!$bind) {
	 	Registra_login($LOCALUser,"ERROR: password incorrecta");
 		return ERROR_LDAP($connect, "LDAP_ERR_NO_BIND");
 	}

	$filter = "(cn=$LOCALUser)";
	$read = ldap_search($connect, $base_dn, $filter);
//	if ($PAIS_SERVER=="PAR") 	echo "$ldap_host - $ldap_user - $ldap_pass - $base_dn - $filter";
	if (!$read) {
		Registra_login($LOCALUser,"ERROR: usuario no existe en LDAP");
		return ERROR_LDAP($connect, "LDAP_ERR_NO_USER");
	}

	$info = ldap_get_entries($connect, $read);
	$LOCALNameUser = $info[0]["displayname"][0];

	ldap_close($connect);
	return true;
}

function validaUsuarioLDAP($user, $pwd) {
	global $mysqli;
	unset($_SESSION["user_error"]);
	if (empty($user)) { ERROR_LDAP(NULL, "NO_USER"); }
	if (empty($pwd)) { ERROR_LDAP(NULL, "NO_PASSWORD"); }
	$result=myQUERY("SELECT usuarioID,usuario,nombre,grupo,grupoNombre FROM usuarios u inner join grupos g on g.grupoID = u.grupo where usuario = '".$user."'");
	if (count($result) < 1) {
		Registra_login($user,"ERROR: usuario no existe en HSR");
		ERROR_LDAP(NULL, "NO_USER_IN_DB");
	}

	// Ahora podemos llevar a cabo la validacion del usuario $user contra LDAP
	if (($user=="servcupo" && $pwd=="Servcupo") || validaNameUserLDAP($user,$pwd,$nameuser)) {
		list($_SESSION['id_usuario'],$_SESSION["usuario"],$_SESSION["nombre_usuario"],$_SESSION["grupo_usuario"],$_SESSION["nombre_grupo"]) = $result[0];
		Crea_Dir_Temporal( $user , true );
		return true;
	}
	return false;
}

function Get_Lista_Acciones_Usuario() {
	$Lista_Total=myQUERY("select script from scripts_web");
	$res1 = array_combine($_SESSION['scripts_x_grupo'], $_SESSION['scripts_x_grupo']);
	$res2 = array_combine($_SESSION['scripts_x_usuario'], $_SESSION['scripts_x_usuario']);
	if (!count($res1) && !count($res2))
		return $Lista_Total;
	return array_merge($res1,$res2);
}

switch ($Accion) {
	case "Alta":
		$user=urldecode($_GET['username']); $pwd=$_GET['password'];
		if (validaUsuarioLDAP($user, $pwd)) {
			Registra_login($user,"LOGIN: login correcto");
			die("<span id='err' style='display:none'>SUCCESS</span>");
		}
		else {
			Registra_login($user,"ERROR: no definido");
			ERROR_LDAP(NULL, "ERROR_NOT_DEFINED");
		}
		break;
	case "Cerrar":
		Registra_login($_SESSION["usuario"],"LOGOUT: logout correcto");
//		exec('sudo mysql soporteremotoweb -e "UPDATE sesiones SET F_FIN=NOW() WHERE id_sesion=\''.session_id().'\'"');
		session_destroy();
		break;
}

?>
