<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
require_once($DOCUMENT_ROOT.$DIR_LIBRERIAS."/jquery.php");
$Idioma=$_SESSION['Idioma'];

if (empty($_SESSION["usuario"])) {
	$_SESSION["usuario"] = $username = "Invitado";
} else {
	$username=$_SESSION["usuario"];
}

$_SESSION['UserIP'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['F_Inicio'] = (empty($_SESSION['F_Inicio'])?date("Y-m-d H:i:s"):"");

//Registra_login($username,"LOGIN: acceso a la herramienta");
//Crea_Dir_Temporal( $username , false );

?>
<head>
	<link rel="stylesheet" type="text/css" href="/Resources/fonts/font-awesome-4.7.0/css/font-awesome.css"/>
</head>
<style>
	body {
		background-color:whitesmoke;
		background-image:url(/img/fondo-degradado.png);
		background-size:100%;
		height: 100%; width: 100%;
		
	}
	#container {
		 position: absolute; top: 50%; margin-top: -235px; left: 0;width: 100%;
	}
	#user_login {
		font-family: "Segoe UI",Calibri,"Myriad Pro",Myriad,"Trebuchet MS",Helvetica,Arial,sans-serif;
		/*padding: 10px; 
		background: ghostwhite;
		border: 2px solid #000;
		border-radius: 5px;
		
		    /* Size & position */
    width: 900px; height: 450px;
    margin: auto auto; 
    padding: 10px;
    position: relative; /* For the submit button positioning */

    /* Styles */
    box-shadow: 
        0 0 1px rgba(0, 0, 0, 0.3), 
        0 3px 7px rgba(0, 0, 0, 0.3), 
        inset 0 1px rgba(255,255,255,1),
        inset 0 -3px 2px rgba(0,0,0,0.25);
    border-radius: 5px;
    background: linear-gradient(#eeefef, #ffffff 10%);
	}

	#user_login #user_error {
		font-size: 12px; color:red; font-weight: normal;
	}
	.icon_50 { height:50; width:50; }

.field {
    position: relative; /* For the icon positioning */
}

.field i {
    /* Size and position */
    left: 0px;
    top: 0px;
    position: absolute;
    height: 36px;
    width: 36px;

    /* Line */
    border-right: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 1px 0 0 rgba(255, 255, 255, 0.7);

    /* Styles */
    color: #777777;
    text-align: center;
    line-height: 42px;
    transition: all 0.3s ease-out;
    pointer-events: none;
}

#user_login input[type=text],
#user_login input[type=password] {
    font-family: 'Lato', Calibri, Arial, sans-serif;
    font-size: 13px;
    font-weight: 400;
    text-shadow: 0 1px 0 rgba(255,255,255,0.8);

    /* Size and position */
    width: 100%;
    padding: 10px 18px 10px 45px;

    /* Styles */
    border: none; /* Remove the default border */
    box-shadow: 
        inset 0 0 5px rgba(0,0,0,0.1),
        inset 0 3px 2px rgba(0,0,0,0.1);
    border-radius: 3px;
    background: #f9f9f9;
    color: #777;
    transition: color 0.3s ease-out;
}

[class^="icon-"]:before, [class*=" icon-"]:before {
    font-family: FontAwesome;
    font-weight: normal;
    font-style: normal;
    display: inline-block;
    text-decoration: inherit;
}

.icon-large:before {
    vertical-align: top;
    font-size: 1.3333333333333333em;
}

.icon-user:before {
    content: "\f007";
}
.icon-lock:before {
    content: "\f023";
}

#grupo_botones button {
	padding: 10px;
	border-radius: 5px;
	font-weight: bold;
	font-size: 20; 
}

</style>


<body>
<div id="container">
<div id="user_login" class="login_esp">
	<table class="t_principal">
		<tr>
			<td>
				<h2 class="IDIOMA ESP">BIENVENIDO/A A LA HERRAMIENTA DE SOPORTE REMOTO</h2>
				<h2 class="IDIOMA ENG" style="display:none">WELCOME TO REMOTE-SUPPORT TOOL</h2>
				<img src="/img/Monitorizacion.jpg" alt="">
			</td>
			<td>
				<h3>LOGIN:</h3>
				<table>
					<tr>
						<td>
							<form class="form-1">
								<p class="field">
									<input type="text" name="login" placeholder="Username">
									<i class="icon-user icon-large"></i>
								</p>
								<p class="field">
									<input type="password" name="password" placeholder="Password">
									<i class="icon-lock icon-large"></i>
								</p>        
							</form>
						</td>
					</tr>
					<tr>
						<td>
							<div class="div_select_idioma">
								<span class="IDIOMA ESP">Seleccione idioma:</span>
								<span class="IDIOMA ENG" style="display:none">Select language:</span>								
								<select id="select_idioma">
									<option value="ESP">Espa&ntilde;ol/Spanish</option>
									<option value="ENG">Ingl&eacute;s/English</option>
								</select>
							</div>
						</td>
					</tr>
				</table>
				<b style="color:red; font-size:75%" id="res_login"></b>
				<div style="margin-top:3em; font-size:80%">
					<div class="IDIOMA ESP">
						<h3>Condiciones:</h3>
						<ul>
							<li>El Usuario debe estar dado de alta en la base de datos de la herramienta. En caso de no estarlo, deber&iacute;a ponerse en contacto con su supervisor del pa&iacute;s o con los administradores del sistema en SEDE.</li>
							<li>Hay que introducir el usuario y password que se utiliza normalmente para entrar en <b>INTRANET</b> (por <b>LDAP</b>).</li>
							<li>El Usuario se responsabiliza de todas las acciones que se realicen una vez que el acceso sea permitido.</li>
						</ul>
					</div>
					<div class="IDIOMA ENG" style="display:none">
						<h3>Condition terms:</h3>
						<ul>
							<li>Users must be registered in the database of the tool. If they are not, you should contact your supervisor of the country or system administrators in Spain. </li>
							<li>The username and password normally used to enter INTRANET must enter (<b>LDAP</b>).</li>
							<li>The user is responsible for all actions taken once access is allowed.</li>
						</ul>
					</div>					
				</div>	
				<div id="grupo_botones">
					<button id="b_login">
						<span class="IDIOMA ESP"> Entrar </span> 
						<span class="IDIOMA ENG"> Login </span>
					</button>
					<button id="b_anonimo">
						<span class="IDIOMA ESP">Invitado</span>
						<span class="IDIOMA ENG">Guess</span>
					</button>
				</div>
			</td>
		</tr>
	</table>
</div>
</div>
</body>

<script>
	var idioma_actual;

	function Cambia_Idioma(new_idioma) {
		idioma_actual=new_idioma;
		console.log(idioma_actual, new_idioma)
		$(".IDIOMA").hide();
		$(".IDIOMA."+new_idioma).show();
	}
	
	Cambia_Idioma("<?php echo $Idioma; ?>");
	
	$("#select_idioma").on("change",function () {
		Cambia_Idioma($(this).val());
	});
	
	/*$("#user_login").dialog({
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
	*/
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

