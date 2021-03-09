<?php
$Idioma=$_SESSION['Idioma'];
echo '
<style>
#user_login {
	font-family: "Segoe UI",Calibri,"Myriad Pro",Myriad,"Trebuchet MS",Helvetica,Arial,sans-serif;
	padding: 10px; width: 600px; height: 400px; background: white;
	margin: auto auto; border: 2px solid #000
}
#user_login input { margin: 5px; padding: 5px }
#user_login #user_error {
	font-size: 13px; color:red; font-weight: normal;
</style>
';

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
		"ESP" => "<b>Condiciones:</b><ul><li>El Usuario debe estar dado de alta en la base de datos de la herramienta. En caso de no estarlo, deber&iacute;a ponerse en contacto con su supervisor del pa&iacute;s o con los administradores del sistema en SEDE.</li><li>Hay que introducir el usuario y password que se utiliza normalmente para entrar en <b>INTRANET</b> (por <b>LDAP</b>).</li><li>El Usuario se responsabiliza de todas las acciones que se realicen una vez que el acceso sea permitido.</li></ul>",
		"ENG" => "<b>Conditions:</b><ul><li>Users must be registered in the database of the tool. If they are not, you should contact your supervisor of the country or system administrators in Spain. </li><li>The username and password normally used to enter INTRANET must enter (<b>LDAP</b>).</li><li>The user is responsible for all actions taken once access is allowed.</li></ul>" )
);

foreach($T_User_Login as $k => $d) {
	$$k = $d[$Idioma];
}
echo '
<div id="user_login" name="alert_pwd" style="display:none" title="'.$UL_TITLE.'">
	<div>
		<table>
		<tr>
			<td><b>'.$UL_USER.':</b></td>
			<td><input type="text" id="usuario" name="input_usuario" size="auto" maxlength="8" placeholder="'.$UL_USER_PLACEHOLDER.'" autofocus/></td>
		</tr>
		<tr>
			<td><b>'.$UL_PASS.':</b></td>
			<td><input type="password" id="password" name="input_password" placeholder="'.$UL_PASS_PLACEHOLDER.'" /></td>
		</tr>
		</table>
		<b style="color:red; font-size:75%" id="res_login"></b>
		<div style="margin-top:3em; font-size:80%">
		'.$UL_LEGEND.'
		</div>
	</div>
</div>

<script>
	$("#user_login").dialog({
		autoOpen: false, modal: true, width: 700, height: "auto", resizable: false,
		buttons: {
			"Login": function() {
				new_user=$("#usuario").val();
				new_pass=$("#password").val();
				url=DIR_RAIZ+"/Usuario/usuario.php?username="+new_user+"&password="+encodeURIComponent(new_pass);
				$.get(url,
					function(data,status) {
						$("#res_login").html(data);
						if ($("#err").text() == "SUCCESS") {
							Put_SESSION("CHG_SESSION","Idioma", "ESP");
							$("#user_login").dialog("close"); location.reload(true);
						}
					});
			},
			"Cancelar": function() { $(this).dialog("close"); }
		}
	});
	$("#user_login").keydown(function (event) {
		if (event.keyCode == 13) {
			$(this).parent().find("button:eq(0)").trigger("click");
			return false;
		}
	});
// 	$("#id_select_idioma").on("change",function(x) {
// 		if ($(this).val() == "Español") {
// 			$("#user_login_ENG").hide(); $("#user_login_ESP").show();
// // 			Put_SESSION("CHG_SESSION","Idioma", "ESP");
// 		} 
// 		if ($(this).val() == "English") {
// 			$("#user_login_ENG").show(); $("#user_login_ESP").hide();
// // 			Put_SESSION("CHG_SESSION","Idioma", "ENG");
// 		} 
// 	});
</script>
';

if (empty($_SESSION["usuario"])) {
	$_SESSION["usuario"] = $username = "Invitado";
} else {
	$username=$_SESSION["usuario"];
}

$_SESSION['UserIP'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['F_Inicio'] = (empty($_SESSION['F_Inicio'])?date("Y-m-d H:i:s"):"");

Registra_login($username,"LOGIN: acceso a la herramienta");
Crea_Dir_Temporal( $username , false );

if ($_SERVER['REMOTE_ADDR'] == "10.208.185.5" ) {
//	print_r($GLOBALS);
	print_r($_SESSION);
	print_r($_SERVER);
}

?>
