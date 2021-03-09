<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/Resources/library/ssh2.php');

isset($_GET['Host']) or die ("<big>ERROR: No hay definida IP para conexion...");
isset($_GET['Port']) or die ("<big>ERROR: No hay definido Puerto para conexion...");
isset($_GET['Archivo']) or die ("<big>ERROR: No hay definida Fichero para Descargar...");

$con_tda=new SFTPConnection($_GET['Host'], $_GET['Port']);

$path = $con_tda->getFileLink($_GET['Archivo']);
$archivo=basename($_GET['Archivo']);

if (is_file($path)) {
	$size = filesize($path); 
	if (function_exists('mime_content_type')) { 
		$type = mime_content_type($path); 
	} else if (function_exists('finfo_file')) { 
		$info = finfo_open(FILEINFO_MIME); 
		$type = finfo_file($info, $path); 
		finfo_close($info); 
	} 
	if ($type == '') { 
		$type = "application/force-download"; 
	} 
	// Set Headers 
	header("Content-Type: $type"); 
	header("Content-Disposition: attachment; filename=".$archivo); 
	header("Content-Transfer-Encoding: binary"); 
	header("Content-Length: " . $size); 
	// Download File 
	readfile($path);
} else { 
	die("El archivo $path no existe"); 
} 
?>
<script>javascript:window.close();</script>
