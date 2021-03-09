<?php
header('Access-Control-Allow-Origin: *');

@session_start();
// if ($_SERVER['REMOTE_ADDR'] == "10.208.185.5") $Traza=true;
// if ($_SERVER['REMOTE_ADDR'] != "10.208.185.5") header("Location:/mantenimiento.php");
$No_Hay_Marquesina=$No_Hay_Menu=$No_Hay_Centro=true;

require_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].$DIR_TOOLS.'tools.php');
require_once($_SERVER['DOCUMENT_ROOT'].$DIR_TOOLS.'comun.php');
require_once($_SERVER['DOCUMENT_ROOT'].$DIR_TOOLS.'head_1.php');
// 	require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/Usuario/usuario.php');

if (empty($_SESSION['usuario'])) {
	require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/Usuario/usuario_login.php');
	echo '<script>$("#user_login").dialog("open");</script>';
}

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }

$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['usuario'];
$grupo_usuario = $_SESSION['grupo_usuario'];

/*$id_usuario = 190;
$usuario = 'CCF001ES';
$grupo_usuario = 13;*/

if (isset($host) && isset($port))
	$conexion_por_ip=true;
else {
	if (!isset($Tienda)) die (Alert("error","No hay definida Tienda para conexion..."));
	if (!isset($Caja)) die (Alert("error", "ERROR: No hay definida Caja para conexion..."));
}

$Pregunta_Mucho='if (!confirm("Esta query puede tardar mucho.\n¿Desea continuar?")) location.reload();';

function Desde_Hasta ($Item, $Value, $Opcion) {
	$tmp="<p>DESDE $Item: <input type='text' name='Desde' id='Desde' /><br>";
	$tmp.="HASTA $Item: <input type='text' name='Hasta' id='Hasta' /><br></p>";
	$tmp.="<input class='button' type='submit' onkeyup=\"if (event.keyCode == 13) submit()\" name='myListados' value='$Opcion' autofocus/>";
	$tmp.="<input type='HIDDEN' name='Subaction' value='$Value'/>";
	return $tmp;
}

function Capt_Item ($Item, $Value, $Opcion) {
	$tmp="<p>Introduzca $Item: <input type='text' name='Desde' id='Desde' /> ";
	$tmp.="<input class='button' type='submit' onkeyup=\"if (event.keyCode == 13) submit()\" name='myListados' value='$Opcion' autofocus/>";
	$tmp.="<input type='HIDDEN' name='Subaction' value='$Value'/>";
	return $tmp;
}

//require_once("./Descripciones_Scripts.php");
require_once("./get_scripts_x_user.php");

$Param_Pais = $_GET['Pais'];
	$tmp=myQUERY("select ip from tiendas where numerotienda=$Tienda AND pais in ('$Param_Pais','GEA')");
	$host=$tmp[0][0];
	if ($tmp == NULL)
		die(Alert("error", "Error en la base de datos<br>No existe la tienda.<br>"));
	list($new_ip,$new_port) = getIP_Absoluta($host, $Caja);
	$IP=$new_ip;
	$Puerto=$new_port;
	
/*
if (@$conexion_por_ip==true)
	$con_tda=new SFTPConnection($host, $port);
else {
	$Param_Pais = $_GET['Pais'];
	$tmp=myQUERY("select ip from tiendas where numerotienda=$Tienda AND pais in ('$Param_Pais','GEA')");
	$host=$tmp[0][0];
	if ($tmp == NULL)
		die(Alert("error", "Error en la base de datos<br>No existe la tienda.<br>"));
	list($new_ip,$new_port) = getIP_Absoluta($host, $Caja);
	if ($Tienda == 61202 || $Tienda == 61206 )
		$con_tda=new SFTPConnection($new_ip, $new_port,"root","root2");
	else
		$con_tda=new SFTPConnection($new_ip, $new_port);
}
if (!($con_tda->getConnection())) {
	echo '<script language="javascript">Desbloqueo();</script>';
	require_once("./traslate/error_conexion.php");
// 	die(Alert("error", "No ha sido posible establecer conexion"));
} else {
	$Tienda=$con_tda->tienda; $Caja=$con_tda->caja;
	$IP=@$con_tda->GetIP();
	$Puerto=@$con_tda->GetPort();
	_ECHO('
		<div id="Lista_de_Opciones">
			<table width="100%">
			<tr>
			<td width="33%" valign="top">
				<fieldset class="Menu_Opciones"><legend>LISTADOS DISPONIBLES</legend>'.
				Pinta_Opciones(@$Lista_Listados,"myListados","ol").'
				</fieldset>
			</td>
			<td width="33%" valign="top">
				<fieldset class="Menu_Opciones"><legend>ACCIONES DISPONIBLES</legend>'.
				Pinta_Opciones(@$Lista_Acciones,"myAcciones","ol").'
				</fieldset>
			</td>
			<td width="33%" valign="top">
				<fieldset class="Menu_Opciones"><legend>DATOS DISPONIBLES</legend>'.
				Pinta_Opciones(@$Lista_Datos,"myDatos","ol").'
				</fieldset>
			</td>
			</tr>
			<tr>
			<td width="33%" valign="top" colspan="3">
				<fieldset id="accesos_directos" class="ACCESOS_DIRECTOS"><legend>ACCESOS DIRECTOS</legend>
				<ul>
					<li><a id="tailDE" href="javascript:{}">Diario Electronico de Hoy</a></li>
					<li><a id="tailog" href="javascript:{}">TAILOG</a></li>
					<li><a id="tailog_periferia" href="javascript:{}">TAILOG PERIFERIA</a></li>
					<li><a id="log_hoy" href="javascript:{}">LOG APLICACION DE HOY</a></li>
					<li><a id="log_capturador_hoy" href="javascript:{}">LOG CAPTURADOR HOY</a></li>
				</ul>
				</fieldset>
			</td>
			<tr>
			</table>
		</div>
	');
	echo '<form id="myForm" action="'.$Pag_Actual.'" method="post">';

	if (!SoyYo()) echo '<div id="Todo">';
	echo '<table width="100%" id="id_table_conectar">
	<tr>
		<td valign="top" width="20%">
			<fieldset>
				<legend>INFORMACION DE LA CAJA</legend>
				<center>
				<BUTTON class="button" type="button" onclick="javascript:Reload();" title="Pulse qu&iacute; para recargar los datos de esta TPV.">Recargar</BUTTON>
				<BUTTON class="button" type="button" onclick="javascript:Desconectar();" title="Pulse aqu&iacute; para cerrar la conexi&oacute;n con esta caja">Desconectar</BUTTON>
				</center>
				<hr>
				'.Datos_Caja_Conexion($con_tda).'
			</fieldset>
		</td>
		<td valign="top" width="80%" id="parte_principal">';

	echo '<div id="contenedor">';
	if (isset($_POST['Que_Accion'])) {
		if (isset($myListados))
			$Repetir_Listado="<button class='button' onclick=\"Repetir_Listado('$myListados');\" title=\"Pulse qu&iacute; para repetir el listado con otros par&aacute;metros\">Repetir</button>";
		require_once($_POST['Que_Accion']);
	}
	else {
		_ECHO(Pinta_Resultado("VISOR CAJERA",str_replace("- INFO  - [main] ","",$con_tda->GetDispCaje(10)),true));
	}
	echo '</div>';

$IH=(isset($_POST['Que_Accion'])?'INPUT_HIDDEN(\'Que_Accion\',\''.$_POST['Que_Accion'].'\', \'myForm\')':'');
$LdO=(!isset($_POST['Que_Accion'])?'$("#Lista_de_Opciones").show();':'');

	echo '</td></tr></table>';
	
}*/

?>

<style>
	#head-nav2 { font-family:Arial, Verdana, sans-serif; cursor:pointer; }
	#head-nav2 * {z-index: 9999}
	
</style>
<body class="VISTAS">
	<div id="DIV_CABECERA_CONECTAR">
		<div id="CAB1">
			<table width="100%" style="top:0">
			<tr>
				<td id="DIV_PAIS">
					<img id="ICONO_PAIS" src="/favicon.ico" /><img src="/img/logo_dia2.gif" title="Pagina del Portal" />
				</td>
				<td id="DIV_CENTRO"></td>
				<td id="DIV_USUARIO"></td>
				<td id="DIV_DATOS"></td>
			</tr>
			</table>
		</div>
		<nav id="head-nav2" class="navbar navbar-fixed-top">
			<ul class="nav">
				<li><?php echo $MENU_LISTADOS; ?></li>
				<li><?php echo $MENU_ACCIONES; ?></li>
				<li><?php echo $MENU_DATOS; ?></li>
				<li><?php echo $MENU_HERRAMIENTAS; ?></li>
			</ul>
			<div class="navbar-inner clearfix"><ul class="nav"></ul></div>
		</nav>
	</div>
	<div id="div_espera" style="display:none" title="Connecting to POS...">
	<table>
		<tr><td><img src="/img/wait.gif" style="margin:0 0 auto; left:50%;"/></td></tr>
		<tr><td align="center" id="Estados"></td></tr>
		<tr><td id="Progreso" style="display:none">Downloading progress: <progress id="id_progreso" max="100"></progress></td></tr>
	</table>
	</div>
	<div id="cuerpo_conecta"></div>
	<span id="s_url_SIABox" style="display:none"><?php echo $SERVER_SHELLINABOX."?IP=".$IP."&caja=".$Puerto; ?></span>

<script lang="javascript">

		$("#div_espera").dialog({ autoOpen: true, modal: true, width: "auto", height: 250, resizable: false });
		var interval_datos_cabecera=en_background("#DIV_DATOS", DIR_RAIZ+"/tools/estado_servidor.php?Opcion=DATOS_CABECERA", 1000);
		$("#DIV_USUARIO").load(DIR_RAIZ+"/Usuario/show_user_lite.php");

	function Repetir_Listado(Que_Listado) {
		INPUT_HIDDEN('myListados',Que_Listado,'myForm');
		SUBMIT('myForm');
	}

	function Ejecuta_Opcion(Clase, Que_Accion, x) {
		INPUT_HIDDEN('Que_Accion',Que_Accion, 'myForm');
		INPUT_HIDDEN(Clase,x.innerText,'myForm');
		SUBMIT('myForm');
	}
	function stopRKey(evt) {
		var evt = (evt) ? evt : ((event) ? event : null);
		var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
		if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
	}

	function shell_in_a_box( obj ) {
		if (obj.attr('class') == 'opc_nok')
			alert("No operational option!!");
		else {
			new_window_open(url_SIABox + "&comandoTerminal="+obj.attr('id'), obj.text()+": "+tmp_tienda+"-"+tmp_caja);
		}
	}

	document.onkeypress = stopRKey;

	tmp_tienda=<?php echo $Tienda; ?>;
	tmp_caja=<?php echo $Caja; ?>;
	url_SIABox='<?php echo @$SERVER_SHELLINABOX."?IP=".@$IP."&caja=".@$Puerto; ?>';

$(document).ready(function(){

	Desbloqueo(); $("#div_espera").dialog("close");

	$("#centro").html('<button class="button" id="Ver_Menu">Mostrar/Ocultar menu</button>');
	$("#DIV_CENTRO").html('<button class="button" id="Ver_Menu">Mostrar/Ocultar menu</button>');
	$("#Ver_Menu").on("click", 
		function(e) { $("#Lista_de_Opciones").toggle("swing"); e.preventDefault(); }
	);

	<?php echo @$IH; ?>
	<?php echo @$LdO; ?>

	$("#id_table_conectar").width($(document).width()-50);

	$(window).resize(function() {
		$("#id_table_conectar").width($(document).width()-50);
	});
	
	$("#a_cerrar_sesion").on("click",function() {
		alert("Accion no permitida\nAction not allowed");
	});

	$("#opciones_diagnosis a, #accesos_directos a ").on("click",function() {
		shell_in_a_box($(this));
	} );

	$("#head-nav2 a").on("click",function () {
		var src=$(this).attr("src");
		if (src) {
			//var url_conecta="/Resources/Estado_Monitorizacion/Conexion_Tienda/"+$(this).attr("src")+"<?php echo '?IP='.$IP,'&Puerto='.$Puerto;?>";
			var url_conecta=$(this).attr("src")+"<?php echo '?IP_Tienda='.$IP,'&Puerto='.$Puerto;?>";
			$("#cuerpo_conecta").html('<div id="Cargando"><img src="/img/wait.gif"/></div>');
			$("#cuerpo_conecta").load(url_conecta);
		}
	})
	
});

</script>

</form>
</body>
</html>

<?php _FLUSH(); ?>