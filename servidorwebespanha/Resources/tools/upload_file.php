<?php

$ERRORES = array (
	"0" => "No hay error, archivo subido con éxito."
	,"1" => "El archivo subido excede en el tamaño maximo permitido (max. 105MB)"
	,"2" => "El archivo subido excede la directiva MAX_FILE_SIZE que fue especificada en el formulario HTML."
	,"3" => "El archivo subido fue sólo parcialmente cargado."
	,"4" => "Ningún archivo fue subido."
	,"6" => "Falta la carpeta temporal."
	,"7" => "No se pudo escribir el archivo en el disco."
	,"8" => "Una extensión de PHP detuvo la carga de archivos."
);

if (empty($_FILES)) {
	echo("<b style='color:red'>No existe fichero a subir o su tamaño excede del permitido</b><br>");
}
else  {
	if ($_FILES['archivo']['error'] > 0)
	{
		echo "<b style='color:red'>Error: " . $ERRORES[$_FILES['archivo']['error']] . "</b><br>";
	}
	else
	{
// 		echo "Nombre: " . $_FILES['archivo']['name'] . "<br>";
// 		echo "Tipo: " . $_FILES['archivo']['type'] . "<br>";
// 		echo "Tamaño: " . ($_FILES["archivo"]["size"] / 1024) . " kB<br>";
// 		echo "Carpeta temporal: " . $_FILES['archivo']['tmp_name'];
		move_uploaded_file($_FILES['archivo']['tmp_name'],$_POST['Path']."/".$_FILES['archivo']['name']);
		echo '<b style="color:blue">Fichero subido con exito!!</b><br>';
	}
}
?>
