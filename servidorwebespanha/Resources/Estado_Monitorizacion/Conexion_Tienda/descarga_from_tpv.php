<?php
$Cabecera=false;
require_once($_SERVER['DOCUMENT_ROOT']."/Resources/styles_js/comun.php");

$host=myQUERY("select ip from tiendas where numerotienda=$Tienda AND pais='$Pais'")[0][0];
$con_tda=new SFTPConnection($host, 10000+$Caja);

$con_tda->descargar_desde_tpv($remote_file, $local_file, $control);

exit;
?>