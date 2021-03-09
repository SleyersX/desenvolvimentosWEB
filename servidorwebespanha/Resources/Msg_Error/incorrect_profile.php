<?php
$Idioma=$_SESSION['Idioma'];
echo "<div class='Aviso Aviso_Rojo'><h2><center>";
switch ($Idioma) {
	case "ESP":
		echo "Lo sentimos, pero no tiene los permisos necesarios para acceder a esta opcion.";
		break;
	case "ENG":
		echo "Sorry, but you do not have permission to access this option.";
		break;
}
echo "</center></h2></div>";
?>