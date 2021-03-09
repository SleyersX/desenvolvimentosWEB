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

echo '<body class="VISTAS">';

echo '<div id="DIV_CABECERA_CONECTAR">
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
	</div>
	<div id="div_espera" style="display:none" title="Connecting to POS...">
	<table>
		<tr><td><img src="/img/wait.gif" style="margin:0 0 auto; left:50%;"/></td></tr>
		<tr><td align="center" id="Estados"></td></tr>
		<tr><td id="Progreso" style="display:none">Downloading progress: <progress id="id_progreso" max="100"></progress></td></tr>
	</table>
	</div>

	<script>
		$("#div_espera").dialog({ autoOpen: true, modal: true, width: "auto", height: 250, resizable: false });
		var interval_datos_cabecera=en_background("#DIV_DATOS", DIR_RAIZ+"/tools/estado_servidor.php?Opcion=DATOS_CABECERA", 1000);
		$("#DIV_USUARIO").load(DIR_RAIZ+"/Usuario/show_user_lite.php");
	</script>';

if (isset($host) && isset($port))
	$conexion_por_ip=true;
else {
	if (!isset($Tienda)) die (Alert("error","No hay definida Tienda para conexion..."));
	if (!isset($Caja)) die (Alert("error", "ERROR: No hay definida Caja para conexion..."));
}

// Configura_Pagina(0,"CNX: $Tienda-$Caja",__FILE__,NULL);

require_once("Descripciones_Scripts.php");

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

$DIR_ACCIONES_PHP=dirname($SCRIPT_FILENAME)."/Acciones_PHP/";
$DIR_LISTADOS_PHP=dirname($SCRIPT_FILENAME)."/Listados_PHP/";
$DIR_DATOS_PHP=dirname($SCRIPT_FILENAME)."/Datos_PHP/";

/////////////////// OBTENEMOS LA LISTA DE SCRIPT GLOBAL PARA EL PAIS.
	$Lista_Scripts=array();
// 	if ($Pais = 'XXX') $Busca_Pais='ESP'; else $Busca_Pais=$Pais;
	$result=myQUERY("select * from scripts_web where pais like '%$Pais%'");
	if (count($result) == 0) die(Alert("warning", myGetText("NO_SCRIPTS_PAIS")));
	foreach($result as $d) {
		list($id_script, $script, $tipo, $php, $pais) = $d;
		$Lista_Scripts[$id_script] = array($script, $tipo, $php, $pais);
	}

////////////////// MIRAMOS SI EL GRUPO TIENE LOS PERMISOS PARA EJECUTAR CADA GRUPO DE SCRIPTS...
	$result=myQUERY("select sw_Listados,sw_Acciones,sw_Datos from grupos where grupoID=".$_SESSION['grupo_usuario']);
	list($sw_Listados, $sw_Acciones, $sw_Datos) = $result[0];

/////////////////// SACAMOS LOS SCRIPTS PARTICULARES DE CADA USUARIO/GRUPO.
	$s_x_g=myQUERY("select id_script from scripts_x_grupo where id_grupo=".$_SESSION['grupo_usuario']." AND Valor=1");
	$s_x_u=myQUERY("select id_script from scripts_x_usuario where id_usuario=".$_SESSION['id_usuario']);	

////////////////// JUNTAMOS TODOS LOS SCRIPTS EN UN SOLO ARRAY
	$scripts_total = array();
	if (count($s_x_g) > 0) foreach($s_x_g as $d) $scripts_total[]=$d[0];
	if (count($s_x_u) > 0) foreach($s_x_u as $d) $scripts_total[]=$d[0];

	foreach($Lista_Scripts as $id => $sc) {
		if (in_array($id, array_values($scripts_total))) {
			if ($sw_Listados == 1 && $sc[1] == "Listados") $Lista_Listados[$sc[0]]=$DIR_LISTADOS_PHP.$sc[2];
			if ($sw_Acciones == 1 && $sc[1] == "Acciones") $Lista_Acciones[$sc[0]]=$DIR_ACCIONES_PHP.$sc[2];
			if ($sw_Datos == 1 && $sc[1] == "Datos") $Lista_Datos[$sc[0]]=$DIR_DATOS_PHP.$sc[2];
		}
	}

if (isset($Lista_Listados)) ksort($Lista_Listados);

if (@$conexion_por_ip==true)
	$con_tda=new SFTPConnection($host, $port);
else {
	$Param_Pais = $_GET['Pais'];
	$tmp=myQUERY("select ip from tiendas where numerotienda=$Tienda AND pais in ('$Param_Pais','GEA')");
	$host=$tmp[0][0];
	if ($tmp == NULL)
		die(Alert("error", "Error en la base de datos<br>No existe la tienda.<br>"));
	list($new_ip,$new_port) = getIP_Absoluta($host, $Caja);
	if ($Tienda == 61202 || $Tienda == 61206)
		$con_tda=new SFTPConnection($new_ip, $new_port,"root","root2");
	else
		$con_tda=new SFTPConnection($new_ip, $new_port);
}
if (!($con_tda->getConnection())) {
	require_once("./traslate/error_conexion.php");
// 	die(Alert("error", "No ha sido posible establecer conexion"));
} else {
	$Tienda=$con_tda->tienda; $Caja=$con_tda->caja;
	$URL_SHELL="http://$SERVER_ADDR:8080/?IP=".$con_tda->GetIP()."&caja=$Caja&comandoTerminal=";
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
				<fieldset class="ACCESOS_DIRECTOS"><legend>ACCESOS DIRECTOS</legend>
				<ul>
					<li><a href="javascript:{}" onclick="new_window_open(\''.$URL_SHELL.'less_DE\',\'DIARIO ELECTRONICO ACTUAL: '.$Tienda.'-'.$Caja.'\');">Diario Electronico de Hoy</a></li>
					<li><a href="javascript:{}" onclick="new_window_open(\''.$URL_SHELL.'less_file&lessFichero=/usr/local/n2a/var/log/n2a_application.log\',\'N2A_APPLICATION.LOG: '.$Tienda.'-'.$Caja.'\');">LOG Application de Hoy</a></li>
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

	echo '</td></tr></table>

	<script language="javascript">
	$("#centro").html("<button class=\"button\" id=\"Ver_Menu\">Mostrar/Ocultar menu</button>");
	$("#DIV_CENTRO").html("<button class=\"button\" id=\"Ver_Menu\">Mostrar/Ocultar menu</button>");
	$("#Ver_Menu").on("click", 
		function(e) { $("#Lista_de_Opciones").toggle("swing"); e.preventDefault(); }
	);

	function Repetir_Listado(Que_Listado) {
		INPUT_HIDDEN(\'myListados\',Que_Listado,\'myForm\');
		SUBMIT(\'myForm\');
	}

	function Ejecuta_Opcion(Clase, Que_Accion, x) {
		INPUT_HIDDEN(\'Que_Accion\',Que_Accion, \'myForm\');
		INPUT_HIDDEN(Clase,x.innerText,\'myForm\');
		SUBMIT(\'myForm\');
	}
	'.(isset($_POST['Que_Accion'])?'INPUT_HIDDEN(\'Que_Accion\',\''.$_POST['Que_Accion'].'\', \'myForm\')':'').'
	'.(!isset($_POST['Que_Accion'])?'$("#Lista_de_Opciones").show();':'').'

	function stopRKey(evt) {
		var evt = (evt) ? evt : ((event) ? event : null);
		var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
		if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
	}
	document.onkeypress = stopRKey;
	$("#id_table_conectar").width($(document).width()-50);

	$(window).resize(function() {
		$("#id_table_conectar").width($(document).width()-50);
	});

	$("#a_cerrar_sesion").on("click",function() {
		alert("Accion no permitida\nAction not allowed");
	});

	</script>';
}
echo '<script>Desbloqueo(); $("#div_espera").dialog("close");</script>';
_FLUSH();

?>
</form>
</body>
</html>