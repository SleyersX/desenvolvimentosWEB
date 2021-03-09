<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

$Pais=GetPais();

$PAG_INICIAL= $DIR_MONITOR."monitorizacion.php?Pais=$Pais";

$Table="Checks".$Pais;

$tmp1=explode("/",$PHP_SELF); $tmp2=array_pop($tmp1);
$mypage=htmlspecialchars($tmp2);
$icono_pais=$DIR_IMAGE."icono_$Pais.png";

$ICONOS=array(
	"Recargar"=>$DIR_IMAGE."recargar.png"
	,"Download_Server"=>$DIR_IMAGE."download_to_server.gif"
	,"Lupa-Edit"=>$DIR_IMAGE."i1/edit-find.png"
	,"Lupa"=>$DIR_IMAGE."lupa.png"
	,"USB"=>$DIR_IMAGE."i1/media-flash.png"
	,"To_PC"=>$DIR_IMAGE.'download_to_pc.gif'
);

// CABECERA DE LA PAGINA, COMUN CON TODAS LAS PAGINAS QUE CUELGAN.

$PAGINA=(isset($NEW_PAGINA)?$NEW_PAGINA:"ERRORES");
// $Que_Pagina=(!empty($_SESSION['NEW_PHP'])?$_SESSION['NEW_PHP']:$_SERVER['SCRIPT_FILENAME']);
$Que_Pagina=(!empty($_SESSION['NEW_PHP'])?$_SESSION['NEW_PHP']:"");
$TITULO="ERRORES";
if (isset($Tienda)) $TITULO=$Tienda;
if (isset($Caja))   $TITULO.=" - ".$Caja;
if (isset($PAGINA) && !empty($PAGINA)) $TITULO=$PAGINA;
if (isset($NEW_PAGINA)) $TITULO=$NEW_PAGINA;

$Lista_Iconos = array(
	"Home"  => array($DIR_MONITOR."/monitorizacion.php", "Pagina principal", $DIR_IMAGE."/user-home.png"),
	"Admon" => array($DIR_MONITOR."/Administracion/administracion.php", "Acceso a administracion del usuarios", $DIR_IMAGE."/preferences-contact-list.png"),
	"Ayuda" => array($DIR_AYUDA."/ayuda.php", "AYUDA", $DIR_IMAGE."/help-contents.png")
);

$Que_hace="Nada";

$Que_hace=$mypage;
if ($mypage == "conectar.php") {
	if (isset($host)) $Que_hace=$host."-".$port;
	else $Que_hace=$Tienda."-".$Caja;
	if (isset($myListados) && !empty($myListados)) $Que_hace.="-".$myListados;
	if (isset($myAcciones) && !empty($myAcciones)) $Que_hace.="-".$myAcciones;
	if (isset($myDatos) && !empty($myDatos)) $Que_hace.="-".$myDatos;
	$Temporizador=0;
}
if ($mypage == "pre_conectar.php") {
	if (isset($busca_ip)) $Que_hace="Busqueda por IP: $busca_ip";
	else	$Que_hace="Pre-con: $Tienda";
}
if ($mypage == "monitorizacion.php") $Que_hace=$TITULO;
if (preg_match("/^gestiona_/", $mypage)) { $TITULO="ADMINISTRACION"; $Temporizador=0; }

$Conexion="IF($Table.Conexion,'ON','OFF')";
$LastM="IF (DATE(LastM) < DATE(NOW()),CONCAT('<p style=\"font-size:8px\">',DATE(LastM),'<br>',TIME(LastM),'</p>'),TIME(LastM))";
$Exec="IF($Table.Exec,'OK','STOP')";
$WSD="'N/A'";
if ($Pais=="POR") $WSD="IF($Table.WSD,'OK','STOP')";
if ($Pais=="ARG") $WSD="IF($Table.WSD,'OK','STOP')";

$MySQL="IF($Table.MySQL,'OK','STOP')"; if ($Pais!="ESP") $MySQL="'N/A'";
$SWD="IF($Table.CAJA=1, IF($Table.SWD, 'OK','STOP'), 'N/A')";	// Sub_Campo(Sub_Campo("DAT3",-3,";"),-1,":");

$Pag_Actual=$mypage.'?'.$_SERVER['QUERY_STRING'];

$Boton_Atras = '</a><button class="button" type="button" onclick="javascript:window.back();" type="submit">Atras...</button>';

$VERSIONES="(select distinct(Version) from $Table where caja=1 and conexion order by 1)";
$tmp=MyQUERY("select distinct(Version) from $Table where caja=1 and conexion");
$T_Versiones=array(); if ($tmp) foreach($tmp as $k => $d) $T_Versiones[$k]=$d[0];

if (empty($_SESSION['Idioma'])) $_SESSION['Idioma']="Español";

if (!function_exists("Crea_Dir_Temporal")) {
	require_once($_SERVER['DOCUMENT_ROOT'].$DIR_TOOLS.'tools.php');
}
@Crea_Dir_Temporal( @$_SESSION["usuario"] , true );

Imprime_Trazas(SoyYo() && false);

?>