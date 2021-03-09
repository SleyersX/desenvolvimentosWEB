<?php
	echo '<script language="JavaScript" src="'.$DIR_LIBRERIAS.'/google/gviz-api.js?v='.filemtime($DOCUMENT_ROOT.$DIR_LIBRERIAS.'/google/gviz-api.js').'"></script>';
	echo '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
	echo '
	<script>
		google.charts.load(
			"current",
			{"packages":[
				"corechart",
				"table",
				"timeline",
				"calendar",
				"controls"
			],
			"language": "es"
			}
		);
	</script>
	';
?>
