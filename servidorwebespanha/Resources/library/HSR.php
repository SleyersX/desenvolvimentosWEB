<?php
$VERSION_HSR="1.09";

if (empty($DIR_RAIZ))
	require_once("/home/soporteweb/config.php");
	
if (empty($DIR_LIBRERIAS)) $DIR_LIBRERIAS=$DIR_RAIZ."/library";

$Ficheros_JS=array(
	$DIR_LIBRERIAS.'/HSR/'.$VERSION_HSR.'/scripts.js',
	$DIR_LIBRERIAS.'/HSR/'.$VERSION_HSR.'/tools_monitorizacion.js',
);

foreach($Ficheros_JS  as $k => $d) echo '<script language="JavaScript" src="'.$d.'?v='.filemtime($DOCUMENT_ROOT.$d).'"></script>';

?>
