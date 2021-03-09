<?php
$PATH_TO_JQGRID=$DIR_RAIZ."/library/jqGrid";

if (!empty($_SESSION['usuario'])) {
	if (preg_match("/VMA001ES/",strtoupper($_SESSION['usuario']))) {
		$PATH_TO_JQGRID=$DIR_RAIZ."/library/jqGrid/jqGrid-4.8.0";
	}
}

echo '<link rel="stylesheet" type="text/css" href="'.$PATH_TO_JQGRID."/css/ui.jqgrid.css".'?v='.filemtime($DOCUMENT_ROOT.$PATH_TO_JQGRID."/css/ui.jqgrid.css").'" />'.PHP_EOL;
echo '<script language="JavaScript" src="'.$PATH_TO_JQGRID.'/js/i18n/grid.locale-es.js'.'?v='.filemtime($DOCUMENT_ROOT.$PATH_TO_JQGRID.'/js/i18n/grid.locale-es.js').'"></script>'.PHP_EOL;
echo '<script language="JavaScript" src="'.$PATH_TO_JQGRID.'/js/jquery.jqGrid.min.js'.'?v='.filemtime($DOCUMENT_ROOT.$PATH_TO_JQGRID.'/js/jquery.jqGrid.min.js').'"></script>'.PHP_EOL;

?>