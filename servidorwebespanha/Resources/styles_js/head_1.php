<?php

$Ficheros_css=array(
	$DIR_RAIZ.'/css/miestilo.css'
	,$DIR_RAIZ.'/css/monitorizacion_estilos.css'
	,$DIR_RAIZ.'/css/fijos.css'
	,$DIR_RAIZ.'/css/tabla2.css'
//	,$DIR_RAIZ.'/fonts/font-awesome-4.7.0/css/font-awesome.css'
	,$DIR_RAIZ.'/fonts/fontawesome-free-5.0.8/web-fonts-with-css/css/fontawesome-all.css'
//	,$DIR_RAIZ.'/css/w3.css',
	,$DIR_RAIZ.'/css/jquery.modal.min.css'
);

//	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
// <meta http-equiv="Cache-control" content="no-cache">
	
//	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
echo '
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8_spanish_ci" />
	<link href="/img/icono_'.$PAIS_SERVER.'.png" rel="shortcut icon" type="image/x-icon"/>'.PHP_EOL;

if (!isset($NO_CSS))
	foreach($Ficheros_css as $k => $d)
		echo '	<link rel="stylesheet" type="text/css" href="'.$d.'?v='.filemtime($DOCUMENT_ROOT.$d).'" />'.PHP_EOL;

require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/library/jquery.php');
require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/library/google_charts.php');
require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/library/sweetalert.php');
require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/library/jqGrid.php');
require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/library/HSR.php');
require_once($DOCUMENT_ROOT.$DIR_RAIZ.'/library/clueTip.php');

echo '<script>
		var DIR_RAIZ="'.$DIR_RAIZ.'";
		</script>'.PHP_EOL;
echo '</head>';
?>
