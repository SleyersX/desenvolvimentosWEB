<?php
$D_R=$_SERVER['DOCUMENT_ROOT'];
$FILE=$_GET['FILE'];
$file_base=basename($FILE);
$D_T=dirname($FILE);

$TITULO="DOWNLOAD"; $Sin_CSS=true;
require_once("$D_R/Resources/styles_js/head_1.php");
require_once("$D_R/Resources/styles_js/tools.php");
echo '<style>'; require_once("$D_R/Resources/styles_js/monitorizacion_estilos.css");echo '</style>';

$name_backup="$D_T/backup.tgz";
$name_md5sum="$D_T/md5sum.ori";

copy($D_R."/".$FILE, $D_R."/".$name_backup);
file_put_contents($D_R."/".$name_md5sum, md5_file($D_R."/".$name_backup));

$File_BBDD=$DIR_CONEXION_TIENDA."download_from_server.php?file=$name_backup";
$File_MD5SUM=$DIR_CONEXION_TIENDA."download_from_server.php?file=$name_md5sum";

echo Alert("info",'
	<h1>Grabar base de datos a dispositivo USB</h1>
	<h2>Pasos para realizar el proceso:</h2>
	<ol>
		<li>Comprobar que el dispositivo tiene espacio suficiente.</li>
		<li>Pulsar <a href="'.$File_MD5SUM.'">aqu&iacute;</a> para grabar el fichero MD5SUM.ORI y aseg&uacute;rese de grabarlo en el directorio ra&iacute;z del dispositivo USB</li>
		<li>Pulsar <a href="'.$File_BBDD.'">aqu&iacute;</a> para grabar el fichero que contiene la BBDD de la TPV y aseg&uacute;rese de grabarlo en el directorio ra&iacute;z del dispositivo USB</li>
		<li>Una vez transferidos los dos ficheros, desconectar el USB del PC apropiadamente.</li>
		<li>Utilizar el USB para descargar la BBDD a una TPV usando el BAT25, opci&oacute;n 4.</li>
	</ol>
	</p>
	<p><i>NOTA: si tiene alg&uacute;n problema en los puntos 2 y 3, por favor p&oacute;ngase en contacto con el administrador de la herramienta de soporte.</i></p>');

?>