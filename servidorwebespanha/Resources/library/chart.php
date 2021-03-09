<?php
	$PATH_TO=$DIR_RAIZ."/library/chart/Chart.js-2.7.2";
	echo '<script language="JavaScript" src="'.$PATH_TO.'/dist/Chart.js'.'?v='.filemtime($DOCUMENT_ROOT.$PATH_TO.'/dist/Chart.js').'"></script>'.PHP_EOL;
?>