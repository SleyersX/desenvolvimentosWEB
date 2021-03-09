<?php
@session_start();
// if (!strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), "CHROME")) {
// 	echo '<center>';
// 	echo '<p style="margin-top:10%; font-size:3em">Lo sentimos...</p>';
// 	echo '<p style="font-size:2em">... pero esta herramienta funciona exclusivamente en Google Chrome.</p>';
// 	echo '<img src="/img/google-chrome.jpg" />';
// 	echo '<p><b>Este es su navegado actual:</b><br>'.$_SERVER['HTTP_USER_AGENT'].'</p>';
// 	echo '</center>';
// 	die();
// }

$f1=glob("/home/MULTI/id_rsa.*");
//$tmp=split('\.',$f1[0]);
$tmp=explode(".", $f1[0]);

if (!empty($tmp[1])) $PAIS_SERVER=$tmp[1]; else $PAIS_SERVER="ESP";

$IP_WEBSERVER=array(
	"ESP" => array("ALIAS" => "soporteesp", "HOME" => "Resources/Estado_Monitorizacion/monitorizacion.php?Pais=ESP"),
	"POR" => array("ALIAS" => "soportepor", "HOME" => "Resources/Estado_Monitorizacion/monitorizacion.php?Pais=POR"),
	"ARG" => array("ALIAS" => "soportearg", "HOME" => "Resources/Estado_Monitorizacion/monitorizacion.php?Pais=ARG"),
	"BRA" => array("ALIAS" => "soportebra", "HOME" => "Resources/Estado_Monitorizacion/monitorizacion.php?Pais=BRA"),
	"CHI" => array("ALIAS" => "soportechi", "HOME" => "Resources/Estado_Monitorizacion/monitorizacion.php?Pais=CHI"),
	"PAR" => array("ALIAS" => "soportepar", "HOME" => "Resources/Estado_Monitorizacion/monitorizacion.php?Pais=PAR")
);

switch($PAIS_SERVER) {
	/* -------------------------------------------------------------- */
	case "ESP":
		$ftp_server="ESCONCEN1"; $ftp_user_name="lares/usertpvsop"; $ftp_user_pass="al59e1q6"; $F_Salvar="Salvado_Ventas_N2A.php";
		$DATOS_LDAP=array(
			"ESP" => array("ldap_host"=>"lares.dsd", "DOMINIO"=>"LARES", "base_dn"=>"DC=lares,DC=dsd")
		);
		$SERVER_INFOCUPONES="10.208.162.6";
		$_SESSION['Idioma']="ESP";
		break;
	/* -------------------------------------------------------------- */	
	case "ARG":
		$ftp_server="ARCONCEN1"; $ftp_user_name="lares/usertpvsop"; $ftp_user_pass="al59e1q6"; $F_Salvar="Salvado_Ventas_SA.php";
		$DATOS_LDAP=array(
			"ARG" => array("ldap_host"=>"10.94.202.121", "DOMINIO"=>"LADIA", "base_dn"=>"OU=Argentina,DC=LA,DC=DIA"),
			"ESP" => array("ldap_host"=>"10.71.216.216", "DOMINIO"=>"LARES", "base_dn"=>"DC=lares,DC=dsd")
		);
		$SERVER_INFOCUPONES="";
		$_SESSION['Idioma']="ESP";
		break;
	/* -------------------------------------------------------------- */
	case "BRA":
		$ftp_server="BRCONCEN1"; $ftp_user_name="userinfoft"; $ftp_user_pass="hosthost"; $F_Salvar="Salvado_Ventas_SA.php";
		$DATOS_LDAP=array(
			"BRA" => array("ldap_host"=>"10.105.186.5",  "DOMINIO"=>"DIAMTZ", "base_dn"=>"DC=diamtz,DC=BR"),
			"ESP" => array("ldap_host"=>"10.71.216.216", "DOMINIO"=>"LARES",  "base_dn"=>"DC=lares,DC=dsd")
		);
		$SERVER_INFOCUPONES="";
		$_SESSION['Idioma']="ESP";
		break;
	/* -------------------------------------------------------------- */
	case "CHI":
		$ftp_server="CNCONCEN1"; $ftp_user_name="userinfoft"; $ftp_user_pass="hosthost"; $F_Salvar="Salvado_Ventas_SA.php";
		$DATOS_LDAP=array(
			"CHI" => array("ldap_host"=>"10.132.190.23", "DOMINIO"=>"LARCN",  "base_dn"=>"DC=larcn,DC=dsd"),
			"ESP" => array("ldap_host"=>"10.71.216.216", "DOMINIO"=>"LARES",  "base_dn"=>"DC=lares,DC=dsd")
		);
		$SERVER_INFOCUPONES="";
		$_SESSION['Idioma']="ENG";
		break;
	/* -------------------------------------------------------------- */
	case "PAR": // PARAGUAY
		$ftp_server="PYCONCEN1"; $ftp_user_name="lares/usertpvsop"; $ftp_user_pass="al59e1q6"; $F_Salvar="Salvado_Ventas_N2A.php";
		$DATOS_LDAP=array(
			"ARG" => array("ldap_host"=>"LA.DIA", "DOMINIO"=>"LADIA",  "base_dn"=>"OU=Argentina,DC=LA,DC=DIA"),
			"ESP" => array("ldap_host"=>"10.71.216.216", "DOMINIO"=>"LARES",  "base_dn"=>"DC=lares,DC=dsd"),
			"PAR" => array("ldap_host"=>"LA.DIA",        "DOMINIO"=>"LADIA",  "base_dn"=>"OU=Paraguay,DC=LA,DC=DIA")
		);
		$SERVER_INFOCUPONES="";
		$_SESSION['Idioma']="ESP";
		break;
	/* -------------------------------------------------------------- */
	case "POR":
		$ftp_server="PTCONCEN1"; $ftp_user_name="userinfoft"; $ftp_user_pass="hosthost"; $F_Salvar="Salvado_Ventas_SA.php";
		$DATOS_LDAP=array(
			"POR" => array("ldap_host"=>"10.246.64.104", "DOMINIO"=>"LARPT",  "base_dn"=>"DC=larpt,DC=dsd"),
			"ESP" => array("ldap_host"=>"10.71.216.216", "DOMINIO"=>"LARES",  "base_dn"=>"DC=lares,DC=dsd")
		);
		$SERVER_INFOCUPONES="";
		$_SESSION['Idioma']="ESP";
		break;
}

if (isset($_GET)) foreach($_GET as $k => $d) { $$k=$d;}
if (isset($_POST)) foreach($_POST as $k => $d) { $$k=$d; }
if (isset($_SESSION)) foreach($_SESSION as $k => $d) { $$k=$d; }
if (isset($_SERVER)) foreach($_SERVER as $k => $d) { $$k=$d; }

$tmp=explode("/",$PHP_SELF); $DIR_RAIZ="/".$tmp[1];

$DIR_MONITOR = "$DIR_RAIZ/Estado_Monitorizacion/";
	$PHP_HOME_MONITOR = "$DIR_MONITOR/monitorizacion.php";
	$DIR_CONEXION_TIENDA = $DIR_MONITOR."Conexion_Tienda/";
		$PHP_CONECTAR    = $DIR_CONEXION_TIENDA."conectar.php";
		$PHP_PRECONECTAR = $DIR_CONEXION_TIENDA."pre_conectar.php";
		$PHP_BUSCATIENDA = $DIR_CONEXION_TIENDA."busca_tienda.php";
	$ADMINISTRACION_PHP = $DIR_MONITOR."/Administracion/administracion.php";
	$DIR_AYUDA = $DIR_MONITOR."ayuda/";
$DIR_TOOLS = "$DIR_RAIZ/styles_js/";
$DIR_IMAGE = "/img/";
$DIR_LIBRERIAS = $DIR_RAIZ."/library/";

$DOWNLOAD_SERVER=$DIR_CONEXION_TIENDA."download_from_server.php";
$ESTADO_SERVIDOR = $DIR_RAIZ."/tools/estado_servidor.php";

$DIR_PHP_MYSQL = "$DOCUMENT_ROOT/$DIR_RAIZ/tools/mysql";

$SERVER_SHELLINABOX="http://".$SERVER_ADDR.":8080";
$SERVER_SHELLINABOX_2="http://".$SERVER_ADDR.":8085";

$VERSION_SERVER="v1.09";

$pag_inicial="/Resources/monitorizacion.php";

$url_menu="/Resources/menu";
$url_general=$url_menu."/1_general";
$url_vela=$url_menu."/2_vela";
$url_errores=$url_menu."/3_errores";
$url_listados=$url_menu."/4_listados";
$url_intervenciones=$url_menu."/5_intervenciones";
$url_s_cupones=$url_menu."/6_s_cupones";
$url_conect_tienda=$url_menu."/7_conect_tienda";
$url_herramientas=$url_menu."/8_herramientas";

function get_url_from_local($url_local) {
	global $PAIS_SERVER;
	$tmp_dir=str_replace($_SERVER["DOCUMENT_ROOT"], "", $url_local);
	if ($PAIS_SERVER=="BRA") $tmp_dir="/".$tmp_dir;
	return $tmp_dir;
}

if (!isset($Modo_Lite)) {
	if (!isset($No_Carga_Mysql)) require_once($DOCUMENT_ROOT.$DIR_TOOLS.'mysql.php');
	if (!isset($No_Carga_Tools)) require_once($DOCUMENT_ROOT.$DIR_TOOLS.'tools.php');
	if (!isset($No_Carga_ssh2))  require_once($DOCUMENT_ROOT.$DIR_LIBRERIAS.'ssh2.php');
	if (!isset($No_Carga_Comun)) require_once($DOCUMENT_ROOT.$DIR_TOOLS.'comun.php');
}


?>
