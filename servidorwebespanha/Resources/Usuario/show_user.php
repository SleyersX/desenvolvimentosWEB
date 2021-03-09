<?php
@session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
$Idioma=$_SESSION['Idioma'];
$Textos1=array(
	"welcome"    => array("ESP" => "Bienvenid@:",	"ENG" => "Welcome:"),
	"logout"     => array("ESP" => "Salir",			"ENG" => "Logout"),
	"guess"      => array("ESP" => "Invitado",		"ENG" => "Guess"),
	"login"      => array("ESP" => "Entrar",		"ENG" => "Login"),
	"txtLogout"  => array(
		"ESP" => "<p>Su sesion ha sido cerrada.</p><p><small><i>Este dialogo se cerrar&aacute; en 5 segundos...</i></small></p>",
		"ENG" => "<p>Your session has been closed.</p><p><small><i>This dialog will be closed in 5 seconds...</i></small></p>")
);

$Welcome=$Textos1["welcome"][$Idioma];
$TextoDespedida=$Textos1["txtLogout"][$Idioma];
$User1=$Textos1["guess"][$Idioma]; $NameUser="";
$Logout=$Textos1["logout"][$Idioma];
$Login=$Textos1["login"][$Idioma];

$banderas=array(
	"ENG" => "<img src='/img/idioma_eng.jpg' height=15 width=20 title='Language: ENGLISH'/>",
	"ESP" => "<img src='/img/idioma_esp.gif' height=15 width=20 title='Idioma: español'/>"
);

$bandera="<a id='a_select_idioma' href='javascript:{}'>".$banderas[$Idioma]."</a>";

$T_User_Login=array(
	"UL_TITLE" => array (
		"ESP" => "Se requiere validacion de usuario" ,
		"ENG" => "User validation is required"),
	"UL_USER" => array(
		"ESP" => "Usuario",
		"ENG" => "User" ),
	"UL_USER_PLACEHOLDER" => array(
		"ESP" => "Escriba el usuario",
		"ENG" => "Type the username" ),
	"UL_PASS" => array(
		"ESP" => "Contrase&ntilde;a",
		"ENG" => "Password" ),
	"UL_PASS_PLACEHOLDER" => array(
		"ESP" => "Escriba la contrase&ntilde;a",
		"ENG" => "Type the password" ),
	"UL_LEGEND" => array (
		"ESP" => "<h3>Condiciones:</h3><ul><li>El Usuario debe estar dado de alta en la base de datos de la herramienta. En caso de no estarlo, deber&iacute;a ponerse en contacto con su supervisor del pa&iacute;s o con los administradores del sistema en SEDE.</li><li>Hay que introducir el usuario y password que se utiliza normalmente para entrar en <b>INTRANET</b> (por <b>LDAP</b>).</li><li>El Usuario se responsabiliza de todas las acciones que se realicen una vez que el acceso sea permitido.</li></ul>",
		"ENG" => "<b>Conditions:</b><ul><li>Users must be registered in the database of the tool. If they are not, you should contact your supervisor of the country or system administrators in Spain. </li><li>The username and password normally used to enter INTRANET must enter (<b>LDAP</b>).</li><li>The user is responsible for all actions taken once access is allowed.</li></ul>" )
);
foreach($T_User_Login as $k => $d) {
	$$k = $d[$Idioma];
}

if (($_SESSION['usuario'])!="Invitado") {
	$User1=$_SESSION['usuario'];
	$NameUser='('.$_SESSION['nombre_usuario'].')';
	$Tool_Login='<a id="a_cerrar_sesion" href="javascript:{}">'.$Logout.'</a>';
} else {
		$Tool_Login='<a id="a_login" href="javascript:{}" onclick="$(\'#user_login\').dialog(\'open\');">'.$Login.'</a>';
}
echo '<div id="Cabecera_Linea2" style="font-size:90%">
		<span>'.$Welcome.' </span>
		<span id="id_user">'.$User1.'</span> <span id="id_name_user">'.$NameUser.'</span>  '.$bandera.'<br>
		<span>IP: '.$_SERVER['REMOTE_ADDR'].' - </span> '.$Tool_Login.'
	</div>';
echo '
	<script>
		$("#a_cerrar_sesion").on("click",function() {
			$.get(DIR_RAIZ+"/Usuario/usuario.php?Accion=Cerrar").done(function() { swal_logout(); });
		});
	</script>';
require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Usuario/select_idioma.php");
?>
