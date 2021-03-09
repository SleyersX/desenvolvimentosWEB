<?php
$VERSION_JQUERY="jquery/3.1.1";

if (empty($DIR_RAIZ))
	require_once("/home/soporteweb/config.php");
	
if (empty($DIR_LIBRERIAS)) $DIR_LIBRERIAS=$DIR_RAIZ."/library";

$Ficheros_JS=array(
	$DIR_LIBRERIAS.'/'.$VERSION_JQUERY.'/jquery.js',
	$DIR_LIBRERIAS.'/'.$VERSION_JQUERY.'/jquery-ui.js',
	$DIR_LIBRERIAS.'/'.$VERSION_JQUERY.'/jquery.qtip-1.0.0-rc3.min.js',
	$DIR_LIBRERIAS.'/'.$VERSION_JQUERY.'/jquery-ajax-progress.js',
	$DIR_LIBRERIAS.'/'.$VERSION_JQUERY.'/jquery.modal.min.js',
	$DIR_LIBRERIAS.'/'.$VERSION_JQUERY.'/grid.locale-en.js'
);

foreach($Ficheros_JS  as $k => $d) echo '<script language="JavaScript" src="'.$d.'?v='.filemtime($DOCUMENT_ROOT.$d).'"></script>';

?>
