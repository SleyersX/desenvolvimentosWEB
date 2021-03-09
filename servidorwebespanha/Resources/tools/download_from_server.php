<?php
$file = $_GET['file'];

if(!$file){ // file does not exist
	die('file not found');
} else {
// 	echo "<big>Descargando fichero $file...</big>";
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".basename($file));
// 	header("Content-Type: application/zip");
	header("Content-Transfer-Encoding: binary");
	// read the file from disk
	readfile($_SERVER["DOCUMENT_ROOT"].$file);
}
?>